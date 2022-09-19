<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use TryAgainLater\JsonPlaceholderFacade\Domain\User\{User, UserRepository, UserNotFoundException};

class Post
{
    private ?int $id;
    private ?User $author;
    private int $authorId;
    private string $title;
    private string $body;

    public function __construct(
        string $title,
        string $body,
        int    $authorId,
        ?User  $author = null,
        ?int   $id = null
    )
    {
        $this->author = $author;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->body = $body;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @throws UserNotFoundException
     */
    public function getAuthor(?UserRepository $userRepository = null): ?User
    {
        if ($this->author === null && $userRepository === null) {
            return null;
        }
        if ($this->author === null) {
            $this->author = $userRepository->findWithId($this->authorId);
        }
        return $this->author;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public static function builder(): PostBuilder
    {
        return new PostBuilder();
    }
}
