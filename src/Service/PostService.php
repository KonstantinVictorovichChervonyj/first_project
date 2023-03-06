<?php

namespace App\Service;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class PostService
{
    public function __construct(protected PostRepository $postRepository, protected UserRepository $userRepository)
    {

    }

    public function getPosts(): array
    {
//        dd($this->postRepository->getPosts());
        return $this->postRepository->getPosts();
    }

    public function createPost($postData): array
    {
//        $data = $postData->toArray(); not working

        $post = new Post();
        $post->setTitle($postData['title']);
        $post->setDescription($postData['description']);
        $post->setCreatedAt(new \DateTime());
        $post->setUpdatedAt(new \DateTime());
        $post->setUser($this->userRepository->find(1));

        $this->postRepository->save($post, true);

        return $postData;
    }

}