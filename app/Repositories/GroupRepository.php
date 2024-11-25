<?php
namespace App\Repositories;
use App\Models\Group;

class GroupRepository implements GroupRepositoryInterface
{
    public function all()
    {
        return Group::all();
    }

    public function find($id)
    {
        return Group::find($id);
    }

    public function create(array $data)
    {
        return Group::create($data);
    }

    public function update($id, array $data)
    {
        $group = Group::find($id);
        if ($group) {
            $group->update($data);
            return $group;
        }
        return null;
    }

    public function delete($id)
    {
        $group = Group::find($id);
        if ($group) {
            $group->delete();
            return true;
        }
        return false;
    }

    public function groupByUserId($id){
        $group =Group::where('user_id',$id)->get();
        return $group;
    }

    
}