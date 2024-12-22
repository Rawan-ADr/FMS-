<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\FileCopy;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CopyFile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $response= $next($request);
         if($response->status()==200){
        $id=$request->route('id');
        $file=File::find($id)->first();
        $count=FileCopy::where('file_id',$id)->count();
        $copyFile=FileCopy::create([
            'name'=>$file->name,
            'type'=>$file->type,
            'size'=>$file->size,
            'file_id'=>$file->id,
            'path'=>'public/Copyfiles/',
         
            'copyNum'=>$count+1

        ]);
        
        $copyFile->save();
        $filePath='public/Copyfiles/'.$file->id.'/'.$copyFile->copyNum;

         try{
        $File=Storage::get($file->path);
        $path=Storage::put($filePath,$File);


            }

        catch(\Exception $ex){
        return back()->withErrors(['file'=> 'Error ...'.$ex->getMessage()]);

            }}


        return $response;
    }
}
