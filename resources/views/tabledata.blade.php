<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sponsor Table</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* Dropzone & File List Styles */
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
      <div class="d-flex gap-2">
        <select id="sortByDate" class="form-select">
          <option value="newest">Sort by Newest</option>
          <option value="oldest">Sort by Oldest</option>
        </select>
        <select id="sortBySponsor" class="form-select">
          <option value="a">Sort by Sponsor A</option>
          <option value="b">Sort by Sponsor B</option>
          <option value="c">Sort by Sponsor C</option>
        </select>
      </div>
      <input type="text" id="search" class="form-control w-25 ms-auto" placeholder="Search for sponsors..." />
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
            <th>Child Code</th>
            <th>Sponsor ID</th>
            <th>Sponsor Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="sponsorTable">
          <!-- Data populated via JS -->
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="d-flex gap-2 mt-3"></div>
  </div>

  <!-- Modal: Add New Data (Dropzone Upload) -->
  <div class="modal fade" id="addSponsorModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Upload Your Files</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted">Files should be <strong>.xlsx</strong></p>
          <!-- Dropzone Form -->
          <form action="#" class="dropzone" id="fileDropzone">
            <div class="dz-message">
              <p>Drag and drop files here</p>
            </div>
          </form>
          <ul id="fileList" class="file-list"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="uploadButton">Upload</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and Dropzone JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

  <script>
    // Global Data: sponsors array and selected items set
    let sponsors = [];
    let selectedItems = new Set();
    const itemsPerPage = 10;
    let currentPage = 1;

    // For demo purposes, generate 27 dummy sponsor entries
    for (let i = 1; i <= 27; i++) {
      sponsors.push({
        id: `CHILD-${i}`,
        sponsor_id: `SPON-${i}`,
        sponsor_name: `Sponsor ${i}`,
        status: 'Uploaded Successfully'
      });
    }

    // Render Sponsor Table with pagination, checkboxes, delete buttons, etc.
    function renderTable(page) {
      const tableBody = document.getElementById("sponsorTable");
      tableBody.innerHTML = "";
      // Apply search filter if needed
      let query = document.getElementById("search").value.toLowerCase();
      let filtered = sponsors.filter(sponsor =>
        sponsor.id.toLowerCase().includes(query) ||
        sponsor.sponsor_id.toLowerCase().includes(query) ||
        sponsor.sponsor_name.toLowerCase().includes(query)
      );
      // Pagination calculations
      let start = (page - 1) * itemsPerPage;
      let paginatedItems = filtered.slice(start, start + itemsPerPage);
      
      paginatedItems.forEach((sponsor) => {
        let row = document.createElement("tr");
        row.innerHTML = `
          <td><input type="checkbox" class="row-checkbox" data-id="${sponsor.id}" ${selectedItems.has(sponsor.id) ? "checked" : ""}></td>
          <td>${sponsor.id}</td>
          <td>${sponsor.sponsor_id}</td>
          <td>${sponsor.sponsor_name}</td>
          <td>${sponsor.status || ""}</td>
          <td>
            <button class="btn btn-primary btn-sm">Download</button>
            <button class="btn btn-danger btn-sm deleteBtn" data-id="${sponsor.id}">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
      attachCheckboxListeners();
      attachDeleteListeners();
      updateSelectAllState();
    }

    // Render Pagination Buttons based on filtered data
    function renderPagination() {
      const paginationContainer = document.getElementById("pagination");
      paginationContainer.innerHTML = "";
      let query = document.getElementById("search").value.toLowerCase();
      let filtered = sponsors.filter(sponsor =>
        sponsor.id.toLowerCase().includes(query) ||
        sponsor.sponsor_id.toLowerCase().includes(query) ||
        sponsor.sponsor_name.toLowerCase().includes(query)
      );
      let pageCount = Math.ceil(filtered.length / itemsPerPage);
      for (let i = 1; i <= pageCount; i++) {
        let pageItem = document.createElement("button");
        pageItem.innerText = i;
        pageItem.className = `pagination-btn ${i === currentPage ? "active" : ""}`;
        pageItem.addEventListener("click", function () {
          currentPage = i;
          renderTable(currentPage);
          renderPagination();
        });
        paginationContainer.appendChild(pageItem);
      }
    }

    // Attach delete listeners for Delete buttons
    function attachDeleteListeners() {
      document.querySelectorAll(".deleteBtn").forEach((button) => {
        button.addEventListener("click", function () {
          let sponsorId = this.getAttribute("data-id");
          sponsors = sponsors.filter((sponsor) => sponsor.id !== sponsorId);
          // Also remove from selected items set if present
          selectedItems.delete(sponsorId);
          renderTable(currentPage);
          renderPagination();
        });
      });
    }

    // Attach checkbox listeners for each row
    function attachCheckboxListeners() {
      document.querySelectorAll(".row-checkbox").forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
          let itemId = this.getAttribute("data-id");
          if (this.checked) {
            selectedItems.add(itemId);
          } else {
            selectedItems.delete(itemId);
          }
          updateSelectAllState();
        });
      });
    }

    // Update "Select All" checkbox state
    function updateSelectAllState() {
      const selectAllCheckbox = document.getElementById("selectAll");
      let checkboxes = document.querySelectorAll(".row-checkbox");
      let allChecked = Array.from(checkboxes).every((checkbox) => checkbox.checked);
      selectAllCheckbox.checked = allChecked && checkboxes.length > 0;
    }

    // "Select All" checkbox functionality
    document.getElementById("selectAll").addEventListener("change", function () {
      if (this.checked) {
        sponsors.forEach((sponsor) => selectedItems.add(sponsor.id));
      } else {
        selectedItems.clear();
      }
      renderTable(currentPage);
    });

    // Search functionality (live filter)
    document.getElementById("search").addEventListener("keyup", function () {
      currentPage = 1;
      renderTable(currentPage);
      renderPagination();
    });

    // Initial Render
    renderTable(currentPage);
    renderPagination();

    // Dropzone Setup for File Upload (Drag & Drop)
    Dropzone.options.fileDropzone = {
      paramName: "file",
      maxFiles: null,
      acceptedFiles: ".xlsx",
      previewsContainer: null,
      createImageThumbnails: false,
      addRemoveLinks: false,
      autoProcessQueue: false,
      init: function () {
        let dropzoneInstance = this;
        let fileList = document.getElementById("fileList");
        let dropzoneMessage = document.querySelector(".dz-message");
        dropzoneMessage.style.display = "block";
        let uploadedFiles = [];
  
        this.on("addedfile", function (file) {
          // Remove default preview if exists
          if (file.previewElement) {
            file.previewElement.remove();
          }
          uploadedFiles.push(file);
          let listItem = document.createElement("li");
          listItem.innerHTML = `${file.name} <button class="remove-file">Remove</button>`;
          listItem.querySelector(".remove-file").addEventListener("click", () => {
            dropzoneInstance.removeFile(file);
            listItem.remove();
            uploadedFiles = uploadedFiles.filter(f => f !== file);
          });
          fileList.appendChild(listItem);
        });
  
        // When Upload button is clicked inside the modal
        document.getElementById("uploadButton").addEventListener("click", function () {
          if (uploadedFiles.length === 0) return;
          // Clear the file list (will be re-populated with status indicators)
          $("#fileList").empty();
          // Process each file and simulate upload (50% success)
          uploadedFiles.forEach(file => {
            let isSuccess = Math.random() > 0.5;
            let listItem = document.createElement("li");
            listItem.innerHTML = isSuccess
              ? `<span class="text-success fw-bold">${file.name} - Uploaded Successfully</span>`
              : `<span class="text-danger fw-bold">${file.name} - Upload Failed</span>`;
            listItem.classList.add(isSuccess ? "success-file" : "error-file");
            fileList.appendChild(listItem);
            // On success, add a new sponsor entry to the table
            if (isSuccess) {
              let newIndex = sponsors.length + 1;
              sponsors.push({
                id: `CHILD-${newIndex}`,
                sponsor_id: `SPON-${newIndex}`,
                sponsor_name: file.name.replace(".xlsx", ""),
                status: "Uploaded Successfully"
              });
            }
          });
          // Clear uploadedFiles array after processing
          uploadedFiles = [];
          // Update table and pagination
          renderTable(currentPage);
          renderPagination();
        });
      }
    };
  </script>
</body>
</html>
