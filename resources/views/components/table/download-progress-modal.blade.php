<div class="modal fade" id="downloadProgressModal" tabindex="-1" aria-labelledby="downloadProgressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadProgressModalLabel">Downloading Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeDownloadModal" style="display: none;"></button>
            </div>
            <div class="modal-body">
                <!-- Progress State -->
                <div id="downloadProgressState">
                    <div class="text-center mb-3">
                        
                    </div>
                    <div class="progress mb-3">
                        <div id="downloadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <p id="downloadStatusText" class="text-center">Preparing your download...</p>
                </div>
                
                <!-- Success State -->
                <div id="downloadSuccessState" style="display: none;">
                    <div class="text-center">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Download Complete!</h5>
                        <p class="mt-2">Your file has been downloaded successfully.</p>
                    </div>
                </div>
                
                <!-- Error State -->
                <div id="downloadErrorState" style="display: none;">
                    <div class="text-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Download Failed</h5>
                        <p id="downloadErrorMessage" class="mt-2">An error occurred during the download.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDownloadBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="downloadDoneBtn" style="display: none;" data-bs-dismiss="modal">Done</button>
                <button type="button" class="btn btn-primary" id="downloadTryAgainBtn" style="display: none;">Try Again</button>
            </div>
        </div>
    </div>
</div>