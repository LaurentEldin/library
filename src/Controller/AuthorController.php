<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{

    /*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------             AFFICHER TOUT LES AUTHORS            -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
    */

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
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                AFFICHER UN AUTEUR                -----------------------------------
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
     * ---------------------------------                  AUTHOR BY NAME                  -----------------------------------
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

    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                CREATE AUTHOR                     -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * On crée une nouvelle route pour créer un nouvel author.
     * @Route("authors/create_author", name="create_author")
     */

    //Je crée une méthode pour ajouter un author à ma BDD à l'aide en EntityManager qui sert à Gérer les Entités.
    // J'utilise le Request pour récup les données passé dans le GET ou le POST, àa servira pour créer un form.
    public function showNewAuthor(EntityManagerInterface $entityManager, Request $request)
    {
        //Je crée une nouvelle instance de ma classe book en utilisant NEW.
        $author = new Author();

        //"request" pour recup une méthode POST ... "query" pour recup une méthode GET
        $name= $request->request->get('name');
        $firstname= $request->request->get('firstname');
        $birthDate= $request->request->get('birthDate');
        $deathDate= $request->request->get('deathDate');
        $biography= $request->request->get('biography');

        //Je passe à ma variable les setteurs de ma classe book pour en créer un nouveau.
        $author->setName($name);
        $author->setFirstname($firstname);
        $author->setBirthDate(new \DateTime($birthDate));
        $author->setDeathDate(new \DateTime($deathDate));
        $author->setBiography($biography);

        //J'uilise le persiste pour stocker temporairement mon instance (comme Make:Migration)
        $entityManager->persist($author);

        //Et maintenant le flush, pour CONFIRMER l'envoie des données dans l'entité. (comme Migration:Migrate).
        $entityManager->flush();
        // Les deux vont tout le temps ensemble.

        //Penser à bien fermer la méthode par une réponse (vardump / dump) pour tester si cela fonctionne.
        return $this->render('author.html.twig', [
            'author' => $author,
            'message' => "Merci, votre auteur a bien était enregistré"]);
    }

    /*
        ----------------------------------------------------------------------------------------------------------------------
        ---------------------------------                   FORM AUTHOR                    -----------------------------------
        ----------------------------------------------------------------------------------------------------------------------
        */

    /**
     * je crée une nouvelle route pour acceder à mon formulaire de création d'un auteur.
     * @Route("books/form_author",name="form_author")
     */
    // Je crée une méthode pour afficher le formulaire dans la page twig que je souhaite.
    public function createNewAuthor()
    {
        return $this->render('ajout_auteur.html.twig');
    }


/*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                   REMOVE AUTHOR                  -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
 */

    /**
     *
     * @Route("authors/remove_author/{id}",name="remove_author")
     */
    public function removeAuthor(AuthorRepository $authorRepository, EntityManagerInterface $entityManager, $id)
    {
        $author = $authorRepository->find($id);

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('authors');

    }
    /*
        ----------------------------------------------------------------------------------------------------------------------
        ---------------------------------                   MODIFIER AUTHOR                -----------------------------------
        ----------------------------------------------------------------------------------------------------------------------
     */

    /**
     *
     * @Route("/books/modif_author", name="modif_author")
     */

    public function showModifAuthor()
    {
        return
    }

    public function modifAuthor(EntityManagerInterface $entityManager, Request $request)
    {
        $author = new Author();

        $name= $request->request->get('name');
        $firstname= $request->request->get('firstname');
        $birthDate= $request->request->get('birthDate');
        $deathDate= $request->request->get('deathDate');
        $biography= $request->request->get('biography');

        $author->setName($name);
        $author->setFirstname($firstname);
        $author->setBirthDate(new \DateTime($birthDate));
        $author->setDeathDate(new \DateTime($deathDate));
        $author->setBiography($biography);

        $entityManager->persist($author);

        $entityManager->flush();

        return $this->render('author.html.twig', [
            'author' => $author,
            'message' => "Merci, votre auteur a bien était mis à jour"]);
    }
}
