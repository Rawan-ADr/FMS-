<?php

namespace App\Repositories;

interface UserReportRepositoryInterface
{
    public function all();
    public function userReportForGroup($id);
    public function find($id);
    public function findByUser($id1,$id2);
    

}