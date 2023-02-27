<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Return the home page of the application.
     * @return Response
     */
    public function index(): Response
    {
        return  $this->render("home.html.twig");
    }
}