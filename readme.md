# Пример использования

Этот же пример есть отдельно в `example/example.php`, запустить его можно через `composer run example`.

```php
$guzzleClient = new GuzzleClient([
    'base_uri' => 'https://jsonplaceholder.typicode.com/',
]);

$userRepository = new RestUserRepository($guzzleClient);
// найти всех пользователей
$users = $userRepository->findAll();
var_dump(array_slice($users, 0, 3));

// найти пользователя по email (если не найден, выбросится UserNotFoundException)
$email = 'Shanna@melissa.tv';
$user = $userRepository->findWithEmail($email);
var_dump($user);

// найти все задачи, которые создал пользователь
$todoRepository = new RestTodoRepository($guzzleClient);
$todos = $todoRepository->findAllByUser($user);
var_dump(array_slice($todos, 0, 3));

// найти пост по ID, автоматически загрузить модель пользователя, который оставил пост
$postRepository = RestPostRepository::withAuthor($guzzleClient, $userRepository);
$post = $postRepository->findWithId(2);
$author = $post->getAuthor();
var_dump($post, $author);

// найти все остальные посты от этого пользователя
$postsByAuthor = $postRepository->findAllByAuthorId($post->getAuthorId());
var_dump(array_slice($postsByAuthor, 0, 3));

// создать новый пост
$newPost = Post::builder()
    ->withAuthor($author)
    ->withBody('Body...')
    ->withTitle('Title')
    ->build();
$postRepository->create($newPost);
// ID созданного поста будет автоматически обновлён в модели
var_dump('ID нового поста', $newPost->getId());

// обновить пост (в случае неудачи выбросится исключение)
$post->setTitle('Some other title');
$postRepository->update($post);

// удалить пост (в случае неудачи выбросится исключение)
$postRepository->delete($newPost);

// репозиторий с постами без автоматической подгрузки моделей авторов постов
$postRepositoryWithoutEagerLoading = new RestPostRepository($guzzleClient);
$postWithoutEagerLoading = $postRepositoryWithoutEagerLoading->findWithId(3);
// будет доступен ID автора
var_dump('ID автора', $postWithoutEagerLoading->getAuthorId());
// но сама модель загружена не будет (вернёт null)
var_dump('Модель автора', $postWithoutEagerLoading->getAuthor());
```
