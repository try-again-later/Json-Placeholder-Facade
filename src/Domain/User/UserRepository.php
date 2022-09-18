<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    public function findWithId(int $id): User;

    public function findWithUsername(string $username): User;

    public function findWithEmail(string $email): User;
}
