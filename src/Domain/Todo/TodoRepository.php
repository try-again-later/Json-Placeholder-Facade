<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Todo;

use TryAgainLater\JsonPlaceholderFacade\Domain\User\User;

interface TodoRepository
{
    /**
     * @return Todo[]
     */
    public function findAllByUser(User $user): array;
}
