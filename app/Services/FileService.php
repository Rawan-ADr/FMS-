<?php
namespace App\Services;


use App\Models\Group;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\File;
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
use App\Repositories\UserGroupRepositoryInterface;

class FileService {

    private $fileRepository;
    private  $userGroupRepository;
     public function __construct(FileRepositoryInterface $fileRepository ,UserGroupRepositoryInterface $userGroupRepository ){
        $this->fileRepository=$fileRepository;
        $this->userGroupRepository = $userGroupRepository;
     }


   public function addFile( $request)
   {
        $id=Auth::id();
        $user_group=UserGroup::where('user_id',$id)->where('group_id',$request['group_id'])->value('id');
        $file=$request['file'];
        $name=$request['name'];
        $filePath='public/files/'.$id.'/'.$name;
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
            'user_groups'=>$user_group,
            'state'=>"free",

        ];
        $file=$this->fileRepository->create($file);
        $file->save();


       
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

        $message=" File Add Successfully";

        return ['message'=>$message,'file'=>$file];
   }






    public function reserveFile($id)
   {
        $file=$this->fileRepository->find($id);
        $user=Auth::id();

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
        $user=Auth::id();
        if ( $file->state == 'reserved'){
        $file->state="free";
        $file->user_id=null;
        $file->save();
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

        return ['message'=>$message,'code'=>$code];

    }


    public function upDateFile($request,$id)
    {
         
         $file = $this->fileRepository->find($id);
        if (!$file) {
            $file=null;
            $message="file not found";
            return['message'=>$message,'file'=>$file];
        }

        $idd=$file->user_groups;
        $userGroup = $this->userGroupRepository->find($idd)->value('user_id');
        if ((Auth::user()->hasRole('adminOfGroup'))||(Auth::id()==$userGroup))
        {
            $this->copyFiles($id);
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

          $File = $this->fileRepository->find($id)->first();

          $file=[
            'name'=>$File->name,
            'path'=>$File->path,
            'type'=>$request['type'],
            'size'=>$file->getSize(),
            'user_groups'=>$File->user_groups,
            'state'=>"free",
           ];
            $file=$this->fileRepository->update($id,$file);
      

            $message="file updated successfully";
            $code=200;
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

 

  









}