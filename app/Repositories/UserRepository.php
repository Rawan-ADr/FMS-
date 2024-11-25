<?php
namespace App\Repositories;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        $user = $this->find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function groupForNormalUser($id){

        $group = User::where('id', $id)->with(['group' =>
        function ($query) use ($id) { 
            $query->wherePivot('user_id', $id);
        }
    ])->get();

    return $group;
    }
}