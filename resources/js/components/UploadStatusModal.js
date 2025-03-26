const modalHTML = `
<div class="modal fade" id="excelFilesModal" tabindex="-1" aria-labelledby="excelFilesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="excelFilesModalLabel">Upload Progress</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="statusList" class="list-group">
          <!-- Progress items will be added here -->
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
`;

let listItems = [];
let progressIntervals = []; // Store interval IDs for cleanup

function createStatusModal(items) {
    // Use the passed items or fallback to stored listItems
    const filesToProcess = items || listItems;
    
    // Clean up any existing modal
    cleanupExistingModal();

    // Create fresh modal
    $('body').append(modalHTML);
    
    const statusModal = new bootstrap.Modal(document.getElementById("excelFilesModal"));
    statusModal.show();

    for(let i = 0; i < filesToProcess.length; i++) {
        const item = filesToProcess[i];
        const progressBarId = `progressBar-${item.uuid}`;
        
        const fileItem = $('<li>', {
            class: 'list-group-item p-3',
            style: 'background-color: #f8f9fa;'
        });

        const fileNameDiv = $('<div>', {
            class: 'd-flex justify-content-between align-items-center'
        }).append(
            $('<span>').text(item.fileName)
        );
 
        const progressDiv = $('<div>', {
            class: 'progress mt-2',
            style: 'height: 15px;'
        }).append(
            $('<div>', {
                id: progressBarId,
                class: 'progress-bar',
                role: 'progressbar',
                style: 'width: 0%', // Start at 0%
                'aria-valuenow': '0',
                'aria-valuemin': '0',
                'aria-valuemax': '100'
            })
        );
        
        // Add status text element
        const statusTextDiv = $('<div>', {
            id: `status-${item.uuid}`,
            class: 'status-text mt-2 small text-muted'
        }).text('Starting upload...');
       
        fileItem.append(fileNameDiv).append(progressDiv).append(statusTextDiv);
        $('#statusList').append(fileItem);
    }
    
    // Initialize progress polling
    initializeProgressPolling(filesToProcess);
    
    // Handle modal close event to clean up intervals
    document.getElementById('excelFilesModal').addEventListener('hidden.bs.modal', function () {
        clearAllProgressIntervals();
    });
    
    // Return the modal instance
    return statusModal;
}

function initializeProgressPolling(items) {
    // Clear any existing intervals first
    clearAllProgressIntervals();
    
    // Start polling for each file
    items.forEach(item => {
        console.log(`Setting up polling for file: ${item.uuid}`);
        
        const intervalId = setInterval(() => {
            fetchProgress(item.uuid);
        }, 1000); // Poll every second
        
        // Store interval ID for cleanup
        progressIntervals.push(intervalId);
    });
}

function fetchProgress(fileId) {
    $.ajax({
        url: `/upload/progress?upload_id=${fileId}`,
        method: 'GET',
        success: function(data) {
            const progress = data.total > 0 ? Math.round((data.progress / data.total) * 100) : 0;
        
            updateProgress(fileId, progress);
          
            const statusText = progress < 100 ? `Processing: ${progress}%` : 'Complete!';
            $(`#status-${fileId}`).text(statusText);
          
            if (progress >= 100) {
                $(`#progressBar-${fileId}`).removeClass('bg-info bg-warning').addClass('bg-success');
                clearProgressInterval(fileId);
            }
        },
        error: function(xhr, status, error) {
            console.error(`Error fetching progress for ${fileId}:`, error);
            $(`#status-${fileId}`).text('Error tracking progress');
        }
    });
}

function clearProgressInterval(fileId) {
 
}

function clearAllProgressIntervals() {
    progressIntervals.forEach(intervalId => {
        clearInterval(intervalId);
    });
    progressIntervals = [];
}

function cleanupExistingModal() {
    const existingModalElement = document.getElementById('excelFilesModal');
    if (existingModalElement) {
        try {
            const existingModal = bootstrap.Modal.getInstance(existingModalElement);
            if (existingModal) {
                existingModal.hide();
                existingModal.dispose();
            }
        } catch (error) {
            console.warn('Error cleaning up modal:', error);
        }
        
        $(existingModalElement).remove();
    }
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css({
        'overflow': '',
        'padding-right': ''
    });
    
    clearAllProgressIntervals();
}

function updateProgress(fileId, progress, status) {
    const progressBar = $(`#progressBar-${fileId}`);
    if (progressBar.length) {
        progressBar.css('width', `${progress}%`);
        progressBar.attr('aria-valuenow', progress);
        
        if (progress === 100) {
            progressBar.removeClass('bg-info bg-warning').addClass('bg-success');
        } else if (progress >= 70) {
            progressBar.removeClass('bg-info bg-danger').addClass('bg-warning');
        } else if (progress > 0) {
            progressBar.removeClass('bg-danger').addClass('bg-info');
        }
        
        // Update status text if provided
        if (status) {
            $(`#status-${fileId}`).text(status);
        }
    }
}

function setListItems(list) {
    listItems = list; 
}

export { setListItems, createStatusModal, updateProgress }