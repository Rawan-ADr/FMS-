<?php
namespace App\Repositories;
use App\Models\File;

class FileRepository implements FileRepositoryInterface
{
    public function all()
    {
        return File::all();
    }

    public function find($id)
    {
        return File::find($id);
    }

    public function create(array $data)
    {
        return File::create($data);
    }

    public function update($id, array $data)
    {
        $file = File::find($id);
        if ($file) {
            $file->update($data);
            return $file;
        }
        return null;
    }

    public function delete($id)
    {
        $file = File::find($id);
        if ($file) {
            $file->delete();
            return true;
        }
        return false;
    }

    public function fileByUserId($id){
        $file =File::where('user_id',$id)->get();
        return $file;
    }

      public function get($id)
    {
        return File::where('user_groups',$id)->get();
    }

    
}