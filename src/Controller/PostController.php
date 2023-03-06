<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CreatePostType;
use App\Repository\PostRepository;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_render')]
    public function index(PostRepository $postRepository, PostService $postService, Request $request): Response
    {
        $addPost = $this->createForm(CreatePostType::class);

        if ($request->isMethod('POST')) {

            $addPost->handleRequest($request);

            $post = $addPost->getData();

            $postService->createPost($post);

//            if ($addPost->isSubmitted() && $addPost->isValid()) {
//                return $this->redirectToRoute('add_post');
//            }
        }

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $postRepository->findAll(),
            'form_add_post' => $addPost->createView(),
        ]);
    }

    #[Route('/posts', name: 'app_posts', methods: ['GET'])]
    public function posts(PostService $postService): JsonResponse
    {
        return $this->json($postService->getPosts());
    }

    #[Route('/add_post', name: 'add_post', methods: ['POST'])]
    public function addPost(PostService $postService, Request $request): JsonResponse
    {
        return $this->json($postService->createPost($request));
    }
}
