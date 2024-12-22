<?php


namespace App\Services;


use App\Models\Group;
use App\Models\UserGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\GroupRepositoryInterface;
use App\Repositories\UserGroupRepositoryInterface;

class GroupService
{
    private  $userRepository;
    private  $groupRepository;
    private  $userGroupRepository;

     public function __construct(UserRepositoryInterface $userRepository,
     GroupRepositoryInterface $groupRepository,UserGroupRepositoryInterface $userGroupRepository){
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->userGroupRepository = $userGroupRepository;
     }

    public function create($request){
        $group= [
            'name'=> $request['name'],
            'description'=>$request['description'],
            'NumOfUser'=>1,
            'creation_date'=> $request['group-date'] ?? Carbon::today(),
            'user_id'=> Auth::id(),

        ];
        $group= $this->groupRepository->create($group);
        $user_group= [
            'user_id'=> Auth::id(),
            'group_id'=> $group->id,
            'joined_date'=> Carbon::today(),

        ];
        $user_group= $this->userGroupRepository->create($user_group);
     
           $user = $this->userRepository->find(Auth::id());
            $adminOfGroupRole= Role::query()->where('name','adminOfGroup')->first();;

            if(!$user->hasRole('adminOfGroup')){

                $user->assignRole($adminOfGroupRole);
                $permissions=$adminOfGroupRole->permissions()->plucK('name')->toArray();
                $user->givePermissionTo($permissions);
    
                $message="group creat successfully and user granted admin of group permission";
    
            return ["group"=>$group,"message"=>$message];}
    
            else{
                $message="group creat successfully";
    
                return ["group"=>$group,"message"=>$message];
           }
    
      

   }

   public function update($request,$group_id){

    $group = $this->groupRepository->find($group_id);
    
    if (is_null($group)) {
        return ["group" => null, "message" => "Group not found."];
    }

   
    if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id)
     || Auth::user()->hasRole('admin')) {
        return $this->processUpdate($request, $group_id);
    }

   
    $userGroup = $this->userGroupRepository->findByUserAndGroup($group_id, Auth::id());
    
    if (!is_null($userGroup)) {
        return $this->processUpdate($request, $group_id);
    } else {
        return ["group" => null, "message" => "You cannot update this group."];
    }
}

private function processUpdate($request, $group_id) {
    
    $existingGroup = $this->groupRepository->groupByNameAndDescription($group_id,$request['name'], 
    $request['description']);
    
    if (is_null($existingGroup)) {
        
        $updatedData = [
            'name' => $request['name'],
            'description' => $request['description']
        ];
        $updatedGroup = $this->groupRepository->update($group_id, $updatedData);

        return ["group" => $updatedGroup, "message" => "Group updated successfully."];
    } else {
        return ["group" => null, "message" => "You cannot update this group; it already exists."];
    }
}
   public function delete($group_id){

    $group = $this->groupRepository->find($group_id);
    
    if (is_null($group)) {
        return ["group" => null, "message" => "Group not found."];
    }

    if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id) 
    || Auth::user()->hasRole('admin')) {

        $group= $this->groupRepository->delete($group_id);

        return ["group" => $group, "message" => "Group deleted successfully."];
    }
    return ["group" => $group, "message" => "you can not delete this group"];
   }

   public function index(){

    if(Auth::user()->hasRole('admin')){
        $group = $this->groupRepository->all();
        
    }
  //  elseif(Auth::user()->hasRole('adminOfGroup')){
     //   $group =$this->groupRepository->groupByUserId(Auth::id());

   // }
    else{
        $group = $this->userRepository->groupForUser(Auth::id());
    }
    if($group->isEmpty()){
        return ["group" => $group, "message" => "there are no group"];
    }
     else{
        return ["group" => $group, "message" => "Group indexed successfully"];
     }
   }

  

}