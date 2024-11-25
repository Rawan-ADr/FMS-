<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Responses\Response;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\Code\Throwable;


class GroupController extends Controller
{
    private GroupService $groupService;

    public function __construct(GroupService $groupService){
       $this->groupService = $groupService;
    }

    public function create(GroupRequest $request){
        
        $data=[];
        try{
            
            $data=$this->groupService->create($request->validated());
            return Response::Success($data['group'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
       
}
     public function update(GroupRequest $request,$group_id){
        $data=[];
        try{
            
            $data=$this->groupService->update($request->validated(),$group_id);
            return Response::Success($data['group'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

    public function delete($group_id){
        $data=[];
        try{
            
            $data=$this->groupService->delete($group_id);
            return Response::Success($data['group'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

    public function index(){
        $data=[];
        try{
            
            $data=$this->groupService->index();
            return Response::Success($data['group'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
}
}