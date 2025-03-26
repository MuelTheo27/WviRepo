async function handleDownload(json_data) {
    if (!json_data) {
        console.error("data is required");
        return;
    }
    
    try {
        // Generate unique download ID
        const downloadId = `download_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        json_data.download_id = downloadId;
        
        // Show progress modal before starting the download
        showDownloadProgress(downloadId);
        
        const response = await fetch('api/download', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(json_data),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const blob = await response.blob();

        // Create temporary link for download
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;

        // Get filename from Content-Disposition header
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = 'downloaded_file';
        if (contentDisposition) {
            const match = contentDisposition.match(/filename="(.+?)"/);
            if (match) filename = match[1];
        }

        link.download = filename;
        document.body.appendChild(link);
        link.click();

        // Clean up
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);
        
        // Mark download as complete in UI
        completeDownloadProgress();
    } catch (error) {
        console.error("Download failed:", error);
        // Show error in progress modal
        showDownloadError(error.message);
    }
}

function showDownloadProgress(downloadId) {
    // Reset progress bar and show modal
    $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
    $('.progress-bar').removeClass('bg-danger').addClass('bg-info');
    
    $('#statusText').text("Preparing your download...");
    $('#downloadProgressModal').modal('show');

    // Start tracking progress
    startProgressTracking(downloadId);
}

let progressInterval = null;
let lastProgress = 0;
let stalledCount = 0;

function startProgressTracking(downloadId) {
    // Clear any existing interval
    if (progressInterval) {
        clearInterval(progressInterval);
    }
    
    lastProgress = 0;
    stalledCount = 0;
    
    // Poll for progress updates
    progressInterval = setInterval(() => {
        fetchDownloadProgress(downloadId);
    }, 1000); // Check every second
    
    // Safety timeout - if download takes too long
    setTimeout(() => {
        if (progressInterval) {
            clearInterval(progressInterval);
            
            // Only show timeout if we haven't reached 100%
            const currentProgress = parseInt($('.progress-bar').attr('aria-valuenow'));
            if (currentProgress < 100) {
                $('#statusText').text("Download is taking longer than expected but may still be processing...");
            }
        }
    }, 10 * 60 * 1000); // 10 minutes max
}

async function fetchDownloadProgress(downloadId) {
    try {
        const response = await fetch(`download/progress?download_id=${downloadId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        const data = await response.json();
        const progress = data.total > 0 ? Math.round((data.progress / data.total) * 100) : 0;
        
        // Update progress bar
        $('.progress-bar').css('width', `${progress}%`).attr('aria-valuenow', progress);
        
        // Update status text
        if (progress === 0) {
            $('#statusText').text("Starting download...");
        } else if (progress < 100) {
            $('#statusText').text(`Downloading: ${progress}% (${data.progress} of ${data.total} files)`);
        } else {
            $('#statusText').text("Download complete! Preparing file...");
            
            // Download is done, stop checking
            clearInterval(progressInterval);
            progressInterval = null;
        }
        
        // Detect stalled downloads (progress not changing)
        if (progress === lastProgress) {
            stalledCount++;
            
            // If progress hasn't changed for 15 seconds, show a message
            if (stalledCount >= 15) {
                $('#statusText').text(`Download is processing... (${progress}%)`);
            }
        } else {
            stalledCount = 0;
            lastProgress = progress;
        }
        
        // Update progress bar color based on progress
        if (progress >= 90) {
            $('.progress-bar').removeClass('bg-info bg-warning').addClass('bg-success');
        } else if (progress >= 50) {
            $('.progress-bar').removeClass('bg-info bg-success').addClass('bg-warning');
        }
        
    } catch (error) {
        console.error("Error fetching download progress:", error);
        
        // Don't stop tracking on temporary errors
        // But limit error count to avoid infinite errors
        stalledCount++;
        if (stalledCount > 10) {
            clearInterval(progressInterval);
            progressInterval = null;
            showDownloadError("Error tracking download progress");
        }
    }
}

function completeDownloadProgress() {
    // Stop progress tracking
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    
    // Set progress to 100%
    $('.progress-bar').css('width', '100%').attr('aria-valuenow', 100);
    $('.progress-bar').removeClass('bg-info bg-warning').addClass('bg-success');
    
    // Update status text
    $('#statusText').text("Download complete!");
    
    // Add a "Close" button or auto-close
    setTimeout(() => {
        // You might want to keep the modal open until user dismisses it
        // $('#downloadProgressModal').modal('hide');
    }, 3000);
}

function showDownloadError(errorMessage) {
    // Stop progress tracking
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    
    // Show error state
    $('.progress-bar').removeClass('bg-info bg-warning bg-success').addClass('bg-danger');
    $('#statusText').text(`Error: ${errorMessage}`);
}

export {handleDownload}