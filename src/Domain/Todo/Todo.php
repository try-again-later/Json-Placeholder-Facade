<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Todo;

use TryAgainLater\JsonPlaceholderFacade\Domain\User\User;

class Todo
{
    private ?int $id;
    private User $user;
    private string $title;
    private bool $completed;

    public function __construct(
        User   $user,
        string $title,
        bool   $completed,
        ?int   $id = null
    )
    {
        $this->user = $user;
        $this->title = $title;
        $this->completed = $completed;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }
}
