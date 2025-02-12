<?php
namespace App\Repositories;
use App\Models\UserReport;

class UserReportRepository implements UserReportRepositoryInterface
{
    public function all()
    {
        return UserReport::all();
    }
    public function userReportForGroup($id){

        return UserReport::where('group_id',$id)->get();
    }
    public function find($id){
        return UserReport::find($id);
    }

    public function findByUser($id1,$id2){
        $report= UserReport::where('group_id',$id1)
                                ->where('user_id',$id2)
                                ->get();
        return $report;
    }

   
}