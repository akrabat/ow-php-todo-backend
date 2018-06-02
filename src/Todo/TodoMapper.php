<?php declare(strict_types=1);

namespace Todo;

use PDO;

class TodoMapper
{
    private $pdo;

    /**
     * TodoMapper constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetch all todos
     *
     * @return [Todo]
     */
    public function fetchAll() : array
    {
        $sql = 'SELECT * FROM todos ORDER BY "order" DESC';
        $statement = $this->pdo->query($sql);

        $todos = [];
        foreach ($statement as $row) {
            $todo = new Todo($row);
            $todos[] = $todo;
        }

        return $todos;
    }

    public function loadById(int $id) : Todo
    {
        $sql = 'SELECT * FROM todos WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception\TodoNotFoundException();
        }

        return new Todo($result);
    }

    public function insert(array $data) : Todo
    {
        $todo = new Todo($data);

        $params = $todo->getArrayCopy();

        $sql = 'INSERT INTO todos (title, completed, "order") VALUES (:title, :completed, :order)';
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam('title', $params['title']);
        $statement->bindParam('completed', $params['completed'], PDO::PARAM_BOOL);
        $statement->bindParam('order', $params['order']);
        $statement->execute();

        $id = (int)$this->pdo->lastInsertId('todos_id_seq');
        return $this->loadById($id);
    }

    public function deleteAll() : void
    {
        $sql = 'DELETE FROM todos';
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
    }

    public function delete(int $id) : void
    {
        $todo = $this->loadById($id);

        $sql = 'DELETE FROM todos WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
    }

    public function update(Todo $todo, array $data) : Todo
    {
        if (array_key_exists('id', $data)) {
            unset($data['id']);
        }

        $orginalData = $todo->getArrayCopy();
        $data = array_replace($orginalData, $data);

        $updatedTodo = new Todo($data);
        $params = $updatedTodo->getArrayCopy();

        $sql = 'UPDATE todos SET
                title = :title,
                completed = :completed,
                "order" = :order
            WHERE id = :id';

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam('title', $params['title']);
        $statement->bindParam('completed', $params['completed'], PDO::PARAM_BOOL);
        $statement->bindParam('order', $params['order']);
        $statement->bindParam('id', $params['id']);
        $statement->execute();

        return $updatedTodo;
    }
}
