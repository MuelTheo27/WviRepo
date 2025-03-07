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
                    <p class="text-muted">Files should be <strong>.xlsx</strong></p>

                    <div id="fileDropzone" class="dropzone"> 
                        @csrf
                        <div class="dz-message">
                          <p>Drag and drop files here</p>
                        </div></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite('resources/js/table.js')
    @vite('resources/js/upload.js')

</body>
    


</html>