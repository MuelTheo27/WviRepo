<?php
namespace App\Http\Services\Zip;

use App\Http\Controllers\ProgressController;
use Laravel\Prompts\Progress;
use ZipArchive;

use Illuminate\Support\Facades\Http;
class ZipService
{
    /**
     * Create a ZIP file from the array of URLs and file names.
     *
     * @param array $sponsors Array of sponsors' data to zip.
     * @param string $zipFileName Name of the resulting zip file.
     * @return string Path to the ZIP file.
     * @throws \Exception
     */
    public function createZipFromSponsors(array $sponsors, $zipFileName, $categoryName, $downloadId)
{

 
    $tempDir = storage_path('app/temp/' . uniqid());
    mkdir($tempDir, 0777, true);

    $zip = new ZipArchive();
    $zipFilePath = storage_path("app/public/{$zipFileName}");

    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        throw new \Exception('Could not create ZIP file.');
    }

    $totalFiles = 0;
        foreach ($sponsors as $sponsorData) {
            $totalFiles += count($sponsorData['url']);
        }
        
    ProgressController::startDownloadBroadcastProgress($downloadId, $totalFiles);
        
    $filesProcessed = 0;

    if($categoryName === "Hardcopy") {
        foreach ($sponsors as $sponsorName => $sponsorData) {
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
                    \Log::error("Error processing file: " . $e->getMessage());
                }
                
                $filesProcessed++;
                ProgressController::updateDownloadBroadcastProgress($downloadId);
            }
        }
    }
    else {
        foreach ($sponsors as $sponsorName => $sponsorData) {
            $sponsorFolder = "{$sponsorName}-{$sponsorData['id']}";
            $sponsorFolderPath = "{$tempDir}/{$sponsorFolder}";
            mkdir($sponsorFolderPath, 0777, true);

            foreach ($sponsorData['url'] as $content_url) {
                $fileContent = Http::get($content_url);
                
                if ($fileContent->successful()) {
                    $fileName = basename($content_url);

                    $tempFilePath = "{$sponsorFolderPath}/{$fileName}";
                    file_put_contents($tempFilePath, $fileContent->body());

                    $zip->addFile($tempFilePath, "{$sponsorFolder}/{$fileName}");
                }
                $filesProcessed++;
                ProgressController::updateDownloadBroadcastProgress($downloadId);
            
            }
        }
    }

    // Close the ZIP archive
    $zip->close();

    // Clean up temporary files
    $this->deleteDirectory($tempDir);

    return $zipFilePath;
}
    /**
     * Helper function to recursively delete a directory.
     */
    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return;
        }
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






