<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickMedia;
use App\Form\TrickCommentFormType;
use App\Form\TrickMediaFormType;
use App\Form\TrickStoreFormType;
use App\Repository\TrickCommentRepository;
use App\Repository\TrickMediaRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/tricks/create', name: 'app_trick_create')]
    public function create(Request $request, TrickRepository $trickRepository, EntityManagerInterface $m)
    {
        $form = $this->createForm(TrickStoreFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $trickRepository->generateSlug($form->get('name')->getData());
            $suffix = 1;

            while($trickRepository->findOneBy(['slug' => $slug])) {
                $slug = $trickRepository->generateSlug($form->get('name')->getData()) . '-' . $suffix;
            }

            $trick = $form->getData();
            $trick->setUser($this->getUser());
            $trick->setSlug($slug);
            $trick->setFeatured(false);
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $m->persist($trick);
            $m->flush();

            $this->addFlash('success', 'Trick created successfully');

            return $this->redirectToRoute('app_trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

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
            ], 200, [], [
                'groups' => ['trick', 'trick:user', 'trick:medias', 'media:read']
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
            ], 200, [], [
                'groups' => ['trick', 'trick:comment', 'user:read']
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
    public function edit(Request $request, Trick $trick, TrickMediaRepository $trickMediaRepository, TrickRepository $trickRepository): Response
    {
        $formMedia = $this->createForm(TrickMediaFormType::class);
        $formMedia->handleRequest($request);

        if ($formMedia->isSubmitted() && $formMedia->isValid()) {
            $media = $formMedia->getData();
            $media->setTrick($trick);

            if ($media->getType() === 'image') {
                $file = $formMedia->get('file')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $media->setContent($fileName);
            } else {
                $media->setContent($formMedia->get('url')->getData());
            }

            $trickMediaRepository->save($media, true);

            $this->addFlash('success', 'Media added successfully');

            return $this->redirectToRoute('app_trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        }

        $formUpdate = $this->createForm(TrickStoreFormType::class, $trick);
        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $trick = $formUpdate->getData();
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trickRepository->save($trick, true);

            $this->addFlash('success', 'Trick updated successfully');

            return $this->redirectToRoute('app_trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'formMedia' => $formMedia->createView(),
            'formUpdate' => $formUpdate->createView()
        ]);
    }

    #[Route('/tricks/{slug}/media/{media}/delete', name: 'app_trick_media_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function destroyMedia(Request $request, Trick $trick, TrickMedia $media,  TrickMediaRepository $trickMediaRepository, EntityManagerInterface $m): Response
    {
        $media = $trickMediaRepository->findOneBy([
            'id' => $media->getId(),
            'trick' => $trick->getId()
        ]);

        if ($media && $this->isCsrfTokenValid('delete-' . $media->getId(), $request->request->get('_token'))) {
            $m->remove($media);
            $m->flush();

            $this->addFlash('success', 'Media deleted successfully');
        } else {
            $this->addFlash('error', 'Media not found');
        }

        return $this->redirectToRoute('app_trick_edit', [
            'slug' => $trick->getSlug(),
        ]);
    }

    #[Route('/tricks/{slug}/delete', name: 'app_trick_delete', methods: ['POST'])]
    public function destroy(Request $request, Trick $trick, TrickRepository $trickRepository, EntityManagerInterface $m): Response
    {
        if ($this->isCsrfTokenValid('delete-' . $trick->getId(), $request->request->get('_token'))) {
            $m->remove($trick);
            $m->flush();
        }

        return $this->redirectToRoute('home');
    }
}
