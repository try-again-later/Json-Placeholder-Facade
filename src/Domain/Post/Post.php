<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use TryAgainLater\JsonPlaceholderFacade\Domain\User\User;

class Post
{
    private ?int $id;
    private User $author;
    private string $title;
    private string $body;

    public function __construct(
        User   $author,
        string $title,
        string $body,
        ?int   $id = null
    )
    {
        $this->author = $author;
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

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
