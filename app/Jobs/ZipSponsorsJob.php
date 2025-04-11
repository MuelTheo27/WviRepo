<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use ZipArchive;
use App\Http\Controllers\ProgressController;

class ZipSponsorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sponsors;
    protected $zipFileName;
    protected $categoryName;
    protected $downloadId;

    public function __construct(array $sponsors, string $zipFileName, string $categoryName, string $downloadId)
    {
        $this->sponsors = $sponsors;
        $this->zipFileName = $zipFileName;
        $this->categoryName = $categoryName;
        $this->downloadId = $downloadId;
    }

    public function handle(): void
    {
        $tempDir = storage_path('app/temp/' . uniqid());
        mkdir($tempDir, 0777, true);

        $zip = new ZipArchive();
        $zipFilePath = storage_path("app/public/{$this->zipFileName}");

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create ZIP file.');
        }

        $totalFiles = 0;
        foreach ($this->sponsors as $sponsorData) {
            $totalFiles += count($sponsorData['url']);
        }

        ProgressController::startDownloadBroadcastProgress($this->downloadId, $totalFiles);

        if ($this->categoryName === "Hardcopy") {
            $this->processHardcopy($zip, $tempDir);
        } else {
            $this->processSoftcopy($zip, $tempDir);
        }

        $zip->close();
        $this->deleteDirectory($tempDir);
    }

    private function processHardcopy($zip, $tempDir)
    {
        foreach ($this->sponsors as $sponsorName => $sponsorData) {
            foreach ($sponsorData['url'] as $content_url) {
                try {
                    $response = Http::get($content_url);

                    if ($response->successful()) {
                        $fileName = basename(parse_url($content_url, PHP_URL_PATH));
                        if (empty($fileName) || $fileName === '/' || $fileName === '.') {
                            $fileName = "file_" . md5($content_url) . ".pdf";
                        }

                        $tempFilePath = "{$tempDir}/" . uniqid() . ".tmp";
                        file_put_contents($tempFilePath, $response->body());

                        $zip->addFromString($fileName, file_get_contents($tempFilePath));

                        if (file_exists($tempFilePath)) {
                            unlink($tempFilePath);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing file: " . $e->getMessage());
                }

                ProgressController::updateDownloadBroadcastProgress($this->downloadId);
            }
        }
    }

    private function processSoftcopy($zip, $tempDir)
    {
        foreach ($this->sponsors as $sponsorName => $sponsorData) {
            $sponsorFolder = "{$sponsorName}-{$sponsorData['id']}";
            $sponsorFolderPath = "{$tempDir}/{$sponsorFolder}";
            mkdir($sponsorFolderPath, 0777, true);

            foreach ($sponsorData['url'] as $content_url) {
                $response = Http::get($content_url);

                if ($response->successful()) {
                    $fileName = basename($content_url);
                    $tempFilePath = "{$sponsorFolderPath}/{$fileName}";
                    file_put_contents($tempFilePath, $response->body());

                    $zip->addFile($tempFilePath, "{$sponsorFolder}/{$fileName}");
                }

                ProgressController::updateDownloadBroadcastProgress($this->downloadId);
            }
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            $filePath = "{$dir}/{$item}";
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($dir);
    }
}
