<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
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
---------------------------------             METHODE UNE (MARCHE PAS)             -----------------------------------
----------------------------------------------------------------------------------------------------------------------
*/
//    /**
//     * Je crée ensuite une route pour afficher un livre via son ID.
//     * @Route("/book/{id}", name="book")
//     */
//
//    // Je reprend ma fonction Show pour les authors et je l'adaptes pour mes Livres.
//    public function show(int $id)
//    {
//        // Je crée une variable book et j'indique le chemin pour alle récup les id dans l'entité Book.
//        $book = $this->getDoctrine()
//            ->getRepository(Book::class)
//            ->find($id);
//
//        //Je fais ma sécurité pour pas que l'on me donne un ID qui n'existe pas.
//        if (!$book) {
//            throw $this->createNotFoundException(
//                "Désolé, il n'existe aucune référence pour l'id numéro : " . $id
//            );
//        }
//
//        //Je demande qu'on me retourne le resultat sur la page 'book' en passant tout les param de l'entité Book.
//        return $this->render('book.html.twig', ['book' =>
//            [$book->getId(),
//            $book->getTitle(),
//            $book->getnbPages(),
//            $book->getStyle(),
//            $book->getInStock()
//            ]]);
//    }

/*
----------------------------------------------------------------------------------------------------------------------
---------------------------------                   METHODE DEUX                   -----------------------------------
----------------------------------------------------------------------------------------------------------------------
    */
    /**
     * Je crée la route pour avoir mon livre seul grâce à son ID.
     * @Route("/book/{id}", name="book")
     */

    //Je crée une fonction showbook qui vas allé récup des infos dans le Répo Book et particulièrement l'id en précisant que ça sera un integer.
    public function showBook(BookRepository $bookRepository,int $id) {

        //Je crée une variable book dans laquelle je lui demande d'aller ciblé un élément (les ID) dans le répo Book.
        $book = $bookRepository->findOneBy(['id'=>$id]);

        //Je demande de me retourner le résultat sur la page book.
        return $this->render('book.html.twig', ['book'=>$book]);
    }
}
