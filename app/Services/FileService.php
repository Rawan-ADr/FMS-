<?php
namespace App\Services;


use App\Models\Group;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\File;
use App\Models\EditContent;
use App\Models\Notification;
use App\Models\FileCopy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\FileRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserGroupRepositoryInterface;

class FileService {

     private  $userRepository;
    private $fileRepository;
     private  $notificationService;
    private  $userGroupRepository;
     public function __construct(UserRepositoryInterface $userRepository,FileRepositoryInterface $fileRepository ,UserGroupRepositoryInterface $userGroupRepository ,NotificationService $notificationService ){
        $this->fileRepository=$fileRepository;
         $this->userRepository = $userRepository;
        $this->userGroupRepository = $userGroupRepository;
         $this->notificationService =$notificationService;
     }


    public function addFile( $request)
   {
        $id=Auth::id();
        $group_id=$request['group_id'];
        $user_group=UserGroup::where('user_id',$id)->where('group_id',$request['group_id'])->value('id');
        $users_id=UserGroup::where('group_id',$request['group_id'])->pluck('user_id');
        $user=User::whereIn('id',$users_id)->get();
        $file=$request['file'];
        $name=$request['name'];
        $filePath='public/files/'.$group_id.'/'.$name;
        
         $existingFile = File::where('name', $name)->first(); 
        if ($existingFile) {
            $message="File with this name already exists.";
            $code=409;
            $file=null;
            return ['message'=>$message,'file'=>$file,'code'=>$code];
        }


        try{
        $path=Storage::put($filePath,file_get_contents($file->getRealpath()));
            }

        catch(\Exception $ex){
        return back()->withErrors(['file'=> 'Error ...'.$ex->getMessage()],500);

            }



        $file=[
            'name'=>$name,
            'path'=>$filePath,
            'type'=>$request['type'],
            'size'=>$file->getSize(),
            'user_group_id'=>$user_group,
            'state'=>"free",

        ];
        $file=$this->fileRepository->create($file);
        $file->save();
         $this->copyFiles($file->id);
       

       $user_group_id=$file->user_group_id;
        $UserGroup=$this->userGroupRepository->find($user_group_id);
        $group_id=$UserGroup->group_id;
        $UserGroups =$this->userRepository->usersIngroup($group_id);
        $uuser=Auth::user();

        
              $title=" Add";
              $body=" file was added by  ".$uuser->name;
        foreach($UserGroups as $UserGroup){
            $User=$this->userRepository->find($UserGroup->user_id);
              $this->notificationService->saveN($User, $title, $body);
            $notification=$this->notificationService->send($User, $title, $body);
        }
        $message=" File Add Successfully";
        $code=200;

        return ['message'=>$message,'file'=>$file,'code'=>$code];
   }






    public function reserveFile($id)
   {
        $file=$this->fileRepository->find($id);
       
         $user_group_id=$file->user_group_id;
        $UserGroup=$this->userGroupRepository->find($user_group_id);
        $group_id=$UserGroup->group_id;
        $Users =$this->userRepository->usersNotInPivotTable($group_id);
        $user=Auth::id();
        $uuser=Auth::user();
         DB::beginTransaction();
            try{
            if ( $file->state == 'free'){
            $file->state="reserved";
            $file->user_id=$user;
            $file->save();
           
            $message=" File reserved Successfully";
            $code=200;
            }


            else{
                $message=" This file is already reserved";
            $code=500;

            }
             DB::commit();
              $title=" Check In";
              $body=" file was reserved by  ".$uuser->name;
                foreach($Users as $User){
                    $notification=$this->notificationService->send($User, $title, $body);
                } 
        }
          catch (\Exception $ex) {
            DB::rollBack();
            $message=$ex->getMessage();
            $code=500;

        }
       return['message'=>$message,'file'=>$file,'code'=>$code];
   }




    public function unreserveFile($id)
    {
        $file=$this->fileRepository->find($id);
       $user_group_id=$file->user_group_id;
        $UserGroup=$this->userGroupRepository->find($user_group_id);
        $group_id=$UserGroup->group_id;
        $Users =$this->userRepository->usersNotInPivotTable($group_id);
        $user=Auth::id();
        $uuser=Auth::user();
        if ( $file->state == 'reserved'){
        $file->state="free";
        $file->user_id=null;
        $file->save();

              $title=" Check Out";
              $body=" file was released by  ".$uuser->name;
        foreach($Users as $User){
            $notification=$this->notificationService->send($User, $title, $body);
        }
        $message=" File unreserved Successfully";
        $code=200;
        }
        else{

            $message=" This file is already free";
            $code=500;


        }
        return['message'=>$message,'file'=>$file,'code'=>$code];
    }


