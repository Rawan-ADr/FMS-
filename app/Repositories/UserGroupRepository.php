<?php
namespace App\Repositories;
use App\Models\UserGroup;

class UserGroupRepository implements UserGroupRepositoryInterface
{
    
    public function create(array $data)
    {
        return UserGroup::create($data);
    }

   
    public function findByUserAndGroup($id1,$id2){

        return UserGroup::where('group_id',$id1)
        ->where('user_id',$id2)
        ->first();
        

    }

      public function findByFileAndGroup($id1,$id2){

        return UserGroup::where('group_id',$id1)
        ->where('id',$id2)
        ->first();
        

    }

     public function find($id)
    {
        return UserGroup::find($id);
    }
       
   

    
}