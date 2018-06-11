<?php declare(strict_types=1);

namespace Todo;

use League\Fractal\TransformerAbstract;

class TodoTransformer extends TransformerAbstract
{
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function transform(Todo $todo) : array
    {
        $data = $todo->getArrayCopy();

        return [
            'id' => $data['id'],
            'title' => $data['title'],
            'completed' => $data['completed'],
            'order' => $data['order'],
            'url' => $this->baseUrl . '/' . $data['id'],
        ];
    }
}
