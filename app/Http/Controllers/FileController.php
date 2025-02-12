<?php

namespace App\Http\Controllers;


use App\Http\Requests\FileRequest;
use App\Http\Requests\FilesRequest;
use App\Http\Responses\Response;
use App\Services\FileService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\FCMNotification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\Code\Throwable;

class FileController extends Controller
{
     private FileService $fileService;
     private NotificationService $notificationService;

    public function __construct(FileService $fileService ,NotificationService $notificationService){
       $this->fileService = $fileService;
       $this->notificationService =$notificationService;
    }

     public function addFile(FileRequest $request){
        
         $data=[];
        try{
            
            $data=$this->fileService->addFile($request->validated());
            $id=Auth::id();
            $user=User::find($id)->first();
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }





    public function reserveFile($id){
        
         $data=[];
         $message="";
        try{
            
            $data=$this->fileService->reserveFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }

    }



    public function unreserveFile($id){
        
         $data=[];
        try{
            
            $data=$this->fileService->unreserveFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }

    }


    public function reserveAll($ids){
        
         $data=[];
        try{
            
            $data=$this->fileService->reserveAll($ids);
            return Response::Success($data['user'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }

    }

    public function upDateFile(Request $request,$id){
        
         $data=[];
        try{
            
            $data=$this->fileService->upDateFile($request,$id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }}



    public function getFile($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->getFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }

    public function getGroupFile($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->getGroupFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }
     public function getadminFile($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->getadminFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }

       public function approveFile($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->approveFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }


       public function rejectFile($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->rejectFile($id);
            return Response::Success($data['file'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }
       public function showFileLogs($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->showFileLogs($id);
            return Response::Success($data['FileLogs'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }

       public function showUserLogs($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->showUserLogs($id);
            return Response::Success($data['UserLogs'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }

   public function showFileCopy($id)
          {
        
         $data=[];
        try{
            
            $data=$this->fileService->showFileCopy($id);
            return Response::Success($data['FileCopy'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }
    }


    
   public function getFileCopies($id) {
        
         $data=[];
        try{
            
            $data=$this->fileService->getFileCopies($id);
            return Response::Success($data['FileCopies'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }

    }
     public function getNotifications($id) {
        
         $data=[];
        try{
            
            $data=$this->fileService->getNotifications($id);
            return Response::Success($data['UserNotification'],$data['message'],$data['code']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message,$code);

        }

    }




   public function showFileContent($id)
    { 

            $data=[];
            try{
                
                $data=$this->fileService->showFileContent($id);
                return Response::Success($data['content'],$data['message'],$data['code']) ;}
                
        
            catch (Throwable $th){
                $message=$th->getmessage();
                return Response::Error($data,$message,$code);

            }

        }
   public function showFileEdit(Request $request)
    { 

            $data=[];
            try{
                
                $data=$this->fileService->showFileEdit( $request);
                return Response::Success($data['FileEdit'],$data['message'],$data['code']) ;}
                
        
            catch (Throwable $th){
                $message=$th->getmessage();
                return Response::Error($data,$message,$code);

            }

        }

    
}
