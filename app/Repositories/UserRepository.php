<?php
namespace App\Repositories;
use App\Models\User;
use App\Models\UserGroup;

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

    public function groupForUser($id){

        $groups = User::find($id)->group()->get();

        return $groups;
       
      
  
    }
            public function updateFCM($id,$fcm)
    {
        $user=User::find($id);
        $user->fcm_token=$fcm;
        $user->save();
        return ;

    }

  /* public function groupForUser($id){

        $groups = User::find($id)->with('group')->get();

        return $groups;
       
      
  
   */// }

    public function usersNotInPivotTable($id){
        $user= User::whereNotIn('id', function($query) use ($id) {
            $query->select('user_id')->from('user_groups')->where('group_id', $id);
        })->get();

        return $user;

    }


      public function usersIngroup($id){
        $user= UserGroup::where('group_id',$id)->get();

        return $user;

    }
    public function searchForUserInGroup(string $name,$id){
        $user = User::where('name', $name)
            ->whereHas('group', function($query) use ($id) {
                $query->where('groups.id', $id);
            })
            ->first();
            return $user;
    }
}