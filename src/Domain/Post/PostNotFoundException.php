<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Domain\Post;

use Throwable;
use TryAgainLater\JsonPlaceholderFacade\Domain\DomainRecordNotFoundException;

class PostNotFoundException extends DomainRecordNotFoundException
{
    public function __construct(Post $post, $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "Post with ID '{$post->getId()}' was not found.",
            $code,
            $previous
        );
    }
}