    public function reserveAll($ids)
    {

        $idsArray=explode(',',$ids);
        $idsArray=array_map('intval',$idsArray);
        $user=Auth::id();
        $User=User::find($user)->first();


        
        DB::beginTransaction();
        try{

            foreach($idsArray as $file){
                 $file=$this->fileRepository->find($file);

                if ( $file->state == 'free'){
                    $file->state="reserved";
                    $file->user_id=$user;
                    $file->save();
                    $resulte=true;

                }


               else{
                    $message=" This file is already reserved";
                    $resulte=false;
                }

                if(!$resulte){
                    throw new \Exception ("Failed to reserved files ");
                }
            }

            DB::commit();
            $message=" Files reserved Successfully";
            $code=200;
           
        }

        catch (\Exception $ex) {
            DB::rollBack();
            $message=$ex->getMessage();
            $code=500;

        }

        return ['message'=>$message,'code'=>$code,'user'=>$User];

    }


    public function upDateFile($request,$id)
    {
         
         $file = $this->fileRepository->find($id);
        if (!$file) {
            $file=null;
            $message="file not found";
            return['message'=>$message,'file'=>$file];
        }

        $idd=$file->user_group_id;
        $userGroup = $this->userGroupRepository->find($idd)->value('user_id');
        if ((Auth::user()->hasRole('adminOfGroup'))||(Auth::id()==$userGroup))
        {
           
            $filePath=$file->path;
            $file=$request['file'];
         try
            {
            $path=Storage::put($filePath,file_get_contents($file->getRealpath()));
            }

         catch(\Exception $ex)
            {
            return back()->withErrors(['file'=> 'Error ...'.$ex->getMessage()]);
            }

          $File = $this->fileRepository->find($id);

          $file=[
            'name'=>$File->name,
            'path'=>$File->path,
            'type'=>$request['type'],
            'size'=>$file->getSize(),
            'user_group_id'=>$File->user_group_id,
            'state'=>"free",
           ];
            $file=$this->fileRepository->update($id,$file);
      

            $message="file updated successfully";
            $code=200;
             $this->copyFiles($id);
        }
        else
        {
            $message="you cannot update this";
            $code=500;
        }
         

        return['message'=> $message ,'file'=>$file,'code'=>$code];
    }


    public function getFile($id){
       $userId=Auth::id();

        $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($id,$userId);

        if(Auth::user()->hasRole('admin')||!is_null($userGroup)){
            $idd=$userGroup->id;
            $files=$this->fileRepository->get($idd);

            $message=" Done ";
            $code=200;
        
        }
        else{
            $files=null;
            $message =" You can not access";
            $code=500;
        }

         return['message'=>$message ,'file'=>$files,'code'=>$code];
    }




