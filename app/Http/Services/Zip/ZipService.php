<?php
namespace App\Http\Services\Zip;

use ZipArchive;

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
    public function createZipFromSponsors(array $sponsors, $zipFileName)
    {
        // Create a temporary directory to store downloaded files.
        $tempDir = storage_path('app/temp/' . uniqid());
        mkdir($tempDir, 0777, true);

        // Create a new ZipArchive object
        $zip = new ZipArchive();
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create ZIP file.');
        }

        // Download each file, create folders by sponsor name, and add to ZIP
        foreach ($sponsors as $sponsorName => $sponsorData) {
            $sponsorFolder = "{$sponsorName}-{$sponsorData['sponsor_id']}";
            $sponsorFolderPath = "{$tempDir}/{$sponsorFolder}";
            mkdir($sponsorFolderPath, 0777, true);

            foreach ($sponsorData['files'] as $fileUrl) {
                // Download file content
                $fileContent = file_get_contents($fileUrl);
                $fileName = basename($fileUrl);

                // Save the file temporarily
                $tempFilePath = "{$sponsorFolderPath}/{$fileName}";
                file_put_contents($tempFilePath, $fileContent);

                // Add the file to the ZIP under the sponsor folder
                $zip->addFile($tempFilePath, "{$sponsorFolder}/{$fileName}");
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






