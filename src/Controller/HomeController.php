<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Return the home page of the application.
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findOneBy(['featured' => true]);

        if (!$trick) {
            // Get random trick if no featured trick
            $trick = $trickRepository->findRandom();
        }

        return  $this->render("home.html.twig", compact('trick'));
    }
}