<?php
namespace App\Http\Services\Zip;

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
    public function createZipFromSponsors(array $sponsors, $zipFileName, $categoryName)
    {
        $tempDir = storage_path('app/temp/' . uniqid());
        mkdir($tempDir, 0777, true);

        $zip = new ZipArchive();
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create ZIP file.');
        }

        if($categoryName === "Hardcopy"){
            foreach ($sponsors as $sponsorName => $contents) {
                foreach ($contents as $content_url) {
                    $response = Http::get($content_url);
                    if ($response->successful()) {
                        $fileName = basename($content_url);
                        $tempFilePath = storage_path("app/temp/{$fileName}");
            
                        file_put_contents($tempFilePath, $response->body());
                        $zip->addFile($tempFilePath, $fileName);
            
                        unlink($tempFilePath);
                    }
                }
            }
        }

        else{
            foreach ($sponsors as $sponsorName => $contents) {
                $sponsorFolder = "{$sponsorName}";
                $sponsorFolderPath = "{$tempDir}/{$sponsorFolder}";
                mkdir($sponsorFolderPath, 0777, true);

                foreach ($contents as $content_url) {
                    $fileContent =  Http::get($content_url);
                    $fileName = basename($content_url);

                    $tempFilePath = "{$sponsorFolderPath}/{$fileName}";
                    file_put_contents($tempFilePath, $fileContent);

                    $zip->addFile($tempFilePath, "{$sponsorFolder}/{$fileName}");
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






