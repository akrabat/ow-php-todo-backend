<?php declare(strict_types=1);

use App\AppContainer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Todo\TodoMapper;
use Todo\TodoTransformer;

/**
 * GET /todos
 */
function main(array $args) : array
{
    try {
        $container = new App\AppContainer($args);
        $mapper = $container[TodoMapper::class];

        $todos = $mapper->fetchAll();

        $transformer = $container[TodoTransformer::class];
        $resource = new Collection($todos, $transformer);
        $fractal = $container[Manager::class];

        return [
            'statusCode' => 200,
            'body' => $fractal->createData($resource)->toArray()['data'],
        ];
    } catch (\Throwable $e) {
        var_dump((string)$e);
        $code = $e->getCode() < 400 ? $e->getCode(): 500;
        return [
            'statusCode' => $code,
            'body' => ['error' => $e->getMessage()]];
    }
}
