<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use App\Models\Child;
use App\Http\Services\Zip\ZipService;
use Symfony\Component\Console\Output\ConsoleOutput;
class DownloadController extends Controller
{
    protected $zipService;
    protected $console;
    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
        $this->console = new ConsoleOutput();
    }

    public function handle(Request $request)
    {
        $child_code = (array) $request->json("child_code");
        if (!$child_code) {
            return response()->json(['error' => 'Child code not found!'], 422);
        }

        $children = Child::whereIn("child_code", $child_code)->with(["content", "sponsor"])->get();
      
        $sponsorData = [];
        $categoryName = "";
        foreach ($children as $child) {
            if ($child->sponsor && $child->content) {
                $sponsorName = $child->sponsor->name;
                $categoryName = $child->sponsor->category->name;
                if (!isset($sponsorData[$sponsorName])) {
                    $sponsorData[$sponsorName] = [];
                }
        
                $sponsorData[$sponsorName][] = $child->content->content_url;
            }
        }

        if (count($child_code) === 1) {
            // Single file case
       
            $firstFile =  array_values(array_values($sponsorData)[0])[0];

            $file = Http::get( $firstFile);

            return Response::make($file->body(), 200, [
                'Content-Type' => $file->header('Content-Type'),
                'Content-Disposition' => 'attachment; filename="Annual_Progress_Report=' . $firstFile . '.pdf"',
            ]);
        } else {
            $zipFileName = 'Annual_Progress_Reports_' . '.zip';
            $zipFilePath = $this->zipService->createZipFromSponsors($sponsorData, $zipFileName, $categoryName);

            // Return the zipped file for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
    
    }
}
