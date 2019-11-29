<?php

namespace App\Controller\admin\books;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
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
     * Je cree une route pour la page qui affichera tout mes livres.
     * @Route("/admin/books", name="admin_books")
     */

    // Je crée une fonction pour afficher tout mes livres en allant chercher dans le Répo BOOK.
    public function allBook(BookRepository $bookRepository)
    {
        // Je crée une variable $book dans laquelle je lui demand d'aller chercher tout les éléments du Répo Book.
        $books = $bookRepository->findAll();

        //Je l'affiche ensuite dans la page 'books' en instanciant $books par books.
        return $this->render('admin/books/books.html.twig', ['books' => $books]);
    }

    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                AFFICHER ONE BOOK                 -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Je crée la route pour avoir mon livre seul grâce à son ID.
     * @Route("admin/book/{id}", name="admin_book")
     */

    //Je crée une fonction showbook qui vas allé récup des infos dans le Répo Book et particulièrement l'id en précisant que ça sera un integer.
    public function showBook(BookRepository $bookRepository, int $id)
    {

        //Je crée une variable book dans laquelle je lui demande d'aller ciblé un élément (les ID) dans le répo Book.
        $book = $bookRepository->findOneBy(['id' => $id]);

        //Je demande de me retourner le résultat sur la page book.
        return $this->render('admin/book/book.html.twig', ['book' => $book]);
    }


    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                   GETBOOK BY                     -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
    */
    /**
     * Je crée une route pour avoir mes livres triés par style.
     * @Route("admin/books_by_style",name="admin_books_by_style")
     */

    //Je crée une fonction qui appel le RépoBOOk en le passant dans la variable.
    public function showBookByStyle(BookRepository $bookRepository, Request $request)
    {
        // Je crée une requette pour aller recup le style dans l'url grace à Query.
        $style = $request->query->get('style');
        $title = $request->query->get('title');
        $inStock = $request->query->get('inStock');
        $author = $request->query->get('author');

        // Appel la méthode créer dans le répo qui doit nous retourner tout les livres trier par $style.
        $books = $bookRepository->getByStyle($style, $title, $inStock, $author);

        //Nous retourne la réponse dans la page 'books' avec une wildcard.
        return $this->render('admin/books/books.html.twig', [
            'books' => $books,
            'title' => $title,
            'inStock' => $inStock,
            'author' => $author
        ]);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 CREATE BOOK                      -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * On crée une nouvelle route pour créer un nouveau livre.
     * @Route("admin/books/create_book", name="admin_create_book")
     */

    //Je crée une méthode pour ajouter un livre à ma BDD à l'aide en EntityManager qui sert à Gérer les Entités.
    // J'utilise le Request pour récup les données passé dans le GET ou le POST, àa servira pour créer un form.
    public function showNewBook(EntityManagerInterface $entityManager, Request $request, AuthorRepository $authorRepository)
    {
        //Je crée une nouvelle instance de ma classe book en utilisant NEW.
        $book = new Book();
        $author = $authorRepository;

        //"request" pour recup une méthode POST ... "query" pour recup une méthode GET
        $title = $request->request->get('title');
        $style = $request->request->get('style');
        $inStock = $request->request->get('inStock');
        $nbPages = $request->request->get('nbPages');
        $author = $request->request->get('author');

        //Je passe à ma variable les setteurs de ma classe book pour en créer un nouveau.
        $book->setTitle($title);
        $book->setStyle($style);
        $book->setInStock($inStock);
        $book->setNbPages($nbPages);
        $book->setAuthor($author);

        //J'uilise le persiste pour stocker temporairement mon instance (comme Make:Migration)
        $entityManager->persist($book);

        //Et maintenant le flush, pour CONFIRMER l'envoie des données dans l'entité. (comme Migration:Migrate).
        $entityManager->flush();
        // Les deux vont tout le temps ensemble.

        //Penser à bien fermer la méthode par une réponse (vardump / dump) pour tester si cela fonctionne.
        return $this->render('admin/book/book.html.twig', [
            'book' => $book,
            'message' => "Merci, votre livre a bien était enregistré"]);
    }

    /*
        ----------------------------------------------------------------------------------------------------------------------
        ---------------------------------                    FORM BOOK                     -----------------------------------
        ----------------------------------------------------------------------------------------------------------------------
        */

    /**
     * @Route("admin/books/form_book",name="admin_form_book")
     */
    public function createNewBook()
    {
        return $this->render('admin/book/ajout_book.html.twig');
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 REMOVE BOOK                      -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * @Route("admin/books/remove_book/{id}",name="admin_remove_book")
     */
    public function removeBook (BookRepository $bookRepository, EntityManagerInterface $entityManager, $id)
    {
        $book = $bookRepository->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('admin_books');
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 MODIF BOOK 1                     -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * @Route("admin/books/update_book", name="admin_update_book")
     */
    public function insertBookForm(EntityManagerInterface $entityManager, Request $request)
    {

        $book = new Book();
        $message = null;

        $bookForm = $this->createForm(BookType::class, $book);

        // Si je suis sur une méthode POST
        // donc qu'un formulaire a été envoyé
        if ($request->isMethod('Post')) {

            // Je récupère les données de la requête (POST)
            // et je les associe à mon formulaire
            $bookForm->handleRequest($request);

            // Si les données de mon formulaire sont valides
            // (que les types rentrés dans les inputs sont bons,
            // que tous les champs obligatoires sont remplis etc)
            if ($bookForm->isValid()) {

                // J'enregistre en BDD ma variable $book
                // qui n'est plus vide, car elle a été remplie
                // avec les données du formulaire
                $message = "Merci, votre enregistrement à bien était pris en compte.";
                $entityManager->persist($book);
                $entityManager->flush();
            }
        }
        $bookFormView = $bookForm->createView();

        return $this->render('admin/book/update_book.html.twig', [
            'bookFormView' => $bookFormView,
            'message' => $message
        ]);
    }

    /**
    * @Route("admin/books/update_form/{id}", name="admin_books_update_form")
    */

    public function updateBookForm(BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request, $id)
    {
        $message = null;
        $book = $bookRepository->find($id);
        $bookForm = $this->createForm(BookType::class, $book);
        if ($request->isMethod('Post'))
        {
            $bookForm->handleRequest($request);
            if ($bookForm->isValid()) {
                $message = "Modification validée.";
                $entityManager->persist($book);
                $entityManager->flush();
            }
        }

        // à partir de mon gabarit, je cree la vue de mon formulaire
        $bookFormView = $bookForm->createView();
        // je retourne un fichier twig, et je lui envoie ma variable qui contient
        // mon formulaire
        return $this->render('admin/book/update_book.html.twig', [
            'bookFormView' => $bookFormView,
            'message' => $message
        ]);
    }


    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                 MODIF BOOK 2                     -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */
//    /**
//     * creation d'une nouvelle route pour pouvoir Editer un livre dans le formulaire.
//     * @Route("amdin/books/edit_modif_book/{id}",name="admin_edit_modif_book")
//     */
//    //Du coup on la nomme EditModifBook et on fait appel a notre répo et entity manager + le rappel de notre ID en wildcard
//    public function editModifBook(BookRepository $bookRepository, EntityManagerInterface $entityManager, $id)
//    {
//        //on stock tout dans la variable ID.
//        $book = $bookRepository->find($id);
//
//        //Je retourne tout sur ma vue du formulaire book.
//        return $this->render('ajout_book.html.twig', ['book' => $book]);
//    }
//
//    /**
//     * Nouvelle route pour modifier un livre.
//     * @Route("amdin/books/save_modif_book",name="admin_save_modif_book")
//     */
//
//    // nouvelle méthode pour modifier un livre. On appel le repo Book et EntityManager.
//    public function saveModifBook(BookRepository $bookRepository, EntityManagerInterface $entityManager,  Request $request)
//    {
//        //On stock le livre à l'id 20 dans la variable book.
//        $id= $request->request->get('id');
//        $book = $bookRepository->find($id);
//
//        $title = $request->request->get('title');
//        $style = $request->request->get('style');
//        $inStock = $request->request->get('inStock');
//        $nbPages = $request->request->get('nbPages');
//
//        //On modifie ses param avec les Setteurs.
//        $book->setTitle($title);
//        $book->setStyle($style);
//        $book->setInStock($inStock);
//        $book->setNbPages($nbPages);
//
//        //On utilise persist pour stocké temporairement la modif
//        $entityManager->persist($book);
//
//        //Flush pour rendre effectif la modif en BDD
//        $entityManager->flush();
//
//        return $this->redirectToRoute('admin_books');
//    }

}