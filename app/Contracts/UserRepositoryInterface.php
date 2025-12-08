<?php

namespace App\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findByEmail(string $email): ? User;
    public function update(User $user,  array $data):bool;
    public function softDelete(User $user): bool;
}