<?php

namespace App\Http\Controllers;


use App\Http\Requests\FileRequest;
use App\Http\Requests\FilesRequest;
use App\Http\Responses\Response;
use App\Services\FileService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\Code\Throwable;

class FileController extends Controller
{
     private FileService $fileService;

    public function __construct(FileService $fileService){
       $this->fileService = $fileService;
    }

    public function addFile(FileRequest $request){
        
         $data=[];
        try{
            
            $data=$this->fileService->addFile($request->validated());
            return Response::Success($data['file'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

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
            return Response::Success("",$data['message'],$data['code']) ;}
            
    
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

    
}
