<?php

namespace App\Http\Controllers;
use App\Services\ReportService;
use App\Http\Responses\Response;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService){
       $this->reportService = $reportService;
    }
    
    public function userReportindex($group_id){
        $data=[];
        try{
            
            $data=$this->reportService->userReportindex($group_id);
            return Response::Success($data['userReport'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }
    }

    public function ReportindexForUser($group_id,$user_id){
        $data=[];
        try{
            
            $data=$this->reportService->ReportindexForUser($group_id,$user_id);
            return Response::Success($data['userReport'],$data['message']) ;}
            
    
        catch (Throwable $th){
            $message=$th->getmessage();
            return Response::Error($data,$message);

        }

    }

        public function fileReportindex($group_id){
          $data=[];
          try{
        
             $data=$this->reportService->fileReportindex($group_id);
             return Response::Success($data['fileReport'],$data['message']) ;}
        

         catch (Throwable $th){
             $message=$th->getmessage();
             return Response::Error($data,$message);

           }
        }

        public function ReportindexForFile($group_id,$file_id){
         
            $data=[];
            try{
          
               $data=$this->reportService->ReportindexForFile($group_id,$file_id);
               return Response::Success($data['fileReport'],$data['message']) ;}
          
  
           catch (Throwable $th){
               $message=$th->getmessage();
               return Response::Error($data,$message);
  
             }
        }

        public function exportUserReportToPdf($id)
        {
            $pdf = $this->reportService->exportUserReportToPdf($id);
            return $pdf->download('user-report-' . $id . '.pdf');
        }
    
        public function exportUserReportToCsv($id)
        {
            $fileName = $this->reportService->exportUserReportToCsv($id);
            return response()->download($fileName)->deleteFileAfterSend(true);
        }

        public function exportFileReportToPdf($id)
        {
            $pdf = $this->reportService->exportFileReportToPdf($id);
            return $pdf->download('file-report-' . $id . '.pdf');
        }
    
        public function exportFileReportToCsv($id)
        {
            $fileName = $this->reportService->exportFileReportToCsv($id);
            return response()->download($fileName)->deleteFileAfterSend(true);
        }
}
