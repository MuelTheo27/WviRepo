@extends('layouts.table-layout')

@section('title', 'Sponsor Data')

@section('content')
    <div class="container">

        <div class="card">

            <div class="card-body" id="card-body">

                <!-- Reorganized top row with Add button, filters and search -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <button class="btn btn-success" id="addNewDataBtn">
                        Add New Data
                    </button>

                    <div class="d-flex gap-2" style="width: 36rem">
                        <div class="flex-grow-1">
                            <select id="categoryFilter" class="form-select">
                                <option value="">All Categories</option>
                                <option value="Mass Sponsor">Mass Sponsor</option>
                                <option value="Middle Sponsor">Middle Sponsor</option>
                                <option value="Major Sponsor">Major Sponsor</option>
                                <option value="Hardcopy">Hardcopy</option>
                            </select>
                        </div>

                        <div class="flex-grow-1">
                            <select id="yearFilter" class="form-select">
                                <option value="">All Fiscal Years</option>
                                @for($year = 2025; $year >= 2019; $year--)
                                    <option value="{{ $year }}">FY {{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <input type="text" id="searchInput" class="form-control w-25 ms-auto"
                        placeholder="Search for sponsors...">
                </div>

                <!-- Select All row -->
                <div class="d-flex align-items-center mb-2">
                    <div class="form-check">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <label class="form-check-label" for="selectAll">
                            Select All
                        </label>
                    </div>
                </div>

                <!-- Selection bar - hidden by default -->
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

                @include('components.scripts.delete-action')
                @include('components.table.select-all')

                <div id="table-container">
                    <div class="text-center py-4" id="loading-indicator">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data...</p>
                    </div>

                    <div class="table-responsive" id="table-responsive" style="display: none;">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <!-- Select All column - checkbox moved to separate row above -->
                                    </th>
                                    <th>Child Code</th>
                                    <th>Sponsor ID</th>
                                    <th>Sponsor Name</th>
                                    <th>Sponsor Category</th>
                                    <th>Fiscal Year</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sponsorTable">
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-danger" id="error-message" style="display: none;">
                        Failed to load data. Please try again.
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="mt-3" style="display: none;">
                    <!-- Pagination will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Data Modal -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataModalLabel">Add New Sponsor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSponsorForm">
                        <div class="mb-3">
                            <label for="childCode" class="form-label">Child Code</label>
                            <input type="text" class="form-control" id="childCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="sponsorId" class="form-label">Sponsor ID</label>
                            <input type="text" class="form-control" id="sponsorId" required>
                        </div>
                        <div class="mb-3">
                            <label for="sponsorName" class="form-label">Sponsor Name</label>
                            <input type="text" class="form-control" id="sponsorName" required>
                        </div>
                        <div class="mb-3">
                            <label for="sponsorCategory" class="form-label">Sponsor Category</label>
                            <select class="form-select" id="sponsorCategory" required>
                                <option value="">Select Category</option>
                                <option value="Mass Sponsor">Mass Sponsor</option>
                                <option value="Middle Sponsor">Middle Sponsor</option>
                                <option value="Major Sponsor">Major Sponsor</option>
                                <option value="Hardcopy">Hardcopy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fiscalYear" class="form-label">Fiscal Year</label>
                            <select class="form-select" id="fiscalYear" required>
                                <option value="">Select Year</option>
                                @for($year = 2025; $year >= 2019; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNewSponsor">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Global state
            let allData = [];
            let filteredData = [];
            let currentPage = 0;
            const pageSize = 10;
            let rowSelection = {};
            let addDataModal = null;

            // DOM elements
            const tableContainer = document.getElementById('table-container');
            const tableResponsive = document.getElementById('table-responsive');
            const sponsorTable = document.getElementById('sponsorTable');
            const loadingIndicator = document.getElementById('loading-indicator');
            const errorMessage = document.getElementById('error-message');
            const paginationContainer = document.getElementById('pagination-container');
            const selectionBar = document.getElementById('selectionBar');
            const selectedCount = document.getElementById('selectedCount');

            // Initialize Bootstrap modal
            if (document.getElementById('addDataModal')) {
                addDataModal = new bootstrap.Modal(document.getElementById('addDataModal'));
            }

            // Add New Data button event
            document.getElementById('addNewDataBtn')?.addEventListener('click', function () {
                // Reset form
                document.getElementById('addSponsorForm')?.reset();

                // Show modal
                if (addDataModal) {
                    addDataModal.show();
                }
            });

            // Save new sponsor event
            document.getElementById('saveNewSponsor')?.addEventListener('click', async function () {
                // Get form values
                const childCode = document.getElementById('childCode')?.value;
                const sponsorId = document.getElementById('sponsorId')?.value;
                const sponsorName = document.getElementById('sponsorName')?.value;
                const sponsorCategory = document.getElementById('sponsorCategory')?.value;
                const fiscalYear = document.getElementById('fiscalYear')?.value;

                // Validate form
                if (!childCode || !sponsorId || !sponsorName || !sponsorCategory || !fiscalYear) {
                    alert('Please fill all required fields');
                    return;
                }

                // Create new sponsor object
                const newSponsor = {
                    child_idn: childCode,
                    sponsor_id: sponsorId,
                    sponsor_name: sponsorName,
                    sponsor_category: sponsorCategory,
                    fiscal_year: fiscalYear
                };

                try {
                    // Send to API
                    const response = await axios.post('/api/sponsors', newSponsor);

                    if (response.data.success) {
                        // Add to local data
                        allData.unshift(response.data.sponsor || newSponsor);

                        // Hide modal
                        if (addDataModal) {
                            addDataModal.hide();
                        }


                        applyFilters();

                        alert('Sponsor added successfully');
                    }
                } catch (error) {
                    console.error('Error adding sponsor:', error);
                    alert('Failed to add sponsor: ' + (error.response?.data?.message || 'Unknown error'));
                }
            });

            async function fetchData() {
                try {
                    loadingIndicator.style.display = 'block';
                    tableResponsive.style.display = 'none';
                    errorMessage.style.display = 'none';
                    paginationContainer.style.display = 'none';


                    const response = await axios.get('/api/results');
                    allData = response.data;

                    applyFilters();


                    loadingIndicator.style.display = 'none';
                    tableResponsive.style.display = 'block';
                    paginationContainer.style.display = 'block';

                } catch (error) {
                    console.error('Error fetching data:', error);
                    loadingIndicator.style.display = 'none';
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = error.response?.data?.message || 'Error loading data. Please try again.';
                }
            }


            function applyFilters() {
                const category = document.getElementById('categoryFilter')?.value || '';
                const year = document.getElementById('yearFilter')?.value || '';
                const search = (document.getElementById('searchInput')?.value || '').toLowerCase();


                filteredData = allData.filter(sponsor => {

                    if (category && sponsor.sponsor_category !== category) {
                        return false;
                    }


                    if (year && sponsor.fiscal_year !== year) {
                        return false;
                    }


                    if (search) {
                        const searchFields = [
                            sponsor.child_idn,
                            sponsor.sponsor_id,
                            sponsor.sponsor_name,
                            sponsor.sponsor_category,
                            sponsor.fiscal_year
                        ];

                        if (!searchFields.some(field =>
                            String(field).toLowerCase().includes(search)
                        )) {
                            return false;
                        }
                    }

                    return true;
                });


                currentPage = 0;


                renderTable();
                renderPagination();


                rowSelection = {};
                updateSelectionInfo();
            }

            function renderTable() {
                // Clear the table body
                sponsorTable.innerHTML = '';

                if (filteredData.length === 0) {
                    // No data to display
                    sponsorTable.innerHTML = `
                                <tr id="noDataRow">
                                    <td colspan="7" class="text-center py-4">No matching records found</td>
                                </tr>
                            `;
                    return;
                }

                // Calculate slice for current page
                const start = currentPage * pageSize;
                const end = Math.min(start + pageSize, filteredData.length);
                const pageData = filteredData.slice(start, end);

                // Create and append rows
                pageData.forEach((sponsor, index) => {
                    const rowIndex = start + index;
                    const isSelected = rowSelection[rowIndex] === true;

                    const row = document.createElement('tr');
                    row.setAttribute('data-index', rowIndex);
                    if (isSelected) {
                        row.classList.add('table-active');
                    }

                    const checkboxCell = document.createElement('td');
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'form-check-input row-checkbox';
                    checkbox.setAttribute('data-index', rowIndex);
                    checkbox.checked = isSelected;
                    checkboxCell.appendChild(checkbox);

                    const childIdnCell = document.createElement('td');
                    childIdnCell.textContent = sponsor.child_idn;

                    const sponsorIdCell = document.createElement('td');
                    sponsorIdCell.textContent = sponsor.sponsor_id;

                    const sponsorNameCell = document.createElement('td');
                    sponsorNameCell.textContent = sponsor.sponsor_name;

                    const categoryCell = document.createElement('td');
                    categoryCell.textContent = sponsor.sponsor_category;

                    const yearCell = document.createElement('td');
                    yearCell.textContent = sponsor.fiscal_year;

                    const actionsCell = document.createElement('td');
                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'd-flex gap-2';

                    if (sponsor.sponsor_category === 'Major Sponsor' || sponsor.sponsor_category === 'Hardcopy') {
                        const downloadBtn = document.createElement('button');
                        downloadBtn.className = 'btn btn-sm btn-primary download-btn';
                        downloadBtn.textContent = 'Download';
                        downloadBtn.setAttribute('data-index', rowIndex);
                        actionsDiv.appendChild(downloadBtn);
                    }

                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'btn btn-sm btn-danger delete-btn';
                    deleteBtn.textContent = 'Delete';
                    deleteBtn.setAttribute('data-index', rowIndex);
                    actionsDiv.appendChild(deleteBtn);

                    actionsCell.appendChild(actionsDiv);

                    // Append all cells to row
                    row.appendChild(checkboxCell);
                    row.appendChild(childIdnCell);
                    row.appendChild(sponsorIdCell);
                    row.appendChild(sponsorNameCell);
                    row.appendChild(categoryCell);
                    row.appendChild(yearCell);
                    row.appendChild(actionsCell);

                    // Append row to table
                    sponsorTable.appendChild(row);
                });

                // Initialize event handlers
                initTableEvents();
            }



            function renderPagination() {
                const totalPages = Math.ceil(filteredData.length / pageSize);
                if (totalPages <= 1) {
                    paginationContainer.style.display = 'none';
                    return;
                }

                paginationContainer.style.display = 'block';

                let paginationHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing ${filteredData.length > 0 ? currentPage * pageSize + 1 : 0} to ${Math.min((currentPage + 1) * pageSize, filteredData.length)} of ${filteredData.length} entries
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <li class="page-item ${currentPage === 0 ? 'disabled' : ''}">
                                    <button class="page-link" data-page="prev" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </button>
                                </li>
                `;


                const maxPages = 5;
                const startPage = Math.max(0, Math.min(currentPage - Math.floor(maxPages / 2), totalPages - maxPages));
                const endPage = Math.min(startPage + maxPages, totalPages);

                for (let i = startPage; i < endPage; i++) {
                    paginationHTML += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <button class="page-link" data-page="${i}">${i + 1}</button>
                        </li>
                    `;
                }

                paginationHTML += `
                                <li class="page-item ${currentPage >= totalPages - 1 ? 'disabled' : ''}">
                                    <button class="page-link" data-page="next" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                `;

                paginationContainer.innerHTML = paginationHTML;

          
                const pageButtons = paginationContainer.querySelectorAll('.page-link');
                pageButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const page = this.getAttribute('data-page');

                        if (page === 'prev') {
                            if (currentPage > 0) {
                                currentPage--;
                            }
                        } else if (page === 'next') {
                            if (currentPage < totalPages - 1) {
                                currentPage++;
                            }
                        } else {
                            currentPage = parseInt(page);
                        }

                        renderTable();
                        renderPagination();
                    });
                });
            }


            function initTableEvents() {
         
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const index = this.getAttribute('data-index');

                        if (this.checked) {
                            rowSelection[index] = true;
                            this.closest('tr').classList.add('table-active');
                        } else {
                            delete rowSelection[index];
                            this.closest('tr').classList.remove('table-active');
                        }

                        updateSelectionInfo();
                    });
                });

    
                const downloadButtons = document.querySelectorAll('.download-btn');
                downloadButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const index = parseInt(this.getAttribute('data-index'));
                        const sponsor = filteredData[index - currentPage * pageSize];

                        handleDownload({
                            child_code: sponsor.child_idn
                        });
                    });
                });

       
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const index = parseInt(this.getAttribute('data-index'));
                        const sponsor = filteredData[index - currentPage * pageSize];

                        if (confirm(`Are you sure you want to delete sponsor ${sponsor.child_idn}?`)) {
                            handleDelete(sponsor.child_idn);
                        }
                    });
                });
            }

            function updateSelectionInfo() {
                const count = Object.keys(rowSelection).length;
                selectedCount.textContent = count;
                selectionBar.style.display = count > 0 ? 'block' : 'none';

                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    selectAllCheckbox.checked = checkboxes.length > 0 && checkboxes.length === count;
                    selectAllCheckbox.indeterminate = count > 0 && count < checkboxes.length;
                }
            }

            async function handleDownload(data) {
                try {
                    const response = await axios.post('/api/download', data, {
                        responseType: 'blob'
                    });

                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;

                    const contentDisposition = response.headers['content-disposition'];
                    let filename = 'download.pdf';
                    if (contentDisposition) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(contentDisposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();

                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);

                } catch (error) {
                    console.error('Download failed:', error);
                    alert('Failed to download file: ' + (error.response?.data?.message || 'Unknown error'));
                }
            }

            async function handleDelete(childId) {
                try {
                    const response = await axios.delete(`/api/delete?child_code=${encodeURIComponent(childId)}`);

                    if (response.data.success) {
                        // Remove from data arrays
                        allData = allData.filter(sponsor => sponsor.child_idn !== childId);

                        // Reset selection and refresh table
                        rowSelection = {};
                        applyFilters();

                        alert('Successfully deleted sponsor');
                    }
                } catch (error) {
                    console.error('Delete failed:', error);
                    alert('Failed to delete: ' + (error.response?.data?.message || 'Unknown error'));
                }
            }

            // Add event listener for "Select All" checkbox
            document.getElementById('selectAll')?.addEventListener('change', function () {
                const isChecked = this.checked;

                // Calculate current page items
                const start = currentPage * pageSize;
                const end = Math.min(start + pageSize, filteredData.length);

                // Update all checkboxes on current page
                for (let i = start; i < end; i++) {
                    if (isChecked) {
                        rowSelection[i] = true;
                    } else {
                        delete rowSelection[i];
                    }
                }

                // Update UI
                renderTable();
                updateSelectionInfo();
            });

            // Add event listeners for bulk actions
            document.getElementById('downloadSelected')?.addEventListener('click', function () {
                const selectedIndices = Object.keys(rowSelection);
                if (selectedIndices.length === 0) {
                    alert('Please select at least one item to download');
                    return;
                }

                const selectedIds = selectedIndices.map(index => filteredData[index].child_idn);

                handleDownload({
                    child_code: selectedIds
                });
            });

            document.getElementById('deleteSelected')?.addEventListener('click', function () {
                const selectedIndices = Object.keys(rowSelection);
                if (selectedIndices.length === 0) {
                    alert('Please select at least one item to delete');
                    return;
                }

                const selectedIds = selectedIndices.map(index => filteredData[index].child_idn);

                if (confirm(`Are you sure you want to delete ${selectedIds.length} selected items?`)) {
                    // Call delete function for each selected ID
                    Promise.all(selectedIds.map(id => handleDelete(id)))
                        .then(() => {
                            alert('Successfully deleted selected items');
                            rowSelection = {};
                            updateSelectionInfo();
                        })
                        .catch(error => {
                            console.error('Error in bulk deletion:', error);
                            alert('Some items could not be deleted');
                        });
                }
            });

            // Add listeners for filter controls
            document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
            document.getElementById('yearFilter')?.addEventListener('change', applyFilters);
            document.getElementById('searchInput')?.addEventListener('input', function () {
                // Debounce search input
                clearTimeout(this.timer);
                this.timer = setTimeout(applyFilters, 300);
            });
            // (rest of your existing JavaScript)

            // Initialize table
            fetchData();
        });
    </script>
@endsection