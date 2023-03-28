<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickCommentFormType;
use App\Repository\TrickCommentRepository;
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
    #[Route('/tricks/{slug}/comments', name: 'app_trick_comment')]
    public function comments(Trick $trick, TrickCommentRepository $trickCommentRepository, Request $request): Response
    {
        $data = $trickCommentRepository->findPaginated(
            $trick,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );

        $total = $trickCommentRepository->count([
            "trick" => $trick->getId()
        ]);

        return $this->json(
            [
                "data" => $data,
                "meta" => [
                    "page" => $request->query->getInt('page', 1),
                    "limit" => $request->query->getInt('limit', 3),
                    "total" => $total,
                    "pages" => ceil($total / $request->query->getInt('limit', 3)),
                ],
            ]
        );
    }

    #[Route('/tricks/{slug}', name: 'app_trick_show')]
    public function show(Request $request, Trick $trick, TrickCommentRepository $trickCommentRepository): Response
    {
        $formComment = $this->createForm(TrickCommentFormType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment = $formComment->getData();
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $trickCommentRepository->save($comment, true);

            $this->addFlash('success', 'Comment added successfully');

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'formComment' => $formComment->createView()
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
