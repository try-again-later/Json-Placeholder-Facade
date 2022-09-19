<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Persistence\Rest;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use TryAgainLater\JsonPlaceholderFacade\Domain\Todo\{Todo, TodoRepository};
use TryAgainLater\JsonPlaceholderFacade\Domain\User\User;

class RestTodoRepository implements TodoRepository
{
    private const ID_KEY = 'id';
    private const USER_ID_KEY = 'userId';
    private const TITLE_KEY = 'title';
    private const COMPLETED_KEY = 'completed';

    private const REQUIRED_ARRAY_KEYS = [
        self::ID_KEY,
        self::USER_ID_KEY,
        self::TITLE_KEY,
        self::COMPLETED_KEY,
    ];

    private GuzzleClientInterface $guzzleClient;

    public function __construct(GuzzleClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return Todo[]
     * @throws GuzzleException
     */
    public function findAllByUser(User $user): array
    {
        if ($user->getId() === null) {
            throw new InvalidArgumentException('Cannot fetch todos for a user entity without the id.');
        }

        $response = $this->guzzleClient->request(
            'GET',
            'todos',
            ['query' => [self::USER_ID_KEY => $user->getId()]]
        );
        $todosData = json_decode($response->getBody()->getContents(), true) ?? [];
        $todos = [];
        foreach ($todosData as $todoData) {
            $todos[] = self::parseTodoFromArray($todoData, $user);
        }

        return $todos;
    }

    /**
     * @throws InvalidArgumentException in case the array does not have any of the required keys.
     */
    public static function parseTodoFromArray(array $todoData, User $user): Todo
    {
        foreach (self::REQUIRED_ARRAY_KEYS as $requiredKey) {
            if (!isset($todoData[$requiredKey])) {
                throw new InvalidArgumentException("Failed to parse todo from array. Key '{$requiredKey}' is missing.");
            }
        }

        if (intval($todoData[self::USER_ID_KEY]) !== $user->getId()) {
            throw new InvalidArgumentException('User IDs from todo data and user model do not match.');
        }

        return new Todo(
            $user,
            $todoData[self::TITLE_KEY],
            $todoData[self::COMPLETED_KEY],
            $todoData[self::ID_KEY],
        );
    }
}
