<?php declare(strict_types=1);

use App\AppContainer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Todo\TodoTransformer;
use Todo\TodoMapper;

/**
 * POST /todos
 */
function main(array $args) : array
{
    try {
        $data = json_decode(base64_decode($args['__ow_body']), true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Missing body', 400);
        }

        $container = new AppContainer($args);
        $mapper = $container[TodoMapper::class];

        $todo = $mapper->insert($data);

        $transformer = $container[TodoTransformer::class];
        $resource = new Item($todo, $transformer, 'todos');
        $fractal = $container[Manager::class];

        return [
            'statusCode' => 200,
            'body' => $fractal->createData($resource)->toArray(),
        ];
    } catch (\Throwable $e) {
        var_dump((string)$e);
        $code = $e->getCode() < 400 ? $e->getCode(): 500;
        return [
            'statusCode' => $code,
            'body' => ['error' => $e->getMessage()]];
    }
}
