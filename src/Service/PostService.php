<?php

namespace App\Service;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpFoundation\Request;

class PostService
{
    public function __construct(protected PostRepository $postRepository, protected UserRepository $userRepository)
    {

    }

    public function getPosts($hydrateMod = AbstractQuery::HYDRATE_OBJECT): array
    {
        return $this->postRepository->getPosts($hydrateMod);
    }

    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->find($id);
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

        $this->savePost($post, true);

        return $postData;
    }
    public function makeNewPost(): Post
    {
        return (new Post())->setUser($this->userRepository->find(1));
    }
    public function savePost(Post $post, $persist = false): void
    {
        $this->postRepository->save($post, $persist);
    }

    public function deletePost($id): void
    {
        $post = $this->postRepository->find($id);

        $this->postRepository->remove($post, true);
    }
}