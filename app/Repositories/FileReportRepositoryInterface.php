<?php

namespace App\Repositories;

interface FileReportRepositoryInterface
{
    public function all();
    public function fileReportForGroup($id);
    public function find($id);
    public function findByFile($id1,$id2);
    

}