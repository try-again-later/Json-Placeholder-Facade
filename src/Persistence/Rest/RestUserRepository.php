<?php

declare(strict_types=1);

namespace TryAgainLater\JsonPlaceholderFacade\Persistence\Rest;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\{ClientException, GuzzleException};
use InvalidArgumentException;
use TryAgainLater\JsonPlaceholderFacade\Domain\User\{User, UserNotFoundException, UserRepository};

class RestUserRepository implements UserRepository
{
    private const ID_KEY = 'id';
    private const NAME_KEY = 'name';
    private const USERNAME_KEY = 'username';
    private const EMAIL_KEY = 'email';

    private const REQUIRED_ARRAY_KEYS = [
        self::ID_KEY,
        self::NAME_KEY,
        self::USERNAME_KEY,
        self::EMAIL_KEY,
    ];

    private GuzzleClientInterface $guzzleClient;

    public function __construct(GuzzleClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return User[]
     * @throws GuzzleException
     */
    public function findAll(): array
    {
        $response = $this->guzzleClient->request('GET', 'users');
        $usersData = json_decode($response->getBody()->getContents(), true) ?? [];
        $users = [];

        foreach ($usersData as $userData) {
            $users[] = self::parseUserFromArray($userData);
        }

        return $users;
    }

    /**
     * @throws UserNotFoundException
     * @throws GuzzleException
     */
    public function findWithId(int $id): User
    {
        try {
            $response = $this->guzzleClient->request('GET', "users/{$id}");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw UserNotFoundException::byId($id);
            }
            throw $e;
        }

        $userData = json_decode($response->getBody()->getContents(), true) ?? [];
        return self::parseUserFromArray($userData);
    }

    /**
     * @throws UserNotFoundException
     * @throws GuzzleException
     */
    public function findWithUsername(string $username): User
    {
        $response = $this->guzzleClient->request(
            'GET',
            'users',
            ['query' => [self::USERNAME_KEY => $username]]
        );
        $usersData = json_decode($response->getBody()->getContents(), true) ?? [];
        if (count($usersData) !== 1)  {
            throw UserNotFoundException::byUsername($username);
        }
        $userData = $usersData[0];
        return self::parseUserFromArray($userData);
    }

    /**
     * @throws UserNotFoundException
     * @throws GuzzleException
     */
    public function findWithEmail(string $email): User
    {
        $response = $this->guzzleClient->request(
            'GET',
            'users',
            ['query' => [self::EMAIL_KEY => $email]]
        );
        $usersData = json_decode($response->getBody()->getContents(), true) ?? [];
        if (count($usersData) !== 1)  {
            throw UserNotFoundException::byEmail($email);
        }
        $userData = $usersData[0];
        return self::parseUserFromArray($userData);
    }

    /**
     * @throws InvalidArgumentException in case the array does not have any of the required keys.
     */
    public static function parseUserFromArray(array $userData): User
    {
        foreach (self::REQUIRED_ARRAY_KEYS as $requiredKey) {
            if (!isset($userData[$requiredKey])) {
                throw new InvalidArgumentException("Failed to parse user from array. Key '{$requiredKey}' is missing.");
            }
        }

        return new User(
            $userData[self::NAME_KEY],
            $userData[self::USERNAME_KEY],
            $userData[self::EMAIL_KEY],
            $userData[self::ID_KEY],
        );
    }
}
