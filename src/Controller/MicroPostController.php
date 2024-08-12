<?php

namespace App\Controller;

use App\Entity\MicroPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class
MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $entityManager->getRepository(MicroPost::class)->findAll(),
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show')]
    public function showOne($id, EntityManagerInterface $entityManager): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $entityManager->getRepository(MicroPost::class)->find($id),
        ]);

    }

    #[Route('/micro-post/{id}/add', name: 'app_micro_post_add', priority: 2)]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $microPost = new MicroPost();
        $form = $this->createFormBuilder($microPost)
            ->add('title')
            ->add('text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new \DateTime());

            // Persist the entity to the database
            $entityManager->persist($post);
            $entityManager->flush(); // Executes the queries to save the entity

            $this->addFlash('Success', 'Your micro post have been added!');

            return $this->redirectToRoute('app_micro_post_index');

        }

        return $this->render('micro_post/add.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $microPost = new MicroPost();
        $form = $this->createFormBuilder($microPost)
            ->add('title')
            ->add('text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new \DateTime());

            // Persist the entity to the database
            $entityManager->persist($post);
            $entityManager->flush(); // Executes the queries to save the entity

            $this->addFlash('Success', 'Your micro post have been updated!');

            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/edit.html.twig', ['form' => $form]);
    }
}
