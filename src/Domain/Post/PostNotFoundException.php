<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use Throwable;
use TryAgainLater\JsonPlaceholderFacade\Domain\DomainRecordNotFoundException;

class PostNotFoundException extends DomainRecordNotFoundException
{
    public function __construct(int $postId, $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "Post with ID '{$postId}' was not found.",
            $code,
            $previous
        );
    }
}
