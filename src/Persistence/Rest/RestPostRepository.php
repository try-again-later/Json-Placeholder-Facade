<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Persistence\Rest;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use TryAgainLater\JsonPlaceholderFacade\Domain\Post\{Post, PostNotFoundException, PostRepository};
use RuntimeException;
use TryAgainLater\JsonPlaceholderFacade\Domain\User\{User, UserNotFoundException, UserRepository};

class RestPostRepository implements PostRepository
{
    private const ID_KEY = 'id';
    private const TITLE_KEY = 'title';
    private const BODY_KEY = 'body';
    private const AUTHOR_ID_KEY = 'userId';

    private const REQUIRED_ARRAY_KEYS = [
        self::ID_KEY,
        self::TITLE_KEY,
        self::BODY_KEY,
        self::AUTHOR_ID_KEY,
    ];

    private GuzzleClientInterface $guzzleClient;
    private ?UserRepository $userRepository = null;

    public function __construct(
        GuzzleClientInterface $guzzleClient,
        ?UserRepository $userRepository = null
    )
    {
        $this->guzzleClient = $guzzleClient;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Post[]
     * @throws GuzzleException
     */
    public function findAllByAuthor(User $author): array
    {
        $response = $this->guzzleClient->request(
            'GET',
            'posts',
            ['query' => [self::AUTHOR_ID_KEY => $author->getId()]]
        );
        $postsData = json_decode($response->getBody()->getContents(), true) ?? [];
        $posts = [];

        foreach ($postsData as $postData) {
            $posts[] = self::parsePostFromArray($postData, $author);
        }

        return $posts;
    }

    public function findWithId(int $id): Post
    {
        try {
            $response = $this->guzzleClient->request('GET', "posts/{$id}");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new PostNotFoundException($id);
            }
            throw $e;
        }

        $postData = json_decode($response->getBody()->getContents(), true) ?? [];
        return self::parsePostFromArray($postData);
    }

    public function create(Post $post): Post
    {
        $response = $this->guzzleClient->request('POST', 'posts', ['json' => $this->postToArray($post)]);
        $newPostData = json_decode($response->getBody()->getContents(), true) ?? [];
        if (!isset($newPostData[self::ID_KEY])) {
            throw new RuntimeException('Failed to parse new post ID from the server response.');
        }
        $post->setId(intval($newPostData[self::ID_KEY]));
        return $post;
    }

    public function update(Post $post): Post
    {
        if ($post->getId() === null) {
            throw new InvalidArgumentException('Cannot update a post without ID.');
        }

        $this->guzzleClient->request('PUT', "posts/{$post->getId()}", ['json' => $this->postToArray($post)]);
        return $post;
    }

    public function delete(Post $post): void
    {
        if ($post->getId() === null) {
            throw new InvalidArgumentException('Cannot delete a post without ID.');
        }

        $this->guzzleClient->request('DELETE', "posts/{$post->getId()}");
    }

    /**
     * @throws InvalidArgumentException in case the array does not have any of the required fields.
     * @throws UserNotFoundException
     */
    public function parsePostFromArray(array $postData, ?User $author = null): Post
    {
        foreach (self::REQUIRED_ARRAY_KEYS as $requiredKey) {
            if (!isset($postData[$requiredKey])) {
                throw new InvalidArgumentException("Failed to parse post from the array. A key '{$requiredKey}' is missing.");
            }
        }

        if ($author !== null && intval($postData[self::AUTHOR_ID_KEY]) !== $author->getId()) {
            throw new InvalidArgumentException('User IDs from post data and user model do not match.');
        }

        $postBuilder = Post::builder()
            ->withTitle($postData[self::TITLE_KEY])
            ->withBody($postData[self::BODY_KEY])
            ->withId($postData[self::ID_KEY])
            ->withAuthorId($postData[self::AUTHOR_ID_KEY]);

        if ($author !== null) {
            $postBuilder->withAuthor($author);
        } else if ($this->userRepository !== null) {
            $postBuilder->eagerLoadAuthor($this->userRepository);
        }

        return $postBuilder->build();
    }

    public function postToArray(Post $post): array
    {
        $array = [
            self::TITLE_KEY => $post->getTitle(),
            self::BODY_KEY => $post->getBody(),
            self::AUTHOR_ID_KEY => $post->getAuthorId(),
        ];
        if ($post->getId() !== null) {
            $array[self::ID_KEY] = $post->getId();
        }
        return $array;
    }

    public static function withAuthor(GuzzleClientInterface $guzzleClient, UserRepository $userRepository): self
    {
        return new self($guzzleClient, $userRepository);
    }
}
