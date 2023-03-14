<?php

namespace App\Controller;

use App\Entity\Trick;
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

    #[Route('/tricks/{slug}', name: 'app_trick_show')]
    public function show(Trick $trick): Response
    {
        // dd($trick);
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/tricks/{slug}/edit', name: 'app_trick_edit')]
    public function edit(Trick $trick): Response
    {
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
        ]);
    }
}
