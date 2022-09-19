<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use TryAgainLater\JsonPlaceholderFacade\Domain\User\User;

interface PostRepository
{
    /**
     * @return Post[]
     */
    public function findAllByAuthor(User $author): array;

    /**
     * @throws PostNotFoundException
     */
    public function findWithId(int $id): Post;

    public function create(Post $post): void;

    /**
     * @throws PostNotFoundException
     */
    public function update(Post $post): void;

    /**
     * @throws PostNotFoundException
     */
    public function delete(Post $post): void;
}
