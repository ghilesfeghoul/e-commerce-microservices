<?php
namespace App\Services;

class UserService extends ApiService
{
    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }
}