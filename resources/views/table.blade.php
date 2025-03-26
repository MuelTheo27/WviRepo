<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <style>
    .dropzone {
      border: 2px dashed #ccc;
      background: #f9f9f9;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      position: relative;
    }
    .dz-message {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }
    .file-list {
      margin-top: 10px;
      padding: 0;
    }
    .file-list li {
      list-style: none;
      padding: 8px;
      margin-bottom: 5px;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
    }
    .remove-file {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
    }
    .success-file {
      background: #d4edda;
    }
    .error-file {
      background: #f8d7da;
    }
    /* Pagination & Table Styles */
    .pagination-btn {
      padding: 5px 10px;
      cursor: pointer;
      border: 1px solid #007bff;
      background: white;
      color: #007bff;
      margin: 2px;
      border-radius: 5px;
    }
    .pagination-btn.active {
      background: #007bff;
      color: white;
    }
    .table-container {
      max-height: 500px;
      overflow-y: auto;
    }
    </style>
</head>
<body class="bg-light p-5">

    <div class="container bg-white p-4 rounded shadow">
        <!-- Top Controls -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSponsorModal">Add New Data</button>

            <div class="d-flex gap-2" style="width:12rem;">
                <select id="sortBySponsor" class="form-select">
                <option hidden disabled selected>Filter by Category</option>

                    <option value="a">Mass Sponsor</option>
                    <option value="b">Middle Sponsor</option>
                    <option value="c">Major Sponsor</option>

                </select>
            </div>

            <input type="text" id="search" class="form-control w-25 ms-auto" placeholder="Search for sponsors...">
        </div>

        <div id="selectionInfo" class="alert alert-danger d-none d-flex align-items-center justify-content-between mb-3">
      <span><span id="selectedCount">0</span> Selected</span>
      <div>
        <button id="downloadSelected" class="btn btn-outline-primary btn-sm me-2"> Download</button>
        <!-- <button id="deleteSelected" class="btn btn-outline-danger btn-sm"> Delete</button> -->
      </div>
    </div>

    <!-- Select All Checkbox (for table with checkboxes) -->
    <div class="d-flex align-items-center gap-2 mb-3">
      <input type="checkbox" id="selectAll" /> <label for="selectAll">Select All</label>
    </div>
                <!-- Sponsor Table -->
                <div class="table-container">
      <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
              <th>Select</th>
              <th style="display:flex; justify-content: space-between; align-items: center;">Child Code
                <div style=" display: inline-flex; flex-direction: column; padding: 4px; border-radius: 4px; background-color: white;" id="sortButton">
                  <button style="background: none; border: none; font-size: 8px; cursor: pointer; padding: 0; line-height: 0.8;">▲</button>
                  <button style="background: none; border: none; font-size: 8px; cursor: pointer; padding: 0; line-height: 0.8;">▼</button>
                </div>
              </th>
              <th>Sponsor ID</th>
              <th>Sponsor Name</th>
              <th>Year</th> <!-- New Year Column -->
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        <tbody id="sponsorTable">
          <!-- Data populated via JS -->
        </tbody>
      </table>

        <div id="pagination" class="d-flex gap-2 mt-3"></div>
    </div>

    <!-- Bootstrap Modal for "Add New Data" with Drag & Drop -->
<div class="modal fade" id="addSponsorModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Upload Your Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Year and Month Dropdowns -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="yearDropdown" class="form-label">Year</label>
                        <select id="yearDropdown" class="form-select">
                            <!-- Populate years dynamically using JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="monthDropdown" class="form-label">Month</label>
                        <select id="monthDropdown" class="form-select">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                </div>

                <p class="text-muted">Files should be <strong>.xlsx</strong></p>

                <div id="fileDropzone" class="dropzone">
                    @csrf
                    <div class="dz-message">
                        <p>Drag and drop files here</p>
                    </div>
                </div>
                <ul id="fileList" class="file-list"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="cancelUploadButton" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadButton">Upload</button>
            </div>
        </div>
    </div>
