<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/tricks', name: 'app_trick')]
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $tricks = $trickRepository->findPaginated(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );

        return $this->json(
            [
                "data" => $tricks,
                "meta" => [
                    "page" => $request->query->getInt('page', 1),
                    "limit" => $request->query->getInt('limit', 3),
                    "total" => $trickRepository->count([]),
                    "pages" => ceil($trickRepository->count([]) / $request->query->getInt('limit', 3)),
                ],
            ]
        );
    }
}
