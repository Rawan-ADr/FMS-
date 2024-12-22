<?php


namespace App\Services;


use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\GroupRepositoryInterface;
use App\Repositories\UserGroupRepositoryInterface;

class UserService
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
     

    public function register($request) {

        $user= [
           'name'=>$request['name'],
           'email'=>$request['email'],
           'password'=>Hash::make($request['password'])
        ];

        $user= $this->userRepository->create($user);
        $normal=Role::query()->where('name','normalUser')->first();
        $user->assignRole($normal);

        $permissions=$normal->permissions()->plucK('name')->toArray();
        $user->givePermissionTo($permissions);

        $user->load('roles','permissions');

        $user=$this->userRepository->find($user['id']);
        $user=$this->apendRoleAndPermission($user);

        $user['token']=$user->createToken("token")->plainTextToken;
        $message="user creat successfully";

        return ["user"=>$user,"message"=>$message];


    }

    public function login($request){
        $user = $this->userRepository->findByEmail($request['email']);
        if(!is_null($user)){
            if(!Auth::attempt($request->only(['email','password']))){
                $message=" email or password is wrong ";
                $code=401;
            }
            else{
                $user=$this->apendRoleAndPermission($user);
                $user['token']=$user->createToken("token")->plainTextToken;
                $message=" logged in successfully ";
                $code=200;
            }
        }
        else{
               $message=" user not found ";
               $code=404;
        }
        return ['user'=>$user,'message'=>$message,'code'=>$code];
    }

    public function logout(){
        $user=$this->userRepository->find(Auth::id());
        if(!is_null($user)){
            Auth::user()->currentAccessToken()->delete();
            $message=" logged out successfully ";
            $code=200;
        }
        else{
            $message=" invalid token ";
            $code=404;
        }
        return ['user'=>$user,'message'=>$message,'code'=>$code];
    }


    public function apendRoleAndPermission($user){
        $roles=[];
        foreach($user->roles as $role){
            $roles[]=$role->name;
        }
        unset($user['roles']);
        $user['roles']=$roles;

        $permissions=[];
        foreach($user->permissions as $permission){
            $permissions[]=$permission->name;
        }
        unset($user['permissions']);
        $user['permissions']=$permissions;

        return $user;
    }

    public function index(){
       $user = $this->userRepository->all();
       if(!is_null($user)){
            $message="user indexed successflly ";
            $code=200;
        }
        else{
            $message=" no body here ";
            $code=404;
        }
        return ['user'=>$user,'message'=>$message,'code'=>$code];
    }

    public function addUserToGroup($request){
        $group =$this->groupRepository->find($request['group_id']);
        $userGroup = null;
        if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id) ){
            
            $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($request['group_id'],$request['user_id']);

            if(is_null($userGroup)){
            $userGroup= [
                'user_id'=>$request['user_id'],
                'group_id'=>$request['group_id'],
                'joined_date'=>$request['joined_date'] ?? Carbon::today()
             ];
             $userGroup= $this->userGroupRepository->create($userGroup);
             $this->groupRepository->increaseNumOfUser($request['group_id']);
            
             $message="user added to group successflly ";
             $code=200;
        }
        else{
            $message="user  already added to group ";
             $code=200;
        }
    }
        else{
            $message="you can not add user to this group ";
            $code=403;
        }
        return ['userGroup'=>$userGroup,'message'=>$message,'code'=>$code];
    }
    

}
