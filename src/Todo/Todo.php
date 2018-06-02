<?php declare(strict_types=1);

namespace Todo;

use InvalidArgumentException;
use \strlen;

class Todo
{
    private $id;
    private $title;
    private $completed = false;
    private $order = 1;

    public function __construct(array $data)
    {
        if (array_key_exists('id', $data)) {
            $this->id = $this->validateId($data['id']);
        }
        if (array_key_exists('title', $data)) {
            $this->title = $this->validateTitle($data['title']);
        }
        if (array_key_exists('completed', $data)) {
            $this->completed = $this->validateCompleted($data['completed']);
        }
        if (array_key_exists('order', $data)) {
            $this->order = $this->validateOrder($data['order']);
        }
    }

    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'completed' => $this->completed,
            'order' => $this->order,
        ];
    }

    private function validateId(int $id)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("Id must be positive", 400);
        }

        return $id;
    }

    private function validateTitle(string $title)
    {
        if (strlen($title) === 0) {
            throw new InvalidArgumentException("Title is too short", 400);
        }
        if (strlen($title) > 100) {
            throw new InvalidArgumentException("Title is too long", 400);
        }

        return $title;
    }

    private function validateCompleted(bool $completed)
    {
        return $completed;
    }

    private function validateOrder(int $order)
    {
        if ($order <= 0) {
            throw new InvalidArgumentException("Order must be positive");
        }

        return $order;
    }
}
