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
        $childIdns = (array) $request->json("child_idn");
        $fiscalYear = $request->json('fiscal_year');
        $downloadId = $request->json("download_id");


        if (count($childIdns) === 1) {
            ProgressController::startDownloadBroadcastProgress($downloadId, 1);

            $contentUrl = Child::where("child_idn", $childIdns[0])->first()->content()->where("fiscal_year", $fiscalYear)->first()->content_url;

            $file = Http::get($contentUrl);
            ProgressController::updateDownloadBroadcastProgress($downloadId);
            return Response::make($file->body(), 200, [
                'Content-Type' => $file->header('Content-Type'),
                'Content-Disposition' => 'attachment; filename="' . basename($contentUrl) . '.pdf"',
            ]);
        } else {

            $children = Child::whereIn("child_idn", $childIdns)
                ->whereHas("content", function ($query) use ($fiscalYear) {
                    $query->where("fiscal_year", $fiscalYear);
                })
                ->with(["content", "sponsor"])
                ->get();


            $sponsorData = [];
            $categoryName = "";
            foreach ($children as $child) {
                if ($child->sponsor && $child->content) {
                    $sponsorName = $child->sponsor->name;
                    $sponsorId = $child->sponsor->id;
                    $categoryName = $child->sponsor->category->sponsor_category_name;
                    if (!isset($sponsorData[$sponsorName])) {
                        $sponsorData[$sponsorName] = [
                            'id' => $sponsorId,
                            'url' => []
                        ];
                    }

                    $sponsorData[$sponsorName]['url'][] = $child->content->content_url;
                }
            }

            $zipFileName = 'Annual_Progress_Reports_' . $categoryName . '.zip';
            $zipFilePath = $this->zipService->createZipFromSponsors($sponsorData, $zipFileName, $categoryName, $downloadId);

            if (!file_exists($zipFilePath)) {
                return response()->json(['error' => 'File not found.'], 404);
            }

            $fileSize = filesize($zipFilePath);

            $response = response()->streamDownload(function () use ($zipFilePath) {
                readfile($zipFilePath);
            }, basename($zipFilePath), [
                'Content-Type' => 'application/zip',
                'Content-Length' => $fileSize,
            ]);

            register_shutdown_function(function () use ($zipFilePath) {
                if (file_exists($zipFilePath)) {
                    unlink($zipFilePath);
                }
            });

            return $response;
        }

    }
}
