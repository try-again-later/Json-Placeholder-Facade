<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\User;

use Throwable;
use TryAgainLater\JsonPlaceholderFacade\Domain\DomainRecordNotFoundException;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function byId(int $id): self
    {
        return new self("User with ID '{$id}' was not found.");
    }

    public static function byUsername(string $username): self
    {
        return new self("User with username '{$username}' was not found.");
    }

    public static function byEmail(string $email): self
    {
        return new self("User with email '{$email}' was not found.");
    }
}
