<?php declare(strict_types=1);

use App\AppContainer;

/**
 * Action to create the database
 *
 * Run using: sls invoke -f create-schema and it will drop the table and recreate
 */
function main(array $args) : array
{
    $container = new AppContainer($args);
    $pdo = $container[PDO::class];

    $pdo->exec('DROP TABLE IF EXISTS todos');

    $pdo->exec(<<<'SQL'
CREATE TABLE todos (
    id serial PRIMARY KEY,
    title varchar(100),
    completed boolean NOT NULL DEFAULT false,
    "order" int NOT NULL DEFAULT 1
);
SQL
    );


    return [
        'done' => true,
    ];
}
