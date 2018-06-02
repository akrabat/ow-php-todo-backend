<?php

use App\AppContainer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Todo\TodoMapper;
use Todo\TodoTransformer;

/**
 * GET /todos/{id}
 */
function main(array $args) : array
{
    try {
        $parts = explode("/", $args['__ow_path']);
        $id = (int)array_pop($parts);

        $container = new AppContainer($args);
        $mapper = $container[TodoMapper::class];

        $todo = $mapper->loadById($id);

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
