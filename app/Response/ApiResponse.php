<?php

namespace App\Response;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    protected static ?self $instance = null;
    protected array $response = [];

    protected function __construct() {}

    protected static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function setMessage(string $message): self
    {
        $instance = self::getInstance();
        $instance->response['message'] = $message;
        return $instance;
    }

    public static function setData($data): self
    {
        $instance = self::getInstance();
        $instance->response['data'] = $data;
        return $instance;
    }

    public function mergeResults(array $results = []): self
    {
        $this->response = array_merge($this->response, $results);
        return $this;
    }

    public function response(int $statusCode = 200): JsonResponse
    {
        $response = response()->json($this->response, $statusCode);
        self::$instance = null;
        return $response;
    }
}
