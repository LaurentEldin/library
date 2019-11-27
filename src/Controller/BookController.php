<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\crypto_box_publickey_from_secretkey;

class BookController extends AbstractController
{
    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------             AFFICHER ALL BOOK                    -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Je crée une route pour la page qui affichera tout mes livres.
     * @Route("/books", name="books")
     */

    // Je crée une fonction pour afficher tout mes livres en allant chercher dans le Répo BOOK.
    public function allBook(BookRepository $bookRepository)
    {
        // Je crée une variable $book dans laquelle je lui demand d'aller chercher tout les éléments du Répo Book.
        $books = $bookRepository->findAll();

        //Je l'affiche ensuite dans la page 'books' en instanciant $books par books.
        return $this->render('books.html.twig', ['books' => $books]);
    }

    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                AFFICHER ONE BOOK                 -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Je crée la route pour avoir mon livre seul grâce à son ID.
     * @Route("/book/{id}", name="book")
     */

    //Je crée une fonction showbook qui vas allé récup des infos dans le Répo Book et particulièrement l'id en précisant que ça sera un integer.
    public function showBook(BookRepository $bookRepository, int $id)
    {

        //Je crée une variable book dans laquelle je lui demande d'aller ciblé un élément (les ID) dans le répo Book.
        $book = $bookRepository->findOneBy(['id' => $id]);

        //Je demande de me retourner le résultat sur la page book.
        return $this->render('book.html.twig', ['book' => $book]);
    }


    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                   GETBOOK BY                     -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
    */
    /**
     * Je crée une route pour avoir mes livres triés par style.
     * @Route("/books_by_style",name="books_by_style")
     */

    //Je crée une fonction qui appel le RépoBOOk en le passant dans la variable.
    public function showBookByStyle(BookRepository $bookRepository, Request $request)
    {
        // Je crée une requette pour aller recup le style dans l'url grace à Query.
        $style = $request->query->get('style');
        $title = $request->query->get('title');
        $inStock = $request->query->get('inStock');

        // Appel la méthode créer dans le répo qui doit nous retourner tout les livres trier par $style.
        $books = $bookRepository->getByStyle($style, $title, $inStock);

        //Nous retourne la réponse dans la page 'books' avec une wildcard.
        return $this->render('books.html.twig', [
            'books' => $books,
            'title' => $title,
            'inStock' => $inStock
        ]);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 CREATE BOOK                      -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * On crée une nouvelle route pour créer un nouveau livre.
     * @Route("books/create_book", name="create_book")
     */

    //Je crée une méthode pour ajouter un livre à ma BDD à l'aide en EntityManager qui sert à Gérer les Entités.
    // J'utilise le Request pour récup les données passé dans le GET ou le POST, àa servira pour créer un form.
    public function showNewBook(EntityManagerInterface $entityManager, Request $request)
    {
        //Je crée une nouvelle instance de ma classe book en utilisant NEW.
        $book = new Book();

        //"request" pour recup une méthode POST ... "query" pour recup une méthode GET
        $title = $request->request->get('title');
        $style = $request->request->get('style');
        $inStock = $request->request->get('inStock');
        $nbPages = $request->request->get('nbPages');

        //Je passe à ma variable les setteurs de ma classe book pour en créer un nouveau.
        $book->setTitle($title);
        $book->setStyle($style);
        $book->setInStock($inStock);
        $book->setNbPages($nbPages);

        //J'uilise le persiste pour stocker temporairement mon instance (comme Make:Migration)
        $entityManager->persist($book);

        //Et maintenant le flush, pour CONFIRMER l'envoie des données dans l'entité. (comme Migration:Migrate).
        $entityManager->flush();
        // Les deux vont tout le temps ensemble.

        //Penser à bien fermer la méthode par une réponse (vardump / dump) pour tester si cela fonctionne.
        return $this->render('book.html.twig', [
            'book' => $book,
            'message' => "Merci, votre livre a bien était enregistré"]);
    }

    /*
        ----------------------------------------------------------------------------------------------------------------------
        ---------------------------------                    FORM BOOK                     -----------------------------------
        ----------------------------------------------------------------------------------------------------------------------
        */

    /**
     * @Route("books/form_book",name="form_book")
     */
    public function createNewBook()
    {
        return $this->render('ajout_book.html.twig');
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 REMOVE BOOK                      -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * @Route("books/remove_book/{id}",name="remove_book")
     */
    public function removeBook (BookRepository $bookRepository, EntityManagerInterface $entityManager, $id)
    {
        $book = $bookRepository->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('books');
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 MODIF BOOK                      -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     *
     * @Route("books/modif_book",name="modif_book")
     */

    public function showModifBook()
    {

    }
}