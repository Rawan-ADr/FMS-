<?php

namespace App\Repositories;

interface GroupRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function groupByUserId($id);
    public function groupByNameAndDescription($id,string $name,string $description);
    public function increaseNumOfUser($id);

}