</div>

    <!-- Upload Success Modal -->
    <div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content p-3">
              <div class="d-flex justify-content-between">
                  <h5>Upload Summary</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <p class="text-muted">Here is the summary of your upload:</p>

              <!-- Upload Summary Details -->
              <div class="mb-3" id="uploadSummary">
                  <p>Successful Uploads: <span id="successfulUploads" class="fw-bold">0</span></p>
                  <p>Error Uploads: <span id="errorUploads" class="fw-bold">0</span></p>
                  <p>Partial Uploads: <span id="partialUploads" class="fw-bold">0</span></p>
              </div>

              <!-- List of Uploaded Files (Optional) -->
              <!-- <ul id="fileSuccessList" class="file-list"></ul> -->

              <!-- Download Button (Optional) -->
              <!-- <button class="btn btn-primary w-100">Download Report</button> -->
          </div>
      </div>
  </div>
    <!-- <div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="d-flex justify-content-between">
                    <h5>Upload Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class="text-muted">You could download or exit</p>

                <ul id="fileSuccessList" class="file-list"></ul>

                <p id="moreFilesText" class="text-muted" style="display: none;"></p>

                <button class="btn btn-primary w-100">Download</button>
            </div>
        </div>
    </div> -->

    <!-- Bootstrap & Dropzone JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    let url_params = {};
    let child_data = []
    let selectedItems = new Map();
    let sortOrder = "asc";

    $(document).ready(function() {
        // $("#selectAll").prop("disabled", true)
        // $("#selectAll").prop("checked", false);
        populateChildrenTable();
        $("#sortBySponsor").prop("selectedIndex", 0);
        selectedItems = new Map();
        child_data = [];
        url_params = {};
    });

    $("#sortBySponsor").change(function() {
        var sponsorMapping = {
            "Mass Sponsor": 1,
            "Middle Sponsor": 2,
            "Major Sponsor": 3
        };
 
        var selectedText = $("#sortBySponsor option:selected").text();
        var mappedValue = sponsorMapping[selectedText] || 0

        filterSponsor(mappedValue).then(()=>{
            allSelectedFalse()
            updateSelectionInfo()
        }

        );

    });

    $(document).on("change", ".row-checkbox", function() {
    let itemId = $(this).data("id");
    if (this.checked) {
        selectedItems.get(itemId).selected = true
    } else {
        selectedItems.get(itemId).selected = false
    }
    updateSelectionInfo()

    });



  function updateSelectAllCheckbox() {
    $("#selectAll").prop("checked", selectedItems.size === child_data.length);
    }

function allSelectedFalse(){
    selectedItems.forEach((item)=>{
        item.selected = false})
}


$(document).on("click", "#downloadSelected", function() {
    const selectedChildData = [];

    // Loop melalui child_data untuk memeriksa item yang dipilih
    child_data.forEach((data, index) => {
        const item = selectedItems.get(index);

        if (item && item.selected) {
            selectedChildData.push(data);
        }
    });

    // // Buat objek JSON dari selectedChildData
    const jsonData = {
        child_code: selectedChildData.map(item => item.child_code)
    };

    // // Panggil fungsi handleDownload dengan JSON yang telah dibuat
    handleDownload(jsonData);
});

    $("#sortButton").on("click", function (e) {
        e.stopPropagation();
        console.log(sortOrder)
        sortOrder = sortOrder === "desc" ? "asc" : "desc";
        sortData(sortOrder)

    });

$('#search').on('keyup', function () {
    let query = $(this).val();
    Object.assign(url_params, {search_query : query});
    populateChildrenTable()
});

window.populateChildrenTable = async function (){
    $.ajax({
        url: "api/results",
        type: "GET",
        data: url_params,
        dataType: "json",
        success : function(data){
            selectedItems.clear()
            child_data = data;

            for(let i = 0; i < child_data.length; ++i){
                selectedItems.set(i, {selected: false });
            }
            renderChildrenTable();


        }
    })
}

const itemsPerPage = 10;
let currentPage = 1;

