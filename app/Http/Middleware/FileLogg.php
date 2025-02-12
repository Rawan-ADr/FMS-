<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FileLog;
use App\Models\File;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\EditContent;
use App\Models\FileReport;
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


    if($response->status()==200){
       
      if($name=='addFile')
      {
          $file=File::orderBy('id', 'desc')->first();
          $idd=$file->id;
          $id= $file->user_group_id;
          $userGroup=UserGroup::find($id);
          $group_id= $userGroup->group_id;
          $description = "file:'{$file->name}' added by user:'" 
          . Auth::user()->name . "' in date:" . Carbon::now()->toDateString();

          $fileLog= FileLog::create([
            'action' =>$name,
            'user_id' =>Auth::id(),
            'date' =>Carbon::today(),
            'file_id'=>$idd
          ]);

          FileReport::create([
            'description' => $description,
            'file_id' => $file->id,
            'group_id'=>$group_id
        ]);
      }


    if($name=='reserveAll')
    {
            $ids=$request->route('ids');
            $idsArray=explode(',', $ids);
            $idsArray=array_map('intval',$idsArray);

          
            foreach($idsArray as $idf)
            {
              $file=File::find($idf);
              if($file){
                  $fileLog= FileLog::create([
                'action' =>$name,
                'user_id' =>Auth::id(),
                'date' =>Carbon::today(),
                'file_id'=>$idf
              ]);
              $id= $file->user_group_id;
              $userGroup=UserGroup::find($id);
              $group_id= $userGroup->group_id;
              $description = "file: '{$file->name}' reserved by user: '" . Auth::user()->name . "' in date: " . Carbon::now()->toDateString();

              FileReport::create([
                  'description' => $description,
                  'file_id' => $idf,
                  'group_id'=>$group_id
              ]);
            }
            }
    }


    if($name=='upDateFile')
      {
           $id=$request->route('id');
           $file=File::find($id);

          
        
          $file1 = FileCopy::orderBy('created_at', 'desc')->first();
          $file2 = FileCopy::orderBy('created_at', 'desc')->skip(1)->first();

          $file1Content=Storage::get($file1->path);
          $file2Content=Storage::get($file2->path);

          $outputBuilder = new UnifiedDiffOutputBuilder();

            $differ = new Differ( $outputBuilder);
            $result = $differ->diff($file1Content, $file2Content);
            $copy_id=$file2->id;
           

            $Edit=EditContent::create([
              'file_id'=>$id,
              'copy_id'=>$copy_id,
              'content'=>$result]);
        $Edit->save();

           $fileLog= FileLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
        'edit_id'=>$Edit->id
       ]);
       $idd= $file->user_group_id;
       $userGroup=UserGroup::find($idd);
       $group_id= $userGroup->group_id;

       $description = "file: '{$file->name}' updated by user: '" . Auth::user()->name . "' in date: " . Carbon::now()->toDateString();

              FileReport::create([
                  'description' => $description,
                  'file_id' => $id,
                  'group_id'=>$group_id
              ]);

      }

      if($name=='reserveFile'){
          $id=$request->route('id');
          $file=File::find($id);
           $fileLog= FileLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);
       $idd= $file->user_group_id;
       $userGroup=UserGroup::find($idd);
       $group_id= $userGroup->group_id;

       $description = "file: '{$file->name}' reserved by user: '" . Auth::user()->name . "' in date: " . Carbon::now()->toDateString();

       FileReport::create([
           'description' => $description,
           'file_id' => $id,
           'group_id'=>$group_id
       ]);

      }

       if($name=='unreserveFile'){
          $id=$request->route('id');
          $file=File::find($id);
           $fileLog= FileLog::create([
        'action' =>$name,
        'user_id' =>Auth::id(),
        'date' =>Carbon::today(),
        'file_id'=>$id,
       ]);
       $idd= $file->user_group_id;
       $userGroup=UserGroup::find($idd);
       $group_id= $userGroup->group_id;
       $description = "file: '{$file->name}' unreserved by user: '" . Auth::user()->name . "' in date: " . Carbon::now()->toDateString();

       FileReport::create([
           'description' => $description,
           'file_id' => $id,
           'group_id'=>$group_id
       ]);

      }
    
    }
      
       return $response;


  }

   protected function onException(Request $request, Exception $e)
    {
        Log::error('حدث خطأ أثناء تنفيذ العملية : ' . $e->getMessage());
    }
    
}
