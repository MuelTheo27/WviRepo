<!-- resources/views/upload.blade.php -->
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

                <div class="mb-4">
                    <input type="file" id="fileInput" multiple class="form-input mt-1 block w-full" />
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
    <script>
        document.getElementById('uploadBtn').addEventListener('click', function () {
            const fileInput = document.getElementById('fileInput');
            const progressBar = document.getElementById('progress');
            const progressText = document.getElementById('progressText');
            const uploadProgressContainer = document.getElementById('uploadProgressContainer');

            if (fileInput.files.length > 0) {
                // Show progress bar
                uploadProgressContainer.classList.remove('hidden');
                
                // Simulate file upload progress
                let progress = 0;
                const interval = setInterval(() => {
                    if (progress < 100) {
                        progress += 10;
                        progressBar.style.width = progress + '%';
                        progressText.textContent = progress + '%';
                    } else {
                        clearInterval(interval);
                    }
                }, 500); // Simulate progress update every 0.5 seconds
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
