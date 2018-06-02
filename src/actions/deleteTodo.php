<?php declare(strict_types=1);

use App\AppContainer;
use Todo\TodoMapper;

/**
 * DELETE /todos/[{id}]
 */
function main(array $args) : array
{
    try {
        $parts = explode("/", $args['__ow_path']);
        $id = (int)array_pop($parts);

        $container = new AppContainer($args);
        $mapper = $container[TodoMapper::class];

        $todo = $mapper->delete($id);

        return [
            'statusCode' => 204,
        ];
    } catch (\Exception $e) {
        var_dump((string)$e);
        $code = $e->getCode() < 400 ? $e->getCode(): 500;
        return [
            'statusCode' => $code,
            'body' => ['error' => $e->getMessage()]];
    }
}
