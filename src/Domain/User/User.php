<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\User;

class User
{
    private ?int $id;
    private string $name;
    private string $username;
    private string $email;

    public function __construct(
        string $name,
        string $username,
        string $email,
        ?int   $id = null
    )
    {
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
