<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

interface PostRepository
{
    /**
     * @return Post[]
     */
    public function findAllByAuthorId(int $authorId): array;

    /**
     * @throws PostNotFoundException
     */
    public function findWithId(int $id): Post;

    public function create(Post $post): Post;

    /**
     * @throws PostNotFoundException
     */
    public function update(Post $post): Post;

    /**
     * @throws PostNotFoundException
     */
    public function delete(Post $post): void;
}
