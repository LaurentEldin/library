<?php

namespace App\Controller\admin\authors;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
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
     * @Route("admin/authors", name="admin_authors")
     */

    // Je crée une fonction pour afficher tout mes auteurs.
    public function allAuthors(AuthorRepository $authorRepository)
    {
        //Je nomme une variable authors dans laquelle je demande d'avoir "All" de mon répertoire.
        $authors = $authorRepository->findAll();

        //Je demande d'afficher ce résultat sur la page "authors.html.twig" en instanciant ma variable $authors par 'authors'
        return $this->render('admin/authors/authors.html.twig', ['authors' => $authors]);

    }


    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                AFFICHER UN AUTEUR                -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */
    /**
     * Je crée ma route pour l'auteur seul grace à son id.
     * @Route("admin/author/{id}", name="admin_author")
     */

    //Je crée un fonction pour monter UN auteur en piochant dans le Répo Author et en indiquant qu'il faudra le $id et que ça sera un integer.
    public function showAuthor(AuthorRepository $authorRepository, int $id)
    {
        //Je crée une variable auteur en lui demandant d'aller simplement récup un élément dans le Répo Author. Que je défini comme étant l'id.
        $author = $authorRepository->findOneBy(['id' => $id]);
        dump($author);
        //Je demande à me retourner le resultat sur la page 'author'.
        return $this->render('admin/author/author.html.twig', ['author' => $author]);
    }
    /*
     * ----------------------------------------------------------------------------------------------------------------------
     * ---------------------------------                  AUTHOR BY NAME                  -----------------------------------
     * ----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Je crée une route pour avoir mes auteurs par nom
     * @Route("admin/authors_by_name",name="admin_authors_by_name")
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
        return $this->render('admin/authors/authors.html.twig', [
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
     * @Route("admin/authors/create_author", name="admin_create_author")
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
        return $this->render('admin/author/author.html.twig', [
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
     * @Route("admin/books/form_author",name="admin_form_author")
     */
    // Je crée une méthode pour afficher le formulaire dans la page twig que je souhaite.
    public function createNewAuthor()
    {
        return $this->render('admin/author/ajout_auteur.html.twig');
    }


/*
    ----------------------------------------------------------------------------------------------------------------------
    ---------------------------------                   REMOVE AUTHOR                  -----------------------------------
    ----------------------------------------------------------------------------------------------------------------------
 */

    /**
     *  Route vers l'url pour supprimer un auteur.
     * @Route("admin/authors/remove_author/{id}",name="admin_remove_author")
     */

    //Methode pour supprimer l'auteur à l'aide de l'entityManager.
    public function removeAuthor(AuthorRepository $authorRepository, EntityManagerInterface $entityManager, $id)
    {
        //Je vais chercher une entité book via son id et je stock dans une variable.
        $author = $authorRepository->find($id);

        // Je supprime la variable via la fonction remove de l'EM.
        $entityManager->remove($author);

        //Ensuite j'utilise flush pour rendre effectif la suppression dans ma BDD
        $entityManager->flush();

        // Redirect to route car il n'y a pas de twig exprès pour afficher la suppression.
        return $this->redirectToRoute('amdin_authors');
    }
    /*
        ----------------------------------------------------------------------------------------------------------------------
        ---------------------------------                   MODIFIER AUTHOR                -----------------------------------
        ----------------------------------------------------------------------------------------------------------------------
     */


    /**
     * @Route("admin/authors/update_author",name="admin_update_author")
     */

    public function updateAuthor (EntityManagerInterface $entityManager, Request $request)
    {

        $author = new Author();
        $message = null;

        $authorForm = $this->createForm(AuthorType::class, $author);

        if ($request->isMethod('Post')) {

            $authorForm->handleRequest($request);

            if ($authorForm->isValid()) {

                $message = "Merci, votre enregistrement à bien était pris en compte.";
                $entityManager->persist($author);
                $entityManager->flush();
            }
        }

        $authorFormView = $authorForm->createView();

        return $this->render('admin/author/update_author.html.twig', [
            'authorFormView' => $authorFormView,
            'message' => $message
        ]);
    }

    /**
     * @Route("admin/authors/update_form/{id}", name="admin_authors_update_form")
     */

    public function updateAuthorForm(AuthorRepository $authorRepository, EntityManagerInterface $entityManager, Request $request, $id)
    {
        $message = null;
        $author = $authorRepository->find($id);
        $authorForm = $this->createForm(AuthorType::class, $author);
        if ($request->isMethod('Post'))
        {
            $authorForm->handleRequest($request);
            if ($authorForm->isValid()) {
                $message = "Modification validée.";
                $entityManager->persist($author);
                $entityManager->flush();
            }
        }

        $authorFormView = $authorForm->createView();

        return $this->render('admin/author/update_author.html.twig', [
            'authorFormView' => $authorFormView,
            'message' => $message
        ]);
    }
}
