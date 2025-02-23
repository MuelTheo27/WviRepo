<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
            background: #eef;
            margin-bottom: 5px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
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

            <input type="text" id="search" class="form-control w-25 ms-auto" placeholder="Search for sponsors...">
        </div>

                <!-- Sponsor Table -->
                        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Child Code</th>
                    <th>Sponsor ID</th>
                    <th>Sponsor Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="sponsorTable">
                @if(isset($children) && count($children) > 0)
                    @foreach ($children as $list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->sponsor_id }}</td>
                            <td>{{ $list->sponsor_name }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Download</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" class="text-center">No data available</td></tr>
                @endif
            </tbody>

        </table>

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

                    <!-- Dropzone Upload -->
                    <form action="{{ route('upload.xlsx') }}" class="dropzone" id="fileDropzone">
                        @csrf
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

    <!-- Upload Success Modal -->
    <div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="d-flex justify-content-between">
                    <h5>Upload Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class="text-muted">You could download or exit</p>

                <ul id="fileSuccessList" class="file-list"></ul>
                <p id="moreFilesText" class="text-muted" style="display: none;"></p>

                <a href="https://d3cfrqjucqh0ip.cloudfront.net/child/pdf/202849-QTQW_20241022_150747_CCS.pdf" class="btn btn-primary w-100" download>Download PDF</a>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        Dropzone.options.fileDropzone = {
            paramName: "file",
            maxFiles: null,
            acceptedFiles: ".xlsx",
            previewsContainer: null,
            createImageThumbnails: false,
            addRemoveLinks: false,

            init: function () {
                let dropzoneInstance = this;
                let fileList = document.getElementById("fileList");
                let dropzoneMessage = document.querySelector(".dz-message");
                let successModal = new bootstrap.Modal(document.getElementById("uploadSuccessModal"));
                let fileSuccessList = document.getElementById("fileSuccessList");
                let moreFilesText = document.getElementById("moreFilesText");

                dropzoneMessage.style.display = "block";

                let uploadedFiles = [];

                this.on("addedfile", function (file) {
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }

                    uploadedFiles.push(file.name);

                    let listItem = document.createElement("li");
                    listItem.innerHTML = `${file.name} <button class="remove-file">Remove</button>`;
                    listItem.querySelector(".remove-file").addEventListener("click", () => {
                        dropzoneInstance.removeFile(file);
                        listItem.remove();
                        uploadedFiles = uploadedFiles.filter(f => f !== file.name);
                    });

                    fileList.appendChild(listItem);
                });

                document.getElementById("uploadButton").addEventListener("click", function () {
                    $("#fileList").empty();
                    uploadedFiles = [];
                    let uploadModal = bootstrap.Modal.getInstance(document.getElementById("addSponsorModal"));
                    uploadModal.hide();

                    fileSuccessList.innerHTML = "";
                    let totalFiles = uploadedFiles.length;
                    let displayedFiles = uploadedFiles.slice(0, 5);
                    let remainingCount = totalFiles - displayedFiles.length;

                    displayedFiles.forEach(file => {
                        let item = document.createElement("li");
                        item.textContent = file;
                        fileSuccessList.appendChild(item);
                    });

                    moreFilesText.innerText = remainingCount > 0 ? `${remainingCount} files more available` : "";
                    moreFilesText.style.display = remainingCount > 0 ? "block" : "none";

                    successModal.show();
                });
            }
        };
    </script>

</body>
    <script>
        $(document).ready(function () {
            $('#search').on('keyup', function () {
                let query = $(this).val();

                $.ajax({
                    url: "{{ route('sponsors.search') }}",
                    type: "GET",
                    data: { query: query },
                    dataType: "json",
                    success: function (data) {
                        let tbody = $("#sponsorTable");
                        tbody.empty(); // Clear previous results

                        if (data.children.length > 0) {
                            data.children.forEach(function (sponsor) {
                                tbody.append(`
                                    <tr>
                                        <td>${sponsor.id}</td>
                                        <td>${sponsor.sponsor_id ?? 'N/A'}</td>
                                        <td>${sponsor.sponsor_name}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Download</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            tbody.append(`<tr><td colspan="4" class="text-center">No sponsors found</td></tr>`);
                        }
                    },
                    error: function (xhr) {
                        console.log("Error: ", xhr.responseText);
                    }
                });
            });
        });
    </script>


</html>
