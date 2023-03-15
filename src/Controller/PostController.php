<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\PostService;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/create', name: 'add_post', methods: ['GET', 'POST'])]
    public function create(PostService $postService, Request $request): Response
    {
        $addPost = $this->createForm(
            PostType::class,
            $postService->makeNewPost(),
        );

        $addPost->handleRequest($request);
        if ($addPost->isSubmitted() && $addPost->isValid()) {

            $post = $addPost->getData();

            $postService->savePost($post, true);

            return $this->redirectToRoute('list_posts',  [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('standart/post/add_post.html.twig', [
            'form_post' => $addPost->createView(),
        ]);
    }

    #[Route('/posts', name: 'app_posts_api', methods: ['GET'])]
    public function posts(PostService $postService): JsonResponse
    {
        return $this->json($postService->getPosts(AbstractQuery::HYDRATE_ARRAY));
    }

    #[Route('/add_post', name: 'add_post_api', methods: ['POST'])]
    public function addPost(PostService $postService, Request $request): JsonResponse
    {
        return $this->json($postService->createPost($request));
    }

    #[Route('/', name: 'list_posts')]
    public function list(PostService $postService): Response
    {
        return $this->render('standart/post/list.html.twig', [
            'posts' => $postService->getPosts(),
        ]);
    }

    #[Route('/posts/{id}', name: 'single_post')]
    public function single(PostService $postService, Request $request): Response
    {
        $post = $postService->getPostById( (int) $request->get('id'));

        if (null === $post) {
            return $this->redirectToRoute('not_found', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('standart/post/single.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/posts/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostService $postService): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postService->savePost($post, true);

            return $this->redirectToRoute('list_posts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('standart/post/edit_post.html.twig', [
            'post' => $post,
            'form_post' => $form,
        ]);
    }

    #[Route('/posts/{id}/delete', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostService $postService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postService->deletePost($post->getId());
        }

        return $this->redirectToRoute('list_posts', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/404', name: 'not_found')]
    public function notFound(): Response
    {
        return $this->render('standart/404.html.twig');
    }
}
