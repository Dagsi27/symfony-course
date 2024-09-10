<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class
MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post_index')]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'id')]
    public function showOne($id, EntityManagerInterface $entityManager): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $entityManager->getRepository(MicroPost::class)->find($id),
        ]);

    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new \DateTime());
            $post->setAuthor($this->getUser());
            // Persist the entity to the database
            $entityManager->persist($post);
            $entityManager->flush(); // Executes the queries to save the entity

            $this->addFlash('Success', 'Your micro post have been added!');

            return $this->redirectToRoute('app_micro_post_index');

        }

        return $this->render('micro_post/add.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            // Persist the entity to the database
            $entityManager->persist($post);
            $entityManager->flush(); // Executes the queries to save the entity

            $this->addFlash('Success', 'Your micro post have been updated!');

            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/edit.html.twig', ['form' => $form, 'post' => $post]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment', priority: 2)]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setMicroPost($post);
            $comment->setAuthor($this->getUser());
            // Persist the entity to the database
            $entityManager->persist($comment);
            $entityManager->flush(); // Executes the queries to save the entity

            $this->addFlash('Success', 'Your comment have been added!');

            return $this->redirectToRoute('app_micro_post_show', ['id' => $post->getId()]);

        }

        return $this->render('micro_post/comment.html.twig', ['form' => $form, 'post' => $post]);
    }
}
