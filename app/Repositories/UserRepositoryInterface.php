<?php

namespace App\Repositories;

use App\Models\Api\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{    
    /**
     * getByEmail
     *
     * @param  string $email
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getByEmail(string $email): \Illuminate\Database\Eloquent\Model|null;
}
