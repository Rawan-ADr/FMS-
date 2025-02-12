<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByEmail(string $email);
    public function groupForUser($id);
    public function updateFCM($id, $fcm);
    public function usersNotInPivotTable($id);
  
    public function searchForUserInGroup(string $name,$id);

}