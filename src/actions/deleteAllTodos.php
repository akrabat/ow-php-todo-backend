<?php declare(strict_types=1);

use App\AppContainer;
use Todo\TodoMapper;

/**
 * DELETE /todos
 */
function main(array $args) : array
{
    try {
        $container = new AppContainer($args);
        $mapper = $container[TodoMapper::class];

        $todo = $mapper->deleteAll();

        return [
            'statusCode' => 204,
        ];
    } catch (\Throwable $e) {
        var_dump((string)$e);
        $code = $e->getCode() < 400 ? $e->getCode(): 500;
        return [
            'statusCode' => $code,
            'body' => ['error' => $e->getMessage()]];
    }
}
