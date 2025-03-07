<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use App\Models\Child;
use App\Services\Zip\ZipService;

class DownloadController extends Controller
{
    protected $zipService;

    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
    }

    public function handle(Request $request)
    {
        $child_code = $request->query("child_code");
        $child = Child::where("child_code", $child_code)->with("content")->first();

        if (!$child) {
            return response()->json(['error' => 'Child code not found!'], 422);
        }

        // Get the content associated with the child
        $contentList = $child->content; // Assuming content is a collection or array

        // Check if the content is a single file or multiple files
        if ($contentList->count() == 1) {
            // Single file case
            $file = Http::get($contentList->first()->content_url);

            return Response::make($file->body(), 200, [
                'Content-Type' => $file->header('Content-Type'),
                'Content-Disposition' => 'attachment; filename="Annual_Progress_Report=' . $child_code . '.pdf"',
            ]);
        } else {
            // Multiple files case - ZIP and download
            $sponsorData = [];
            foreach ($contentList as $content) {
                $sponsorData[$content->name] = [
                    'sponsor_id' => $content->id,
                    'files' => [$content->content_url],
                ];
            }

            // Generate ZIP
            $zipFileName = 'Annual_Progress_Reports_' . $child_code . '.zip';
            $zipFilePath = $this->zipService->createZipFromSponsors($sponsorData, $zipFileName);

            // Return the zipped file for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
    }
}
