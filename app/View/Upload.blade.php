@extends('layouts.app')

@section('content')
    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 hidden">
        <div class="w-1/2 mx-auto mt-24 bg-white p-6 rounded-lg">
            <div class="modal-header flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold">Upload Your Files</h5>
                <button id="closeModal" class="text-gray-500">&times;</button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label for="fiscalYear" class="block text-sm font-semibold">Fiscal Year</label>
                    <select id="fiscalYear" class="form-select mt-1 block w-full">
                        <option value="2025">All Fiscal Year</option>
                        @foreach (range(2025, 2015) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="month" class="block text-sm font-semibold">Month</label>
                    <select id="month" class="form-select mt-1 block w-full">
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

                <!-- Dropzone for file uploads -->
                <div class="dropzone" id="fileDropzone" style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
                    <p>Drag and drop Excel files here (xlsx only)</p>
                </div>

                <div id="uploadProgressContainer" class="space-y-4 hidden">
                    <div class="progress-bar-container">
                        <div class="progress-bar bg-gray-300 rounded-full h-2 relative">
                            <div id="progress" class="bg-blue-600 rounded-full h-2 absolute top-0 left-0"></div>
                        </div>
                        <span id="progressText" class="mt-2 block text-sm text-center text-gray-600">0%</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-between items-center mt-4">
                <button id="uploadBtn" class="btn btn-primary py-2 px-4 bg-blue-500 text-white rounded-md">Start Upload</button>
                <button id="cancelBtn" class="btn btn-secondary py-2 px-4 bg-gray-500 text-white rounded-md">Cancel</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.7.0/dist/dropzone.js"></script>

    <script>
        // Initialize Dropzone
        Dropzone.autoDiscover = false;
        const fileDropzone = new Dropzone("#fileDropzone", {
            url: "/api/upload/xlsx", // The server endpoint to handle file upload
            paramName: "file", // The name of the file parameter sent to the server
            maxFilesize: 10, // Maximum file size in MB
            acceptedFiles: ".xlsx", // Accept only .xlsx files
            dictDefaultMessage: "Drag and drop Excel files here (xlsx only)",
            addRemoveLinks: true,
            init: function() {
                this.on("sending", function(file, xhr, formData) {
                    // You can add additional data to the formData if needed
                    const fiscalYear = document.getElementById('fiscalYear').value;
                    const month = document.getElementById('month').value;
                    formData.append('fiscal_year', fiscalYear);
                    formData.append('month', month);
                });

                this.on("uploadprogress", function(file, progress) {
                    const progressBar = document.getElementById('progress');
                    const progressText = document.getElementById('progressText');
                    const uploadProgressContainer = document.getElementById('uploadProgressContainer');

                    uploadProgressContainer.classList.remove('hidden');
                    progressBar.style.width = progress + '%';
                    progressText.textContent = Math.round(progress) + '%';
                });

                this.on("success", function(file, response) {
                    // Handle success here
                    console.log(response);
                });

                this.on("error", function(file, errorMessage) {
                    // Handle error here
                    console.error(errorMessage);
                });

                this.on("complete", function(file) {
                    // Hide progress bar after completion
                    setTimeout(() => {
                        document.getElementById('uploadProgressContainer').classList.add('hidden');
                    }, 1000);
                });
            }
        });

        // Close modal
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('uploadModal').classList.add('hidden');
        });

        document.getElementById('cancelBtn').addEventListener('click', function () {
            document.getElementById('uploadModal').classList.add('hidden');
        });
    </script>
@endpush
