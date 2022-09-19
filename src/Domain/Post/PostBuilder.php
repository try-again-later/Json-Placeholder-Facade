<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use RuntimeException;
use TryAgainLater\JsonPlaceholderFacade\Domain\User\{User, UserNotFoundException, UserRepository};

class PostBuilder
{
    private ?int $id = null;
    private ?User $author = null;
    private ?int $authorId = null;
    private ?string $title = null;
    private ?string $body = null;

    private ?UserRepository $userRepository = null;

    public function withId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function withAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;
        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function eagerLoadAuthor(UserRepository $userRepository): self
    {
        $this->userRepository = $userRepository;
        return $this;
    }

    /**
     * @throws RuntimeException
     * @throws UserNotFoundException
     */
    public function build(): Post
    {
        if ($this->title === null || $this->body === null) {
            throw new RuntimeException('Failed to build a post: one of the required fields is missing.');
        }
        if ($this->authorId === null && $this->author === null) {
            throw new RuntimeException('At least user model or user ID is required to build a post.');
        }
        if ($this->authorId !== null && $this->author !== null && $this->authorId !== $this->author->getId()) {
            throw new RuntimeException('Author ID on the model and author ID provided as a separate value do not match.');
        }

        if ($this->userRepository !== null && $this->author === null) {
            $this->author = $this->userRepository->findWithId($this->authorId);
        }

        return new Post(
            $this->title,
            $this->body,
            $this->authorId ?? $this->author->getId(),
            $this->author,
            $this->id,
        );
    }
}
