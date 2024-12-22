<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FileLog;
use App\Models\File;
use App\Models\EditContent;
use Carbon\Carbon;
use App\Models\FileCopy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Diff\Differ;
 use SebastianBergmann\Diff\Output\DiffOutputBuilder;
   use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class FileLogg
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
          $file=File::orderBy('created_at', 'desc')->first();
          $idd=$file->id;
          $fileLog= FileLog::create([
            'action' =>$name,
            'user_id' =>Auth::id(),
            'date' =>Carbon::today(),
            'file_id'=>$idd
          ]);
        }


       if($name=='reserveAll')
       {
            $ids=$request->route('ids');
            $idsArray=explode(',',$ids);
            $idsArray=array_map('intval',$idsArray);

            foreach($idsArray as $id)
            {
                  $fileLog= FileLog::create([
                'action' =>$name,
                'user_id' =>Auth::id(),
                'date' =>Carbon::today(),
                'file_id'=>$id,
              ]);
            }
         }

      if($name=='upDateFile')
      {
           $id=$request->route('id');
        
          $file1 = FileCopy::orderBy('created_at', 'desc')->first();
          $file2 = FileCopy::orderBy('created_at', 'desc')->skip(1)->first();

          $file1Content=Storage::get($file1->path);
          $file2Content=Storage::get($file2->path);

          $outputBuilder = new UnifiedDiffOutputBuilder();

            $differ = new Differ( $outputBuilder);
            $result = $differ->diff($file1Content, $file2Content);

            $Edit=EditContent::create([
              'file_id'=>$id,
              'copy_id'=>$file2->id,
              'content'=>$result]);
        $Edit->save();

           $fileLog= FileLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
        'edit_id'=>$Edit->id
       ]);



      }
      else{
          $id=$request->route('id');
           $fileLog= FileLog::create([
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
