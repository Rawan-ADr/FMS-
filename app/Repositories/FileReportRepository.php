<?php
namespace App\Repositories;
use App\Models\FileReport;

class FileReportRepository implements FileReportRepositoryInterface
{
    public function all()
    {
        return FileReport::all();
    }

    public function fileReportForGroup($id){
        return FileReport::where('group_id',$id)->get();
    }

    public function find($id){
        return FileReport::find($id);
    }

    public function findByFile($id1,$id2){
        return FileReport::where('group_id',$id1)->where('file_id',$id2)->get();
    }

   
}