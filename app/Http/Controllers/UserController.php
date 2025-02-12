<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserGroupRequest;
use App\Http\Requests\UserNameRequest;
use App\Http\Responses\Response;
use App\Services\UserService;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;


class UserController extends Controller
{
     private UserService $userService;

     public function __construct(UserService $userService){
        $this->userService = $userService;
     }


    public function register(UserRequest $request){
        $data=[];
        try{
            $data=$this->userService->register($request->validated());
            return Response::Success($data['user'],$data['message']) ;
        }

        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

    public function login(Request $request){

           $data=[];
        try{
            $data=$this->userService->login($request);
            return Response::Success($data['user'],$data['message'],$data['code']) ;
        }

        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

      public function logout(){

           $data=[];
        try{
            $data=$this->userService->logout();
            return Response::Success($data['user'],$data['message'],$data['code']) ;
        }

        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

    public function index($group_id){

        $data=[];
     try{
         $data=$this->userService->index($group_id);
         return Response::Success($data['user'],$data['message'],$data['code']) ;
     }

     catch (Throwable $th){
         $message=$th->getmessage();
         return Response::Error($data,$message);

     }
 }

    public function addUserToGroup(UserGroupRequest $request){
        $data=[];
        try{
            $data=$this->userService->addUserToGroup($request->validated());
            return Response::Success($data['userGroup'],$data['message']) ;
        }

        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

        public function searchForUserByName(UserNameRequest $request,$group_id){
            $data=[];
            try{
                $data=$this->userService->searchForUserByName($request->validated(),$group_id);
                return Response::Success($data['user'],$data['message']) ;
            }
    
            catch (Throwable $th){
                $message=$th->getmessage();
                return Response::Error($data,$message);
    
            }

        }

 

}
