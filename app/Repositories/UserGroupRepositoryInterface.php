<?php

namespace App\Repositories;

interface UserGroupRepositoryInterface
{
    
    public function create(array $data);
    public function findByUserAndGroup($id1,$id2);
    public function findByFileAndGroup($id1,$id2);
     public function find($id);
}