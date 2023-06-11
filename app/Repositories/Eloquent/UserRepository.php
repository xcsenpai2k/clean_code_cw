<?php 

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface 
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    
    /**
     * getByEmail
     *
     * @param  string $email
     * @return Illuminate\Database\Eloquent\Model|null
     */
    public function getByEmail(string $email): \Illuminate\Database\Eloquent\Model|null
    {
        return $this->eloquentBuilder()
                ->where('email', $email)
                ->first();
    }
}