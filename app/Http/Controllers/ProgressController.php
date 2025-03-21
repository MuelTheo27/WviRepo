<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgressController extends Controller
{
    //
    public static function updateUploadBroadcastProgress($uploadId)
    {
        $totalKey = "total_progress_$uploadId";
        $progressKey = "upload_progress_$uploadId";

        $total = \Cache::get($totalKey, 1);
        $progress = \Cache::increment($progressKey);

        if ($progress > $total) {
            \Cache::put($progressKey, $total);
        }
    }
    public static function startUploadBroadcastProgress($uploadId, $total){
        $totalKey = "total_progress_$uploadId";
        $progressKey = "upload_progress_$uploadId";

        \Cache::put($progressKey, 0, 60);
        \Cache::put($totalKey, $total, 60);
    }

    public function getUploadProgress(Request $request){
        
        $uploadId = $request->query('upload_id');
        $totalKey = "total_progress_$uploadId";
        $progressKey = "upload_progress_$uploadId";

        $progress = \Cache::get($progressKey, 0);
        $total = \Cache::get($totalKey, 1);

        return response()->json(['progress' => $progress, 'total' => $total]);
    }

    public static function updateDownloadBroadcastProgress($downloadId)
    {
        $totalKey = "total_progress_$downloadId";
        $progressKey = "download_progress_$downloadId";

        $total = \Cache::get($totalKey, 1);
        $progress = \Cache::increment($progressKey);

        
    }
    public static function startDownloadBroadcastProgress($downloadId, $total){
        $totalKey = "total_progress_$downloadId";
        $progressKey = "download_progress_$downloadId";

        \Cache::put($progressKey, 0, 60);
        \Cache::put($totalKey, $total, 60);
    }

    public function getDownloadProgress(Request $request){
        
        $downloadId = $request->query('download_id');
        if (empty($downloadId)) {
            return response()->json(['error' => 'Missing download ID'], 400);
        }
        $totalKey = "total_progress_$downloadId";
        $progressKey = "download_progress_$downloadId";

        $progress = \Cache::get($progressKey, 0);
        $total = \Cache::get($totalKey, 1);

        return response()->json(['progress' => $progress, 'total' => $total]);
    }

    public static function getProgress($downloadId){
        $progressKey = "download_progress_$downloadId";

        return \Cache::get($progressKey, 0);
    }

    public function clearDownloadProgress(Request $request)
{
    $downloadId = $request->query('download_id');
    if (empty($downloadId)) {
        return response()->json(['error' => 'Missing download ID'], 400);
    }
    
    $totalKey = "total_progress_$downloadId";
    $progressKey = "download_progress_$downloadId";
    
    \Cache::forget($progressKey);
    \Cache::forget($totalKey);
    
    return response()->json(['success' => true]);
}
}
