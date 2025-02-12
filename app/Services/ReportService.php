<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\FileReportRepositoryInterface;
use App\Repositories\UserReportRepositoryInterface;
use App\Repositories\GroupRepositoryInterface;
use App\Repositories\UserGroupRepositoryInterface;
use App\Models\UserReport;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


class ReportService
{
    private  $userReportRepository;
    private  $fileReportRepository;
    private  $groupRepository;
    private  $userGroupRepository;
    
     public function __construct(UserReportRepositoryInterface $userReportRepository ,
     FileReportRepositoryInterface $fileReportRepository,GroupRepositoryInterface $groupRepository,
     UserGroupRepositoryInterface $userGroupRepository){
        $this->userReportRepository = $userReportRepository;
        $this->fileReportRepository = $fileReportRepository;
        $this->groupRepository = $groupRepository;
        $this->userGroupRepository = $userGroupRepository;
     }
     public function userReportindex($group_id){
      $group = $this->groupRepository->find($group_id);
    
      if (is_null($group)) {
          return ["userReport" => null, "message" => "Group not found."];
      }
  
     
      if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id)) {
          $userReport= $this->userReportRepository->userReportForGroup($group_id);
      }
      elseif(Auth::user()->hasRole('admin')){
         $userReport=$this->userReportRepository->all();
      }
      else{
         return ["userReport" => null, "message" => "you can not see user report"];
      }
  
      return ["userReport" =>  $userReport, "message" => "user report indexed successfully"];

     }

     public function ReportindexForUser($group_id,$user_id){

        $group = $this->groupRepository->find($group_id);
    
      if (is_null($group)) {
          return ["userReport" => null, "message" => "Group not found."];
      }
      $report= $this->userReportRepository->findByUser($group_id,$user_id);

      if (is_null($report)) {
        return ["userReport" => null, "message" => "not found report for this user"];
    }
     
      if ((Auth::user()->hasRole('adminOfGroup') && Auth::id() == $group->user_id)
      || Auth::user()->hasRole('admin')) {
        return ["userReport" =>  $report, "message" => "user report indexed successfully"];

      }
     
      else{
         return ["userReport" => null, "message" => "you can not see user report"];
      }
       
     }

     public function fileReportindex($group_id){
      if(Auth::user()->hasRole('admin')){
         $fileReport=$this->fileReportRepository->all();
      }
      else{
         $userGroup= $this->userGroupRepository->findByUserAndGroup($group_id,Auth::id());
         if(is_null($userGroup)){
            return ["fileReport" => null, "message" => "you are not a member of these group"];
         }
         else{
            $fileReport=$this->fileReportRepository->fileReportForGroup($group_id);
         }
      }
      return ["fileReport" => $fileReport, "message" => "file report indexed successfully"];

     }

     public function ReportindexForFile($group_id,$file_id){

        $fileReport=$this->fileReportRepository->findByFile($group_id,$file_id);

        if(Auth::user()->hasRole('admin')){
           return ["fileReport" => $fileReport, "message" => "file report indexed successfully"];
         }
         else{
            $userGroup= $this->userGroupRepository->findByUserAndGroup($group_id,Auth::id());
            if(is_null($userGroup)){
               return ["fileReport" => null, "message" => "you are not a member of these group"];
            }
            else{
                return ["fileReport" => $fileReport, "message" => "file report indexed successfully"];
            }
         }
        
   
     }

     public function exportUserReportToPdf($id)
     {
         $report = $this->userReportRepository->find($id);
 
         if (!$report) {
             return response()->json(['error' => 'User report not found'], 404);
         }
 
         return \PDF::loadView('reports.user_pdf', ['report' => $report]);
     }
 
     public function exportUserReportToCsv($id)
     {
         $report = $this->userReportRepository->find($id);
 
         if (!$report) {
             return response()->json(['error' => 'User report not found'], 404);
         }
 
         $csvData = [
             ['Description', 'Created At'],
             [$report->description, $report->created_at],
         ];
 
         $fileName = 'user-report-' . $id . '.csv';
         $handle = fopen($fileName, 'w');
         foreach ($csvData as $row) {
             fputcsv($handle, $row);
         }
         fclose($handle);
 
         return $fileName;
     }

     public function exportFileReportToPdf($id)
     {
         $report = $this->fileReportRepository->find($id);
 
         if (!$report) {
             return response()->json(['error' => 'File report not found'], 404);
         }
         return \PDF::loadView('reports.file_pdf', ['report' => $report]);
   
     }
 
     
     public function exportFileReportToCsv($id)
     {
      $report = $this->fileReportRepository->find($id);
 
      if (!$report) {
          return response()->json(['error' => 'file report not found'], 404);
      }

      $csvData = [
          ['Description', 'Created At'],
          [$report->description, $report->created_at],
      ];

      $fileName = 'file-report-' . $id . '.csv';
      $handle = fopen($fileName, 'w');
      foreach ($csvData as $row) {
          fputcsv($handle, $row);
      }
      fclose($handle);

      return $fileName;

        
     }
 }

     


  

