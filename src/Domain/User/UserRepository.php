<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @throws UserNotFoundException
     */
    public function findWithId(int $id): User;

    /**
     * @throws UserNotFoundException
     */
    public function findWithUsername(string $username): User;

    /**
     * @throws UserNotFoundException
     */
    public function findWithEmail(string $email): User;
}
