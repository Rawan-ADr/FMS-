<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserLog;
use App\Models\UserReport;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserLogg
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

          $this->before($request);

        try {
            
            $response = $next($request);

          
            $this->after($request, $response);

            return $response;
        } catch (Exception $e) {
           
            $this->onException($request, $e);
            throw $e;
        }
      }

    protected function before(Request $request)
    {
        
    }
    protected function after(Request $request, Response $response)
    {

     $name=$request->route()->getActionMethod();
      
     if($response->status()===200){
       
      if($name=='addFile'){
     $file=File::orderBy('id', 'desc')->first();
          $idd=$file->id;
       $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$idd,
       ]);
       
       $id= $file->user_group_id;
       $userGroup=UserGroup::find($id);
       $group_id= $userGroup->group_id;
       $group=Group::find($group_id);

       $description = "user:'" . Auth::user()->name . "' add file :'{$file->name}'in group: '{$group->name}' in date : "
   . Carbon::now()->toDateString();

 UserReport::create([
   'description' => $description,
   'user_id' => Auth::id(),
   'group_id'=>$group_id
]);
      }


       if($name=='reserveAll'){
         $ids=$request->route('ids');
        $idsArray=explode(',',$ids);
        $idsArray=array_map('intval',$idsArray);
        foreach($idsArray as $id){
          $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);

       $file=File::find($id);
       $idd= $file->user_group_id;
      $userGroup=UserGroup::find($idd);
      $group_id= $userGroup->group_id;
      $group=Group::find($group_id);

      $description = "user:'" . Auth::user()->name . "' reserve file :'{$file->name}'in group: '{$group->name}' in date : "
   . Carbon::now()->toDateString();

    UserReport::create([
      'description' => $description,
      'user_id' => Auth::id(),
      'group_id'=>$group_id
  ]);


       }
      }
       if($name=='reserveFile'){
          $id=$request->route('id');
           $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);

       $file=File::find($id);
       $idd= $file->user_group_id;
      $userGroup=UserGroup::find($idd);
      $group_id= $userGroup->group_id;
      $group=Group::find($group_id);

      $description = "user:'" . Auth::user()->name . "' reserve file :'{$file->name}'in group: '{$group->name}' in date : "
   . Carbon::now()->toDateString();

    UserReport::create([
      'description' => $description,
      'user_id' => Auth::id(),
      'group_id'=>$group_id
  ]);

      }
      if($name=='unreserveFile'){
          $id=$request->route('id');
           $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);

       $file=File::find($id);
       $idd= $file->user_group_id;
      $userGroup=UserGroup::find($idd);
      $group_id= $userGroup->group_id;
      $group=Group::find($group_id);

      $description = "user:'" . Auth::user()->name . "' unreserve file :'{$file->name}'in group: '{$group->name}' in date : "
   . Carbon::now()->toDateString();

    UserReport::create([
      'description' => $description,
      'user_id' => Auth::id(),
      'group_id'=>$group_id
  ]);

      }
    
    }
      
       return $response;

   
    }

    
    protected function onException(Request $request, Exception $e)
    {
        Log::error('حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage());
    }
    
}