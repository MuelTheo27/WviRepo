
<div id="selectionBar" class="selection-bar mb-3" style="display: none;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span id="selectedCount">0</span> items selected
        </div>
        <div>
            <button id="downloadSelected" class="btn btn-primary btn-sm">
                <i class="bi bi-download"></i> Download Selected
            </button>
            <button id="deleteSelected" class="btn btn-danger btn-sm ms-2">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // References to elements
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectionBar = document.getElementById('selectionBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionModal = document.getElementById('bulkActionModal');
    const bulkActionCount = document.getElementById('bulkActionCount');
    const bulkActionMessage = document.getElementById('bulkActionMessage');
    const bulkDownloadOptions = document.getElementById('bulkDownloadOptions');
    const confirmBulkActionBtn = document.getElementById('confirmBulkAction');
    
    let bulkActionModalInstance = null;
    if (typeof bootstrap !== 'undefined') {
        bulkActionModalInstance = new bootstrap.Modal(bulkActionModal);
    }
    
    let rowSelection = {};
    let currentBulkAction = null;
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                const index = checkbox.getAttribute('data-index');
                if (isChecked) {
                    rowSelection[index] = true;
                } else {
                    delete rowSelection[index];
                }
            });
            updateSelectedCount();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const index = this.getAttribute('data-index');
            if (this.checked) {
                rowSelection[index] = true;
            } else {
                delete rowSelection[index];
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
            }
            updateSelectedCount();
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = rowCheckboxes.length > 0 && 
                                          Array.from(rowCheckboxes).every(cb => cb.checked);
            }
        });
    });
    
    // Bulk action buttons
    document.getElementById('downloadSelected')?.addEventListener('click', function() {
        showBulkActionModal('download');
        console.log("OEJIDE")
    });
    
    document.getElementById('deleteSelected')?.addEventListener('click', function() {
        showBulkActionModal('delete');
    });
    
    // Confirm bulk action button
 
    
    // Show the bulk action modal
    function showBulkActionModal(action) {
        currentBulkAction = action;
        
        // Get the count of selected items
        // const count = Object.keys(rowSelection).length;
        // bulkActionCount.textContent = count;
        
        // Show/hide download options
        if (action === 'download') {
            bulkActionMessage.textContent = `Are you sure you want to download ${count} selected items?`;
            bulkDownloadOptions.style.display = 'block';
            confirmBulkActionBtn.textContent = 'Download';
            confirmBulkActionBtn.className = 'btn btn-primary';
        } else if (action === 'delete') {
            bulkActionMessage.textContent = `Are you sure you want to delete ${count} selected items? This action cannot be undone.`;
            bulkDownloadOptions.style.display = 'none';
            confirmBulkActionBtn.textContent = 'Delete';
            confirmBulkActionBtn.className = 'btn btn-danger';
        }
        
        // Show the modal
        if (bulkActionModalInstance) {
            bulkActionModalInstance.show();
        }
    }
    
    confirmBulkActionBtn?.addEventListener('click', function() {
    if (currentBulkAction === 'download') {
        performBulkDownload();
    } else if (currentBulkAction === 'delete') {
        performBulkDelete();
    }
});

    function performBulkDownload() {
        const progressBar = bulkActionModal.querySelector('.progress');
        const progressBarInner = progressBar.querySelector('.progress-bar');
        progressBar.style.display = 'block';
        
        const selectedIds = getSelectedIds();

        const format = document.querySelector('input[name="downloadFormat"]:checked').value;
        
        let progress = 0;
        const interval = setInterval(function() {
            progress += 5;
            progressBarInner.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                
                setTimeout(function() {
                    if (bulkActionModalInstance) {
                        bulkActionModalInstance.hide();
                    }
                    progressBar.style.display = 'none';
                    progressBarInner.style.width = '0%';
                    
                    // Dispatch the actual download event
                    document.dispatchEvent(new CustomEvent('bulk-download', {
                        detail: {
                            ids: selectedIds,
                            format: format
                        }
                    }));
                }, 500);
            }
        }, 100);
    }
    
 // Perform bulk delete
    async function performBulkDelete() {
     
        const selectedIds = getSelectedIds();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios({
            url: 'api/delete',
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
            },
            data: selectedIds
        });

        if (bulkActionModalInstance) {
            bulkActionModalInstance.hide();
        }
     
        document.dispatchEvent(new CustomEvent('bulk-delete', {
            detail: {
                ids: selectedIds
            }
        }));
        
        resetSelection();
    }
    

    function getSelectedIds() {
        return Object.keys(rowSelection).map(index => {
        const row = document.querySelector(`tr[data-index="${index}"]`);
        return row ? { child_idn : row.cells[1].textContent.trim(), fiscal_year : row.cells[5].textContent.trim() } : null;
    }).filter(id => id !== null); 
    }
    
    // Reset selection helper
    function resetSelection() {
        rowSelection = {};
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    }
    
    // Update selected count helper
    function updateSelectedCount() {
        const selectedCount = Object.keys(rowSelection).length;
        
        // Update UI
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedCount;
        }
        
        if (selectionBar) {
            selectionBar.style.display = selectedCount > 0 ? 'block' : 'none';
        }
        
        // Dispatch custom event
        const selectionEvent = new CustomEvent('data-table-selection', {
            detail: { 
                selection: rowSelection, 
                count: selectedCount,
                selectedIds: getSelectedIds()
            }
        });
        document.dispatchEvent(selectionEvent);
    }
    
    // Listen for external events to update selection
    document.addEventListener('data-table-reset-selection', resetSelection);
    
    // When rows are filtered, we need to update the "select all" state
    document.addEventListener('data-table-filter-applied', function() {
        if (selectAllCheckbox) {
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => {
                const row = cb.closest('tr');
                return row && row.style.display !== 'none';
            });
            
            const allVisible = visibleCheckboxes.length > 0;
            const allChecked = allVisible && visibleCheckboxes.every(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
        }
    });
});
</script>