function renderChildrenTable() {
    const $tableBody = $('#sponsorTable');
    const $paginationContainer = $('#pagination');
    $tableBody.empty();
    $paginationContainer.empty();

    if (!child_data || child_data.length === 0) {
        renderEmptyTable();
        return;
    }

    let totalItems = child_data.length;
    let pageCount = Math.ceil(totalItems / itemsPerPage);
    let startIndex = (currentPage - 1) * itemsPerPage;
    let endIndex = startIndex + itemsPerPage;
    let paginatedChildren = child_data.slice(startIndex, endIndex);

    $.each(paginatedChildren, function(index, child) {
        const $row = $('<tr>');
        $row.append($('<td>').append(
            $('<input>', {
                type: 'checkbox',
                class: 'row-checkbox',
                'data-id': startIndex + index,
                checked: selectedItems.get(startIndex + index).selected
            })
        ));
        $row.append($('<td>').text(child.child_code));
        $row.append($('<td>').text(child.sponsor_name));
        $row.append($('<td>').text(child.sponsor_category));
        $row.append($('<td>').text(child.fiscal_year));

        const $actionsCell = $('<td>');

        const $downloadButton = $('<button>')
            .addClass('btn btn-primary btn-sm')
            .text('Download')
            .on('click', function() {
                const jsonData = {
                    child_code: child.child_code
                };
                handleDownload(jsonData);
            });

        const $deleteButton = $('<button>')
            .addClass('btn btn-danger btn-sm')
            .text('Delete')
            .on('click', function() {
                handleDelete(child.child_code);
            });

        $actionsCell.append($downloadButton).append(' ').append($deleteButton);
        $row.append($actionsCell);

        $tableBody.append($row);
    });

    for (let i = 1; i <= pageCount; i++) {
        let $pageButton = $('<button>', {
            text: i,
            class: `pagination-btn ${i === currentPage ? "active" : ""}`,
            click: function() {
                currentPage = i;
                renderChildrenTable();
            }
        });

        $paginationContainer.append($pageButton);
    }
}

async function handleDelete(child_code){
    if (!child_code) {
        console.error("child_code is required");
        return;
    }
    try {
        const response = await fetch(`api/delete?child_code=${encodeURIComponent(child_code)}`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
    } catch (error) {
        console.error("Deletion failed:", error.message);
    }
    populateChildrenTable()
}

/*
    handleDownload(json_data)

*/
async function handleDownload(json_data) {
    if (!json_data) {
        console.error("data is required");
        return;
    }
    try {
        // Kirim data JSON menggunakan metode POST
        const response = await fetch('api/download', {
            method: 'POST', // Gunakan POST untuk mengirim data JSON
            headers: {
                'Content-Type': 'application/json', // Tentukan tipe konten sebagai JSON
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Add CSRF token
            },
            body: JSON.stringify(json_data), // Konversi objek JSON ke string
        });

        console.log(response)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const blob = await response.blob();

        // Buat link sementara untuk download
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;

        // Ambil nama file dari header Content-Disposition
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = 'downloaded_file';
        if (contentDisposition) {
            const match = contentDisposition.match(/filename="(.+?)"/);
            if (match) filename = match[1];
        }

        link.download = filename; // Set nama file
        document.body.appendChild(link);
        link.click(); // Mulai download

        // Bersihkan
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);
    } catch (error) {
        // console.error("Download failed:", error);
        // alert("Failed to download file.");
    }
}

function renderEmptyTable() {
    $('#sponsorTable').html('<tr><td colspan="5" class="text-center">No data available</td></tr>');
}

async function filterSponsor(value){
    Object.assign(url_params, {category : value});
    window.populateChildrenTable()

}

async function sortData(value){
    Object.assign(url_params, {option : value});
    window.populateChildrenTable()
}

function updateSelectionInfo() {

    let count = 0
    selectedItems.forEach((item)=>{
        if(item.selected === true){
            count++
        }
    })

    $("#selectedCount").text(count)
    $("#selectionInfo").toggleClass("d-none", count === 0);
    console.log(count)
  }

$("#deleteSelected").on("click", ()=>{

})

// Add Select All functionality
$("#selectAll").on("change", function() {
    const isChecked = $(this).prop("checked");
    
    // Update all checkboxes
    $(".row-checkbox").prop("checked", isChecked);
    
    // Update selection state in map
    selectedItems.forEach((item, key) => {
        selectedItems.set(key, {selected: isChecked});
    });
    
    // Update selection info
    updateSelectionInfo();
});

// Populate year dropdown dynamically
$(document).ready(function() {
    const currentYear = new Date().getFullYear();
    const yearDropdown = $("#yearDropdown");
    
    // Add last 5 years
    for (let i = 0; i < 5; i++) {
        const year = currentYear - i;
        yearDropdown.append($("<option>", {
            value: year,
            text: year
        }));
    }
});
    </script>
</body>
</html>

</body>



</html>
