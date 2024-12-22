<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserLog;
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
       $response= $next($request);

     $name=$request->route()->getActionMethod();
      
     if($response->status()===200){
       
      if($name=='addFile'){
      $file=File::max('id');
       $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>1,
       ]);}


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

       }
      }
      else{
          $id=$request->route('id');
           $userLog= UserLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);

      }
    
    }
      
       return $response;


    }
    
}
