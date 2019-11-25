<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * Je crée une route pour ma page avec tout les auteurs.
     * @Route("/authors", name="authors")
     */

    // Je crée une fonction pour afficher tout mes auteurs.
    public function allAuthors(AuthorRepository $authorRepository)
    {
        //Je nomme une variable authors dans laquelle je demande d'avoir "All" de mon répertoire.
        $authors = $authorRepository->findAll();

        //Je demande d'afficher ce résultat sur la page "authors.html.twig" en instanciant ma variable $authors par 'authors'
        return $this->render('authors.html.twig', ['authors' => $authors]);

    }

    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------             METHODE UNE   (MARCHE PAS)           -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
    */

//    /**
//     * Je crée une route pour ma page qui n'affichera qu'un seul auteur via son ID.
//     * @Route("/author/{id}", name="author")
//     */

//    // Je crée une fonction pour afficher une livre via son $id, et je précise que je n'attend qu'un integer en param.
//    public function show(int $id)
//    {
//        // J'indique le chemin pour aller récup les bons ID.
//        $author = $this->getDoctrine()
//            ->getRepository(Author::class)
//            ->find($id);
//
//        //J'installe une sécurité au cas ou l'on me demande un ID qui n'existe pas.
//        if (!$author) {
//            throw $this->createNotFoundException(
//                "Désolé, il n'existe aucune référence pour l'id numéro : " . $id
//            );
//        }
//
//        //Je retourne le resultat sur la page "author" en demandant tout les param de l'entité "Author".
//        return $this->render('author.html.twig', ['author' =>
//            [$author->getId(),
//                $author->getName(),
//                $author->getFirstname(),
//                $author->getBirthDate(),
//                $author->getDeathDate()
//            ]]);
//    }
    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                     METHODE DEUX                 -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */
    /**
     * Je crée ma route pour l'auteur seul grace à son id.
     * @Route("/author/{id}", name="author")
     */

    //Je crée un fonction pour monter UN auteur en piochant dans le Répo Author et en indiquant qu'il faudra le $id et que ça sera un integer.
    public function showAuthor(AuthorRepository $authorRepository, int $id)
    {
        //Je crée une variable auteur en lui demandant d'aller simplement récup un élément dans le Répo Author. Que je défini comme étant l'id.
        $author = $authorRepository->findOneBy(['id' => $id]);
        dump($author);
        //Je demande à me retourner le resultat sur la page 'author'.
        return $this->render('author.html.twig', ['author' => $author]);
    }
    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                AUTOR BY NAME                 -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Je crée une route pour avoir mes auteurs par nom
     * @Route("/authors_by_name",name="authors_by_name")
     */

    //Je crée une fonction qui appel le RépoBOOk en le passant dans la variable.
    public function showBookByStyle(AuthorRepository $authorRepository, Request $request)
    {
        // Je crée une requette pour aller recup des param dans l'url grace à Query.
        $name = $request->query->get('name');
        $firstname = $request->query->get('firstname');
        $biography = $request->query->get('biography');

        // Appel la méthode créer dans le répo qui doit nous retourner tout les auteurs par name etc...
        $authors = $authorRepository->getByName($name, $firstname, $biography);

        //Nous retourne la réponse dans la page 'books' avec une wildcard.
        return $this->render('authors.html.twig', [
            'authors' => $authors,
            'name' => $name,
            'firstname' => $firstname,
            'biography' => $biography
        ]);
    }

}
