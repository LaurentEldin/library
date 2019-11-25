<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * Je crée une route pour mon index.
     * @Route("/", name="index")
     */

    //Je crée une fonction pour afficher mon index.
    public function index()
    {
        //Je lui demande de retourner mon html/css dans cette page.
        return $this->render('index.html.twig');
    }
}