     public function getGroupFile($groupId){
       $userId=Auth::id();
       

        $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($groupId,$userId);


        if(!is_null($userGroup)){
           $files = File::whereHas('userGroup', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);})->where('is_approved',1)->get();
          $message=" Done ";
            $code=200;
        
        }
        else{
            $files=null;
            $message =" You can not access";
            $code=500;
        }

         return['message'=>$message ,'file'=>$files,'code'=>$code];
    }


     public function getadminFile($groupId){
            $userId=Auth::id();
            $group=Group::find($groupId)->first();

                if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id)
            || Auth::user()->hasRole('admin')){
                $files = File::whereHas('userGroup', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);})->where('is_approved',0)->get();
                $message=" Done ";
                    $code=200;
        
        }
        else{
            $files=null;
            $message =" You can not access";
            $code=500;
        }

         return['message'=>$message ,'file'=>$files,'code'=>$code];
    }



        
    public function approveFile($id)
    {
        $file=$this->fileRepository->find($id);


        $file->is_approved = 1;
        $file->save();
        $code=200;
        $message="done";


        return['message'=>$message ,'file'=>$file,'code'=>$code];
        
    }

    
    public function rejectFile($id)
    {
        $file=$this->fileRepository->find($id);

        $file=$this->fileRepository->delete($id);

        // $file->is_approved = false;
        // $file->save();
        $file=null;
        $code=200;
        $message="done";


        return['message'=>$message ,'file'=>$file,'code'=>$code];
        
    }

    public function copyFiles($id){
         $file = $this->fileRepository->find($id);
          $count=FileCopy::where('file_id',$id)->count();
         $filePath='public/Copyfiles/'.$file->id.'/'.$count+1;
        $copyFile=FileCopy::create([
            'name'=>$file->name,
            'type'=>$file->type,
            'size'=>$file->size,
            'file_id'=>$file->id,
            'path'=>$filePath,
         
            'copyNum'=>$count+1

        ]);
       
          $copyFile->save();
          

         try{
        $File=Storage::get($file->path);
        $path=Storage::put($filePath,$File);


            }

        catch(\Exception $ex){
        return back()->withErrors(['file'=> 'Error ...'.$ex->getMessage()]);

            }

    }


    public function showFileLogs($groupId){

         $userId=Auth::id();

        $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($groupId,$userId);

            if(Auth::user()->hasRole('admin')||!is_null($userGroup)){
                $FileLogs = Group::with('files.fileLogs')
                ->findOrFail($groupId)
                ->files
                ->flatMap(function ($file) {
                    return $file->fileLogs;
                });
        $message="Done";
        $code=200;}
        else{
        $message="you can not access ";
        $code=500;
        $FileLogs=[];
        }

        
        return['message'=> $message ,'FileLogs'=> $FileLogs,'code'=>$code];
    }

    public function showUserLogs($groupId){

         $userId=Auth::id();

        $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($groupId,$userId);

            if(Auth::user()->hasRole('admin')||Auth::user()->hasRole('adminOfGroup')){
                $UserLogs = Group::with('files.userLogs')
                ->findOrFail($groupId)
                ->files
                ->flatMap(function ($file) {
                    return $file->userLogs;
                });
        $message="Done";
        $code=200;}
        else{
        $message="you can not access ";
        $code=500;
        $UserLogs=[];
        }

        
        return['message'=> $message ,'UserLogs'=> $UserLogs,'code'=>$code];
    }


       public function showFileCopy($groupId){

         $userId=Auth::id();

        $userGroup= $this->userGroupRepository
            ->findByUserAndGroup($groupId,$userId);

            if(Auth::user()->hasRole('admin')||!is_null($userGroup)){
                $FileCopy = Group::with('files.fileCopies')
                ->findOrFail($groupId)
                ->files
                ->flatMap(function ($file) {
                    return $file->fileCopies;
                });
        $message="Done";
        $code=200;}
        else{
        $message="you can not access ";
        $code=500;
        $FileCopy=[];
        }

        
        return['message'=> $message ,'FileCopy'=> $FileCopy,'code'=>$code];
    }

     public function getFileCopies($fileId)
    {
 
            $FileCopies = File::with('fileCopies')->findOrFail($fileId)->fileCopies;
             $message="Done";
             $code=200;
       
        return['message'=> $message ,'FileCopies'=> $FileCopies,'code'=>$code];
    }

     public function getNotifications($Id)
    {
 
            $UserNotification =User::with('notifications')->findOrFail($Id)->notifications;
             $message="Done";
             $code=200;
       
        return['message'=> $message ,'UserNotification'=> $UserNotification,'code'=>$code];
    }

      public function showFileContent($id)
    {
        $file = $this->fileRepository->find($id);
        $filePath = storage_path('app/' .$file->path);

         if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $content = htmlspecialchars($content);
            $message="This is content";
            $code=200;

             } else {
             $message="file not found";
             $code=404;
             $content=null;
           }


        
        return['message'=> $message ,'content'=> $content,'code'=>$code];

    }
 

  
 public function showFileEdit( $request){

         $userId=Auth::id();

        // $userGroup= $this->userGroupRepository
        //     ->findByUserAndGroup($groupId,$userId);

            $id1=$request['file_id'];
            $id2=$request['copy_id'];
            // ||!is_null($userGroup)
            // if(Auth::user()->hasRole('admin')){
                $FileEdit = EditContent::where('file_id',$id1)->where('copy_id',$id2)->first();
               
        $message="Done";
        $code=200;
          //}
        // else{
        // $message="you can not access ";
        // $code=500;
        // $FileEdit=[];
        // }

        
        return['message'=> $message ,'FileEdit'=> $FileEdit,'code'=>$code];
    }








}