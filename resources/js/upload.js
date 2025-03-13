
$("#uploadButton").prop("disabled", true);

let successModal = new bootstrap.Modal(document.getElementById("uploadSuccessModal"));


    Dropzone.autoDiscover = false
    var myDropzone = new Dropzone("div#fileDropzone", {
    url:"/api/upload/xlsx",
    paramName: "file",
    maxFiles: null,  // Allows unlimited files
    acceptedFiles: ".xlsx",
    previewsContainer: null,  // Prevents Dropzone from adding previews
    createImageThumbnails: false,  // Disables image thumbnails
    addRemoveLinks: false,
    autoProcessQueue: false,
    headers: {
        "X-CSRF-TOKEN": $('input[name="_token"]').val()
    },

    init: function () {
        let dropzoneInstance = this;
        let fileList = document.getElementById("fileList");
        let dropzoneMessage = document.querySelector(".dz-message");
        let fileSuccessList = document.getElementById("fileSuccessList");
        let moreFilesText = document.getElementById("moreFilesText");
        let totalFiles = 0;
        let successCount = 0;
        let failCount = 0;
        let partialSuccessCount = 0;
        dropzoneMessage.style.display = "block";

        let uploadedFiles = [];

        this.on("addedfile", function (file) {

            if (file.previewElement) {
                file.previewElement.remove();
            }

            let listItem = document.createElement("li");

            validateExcelContent(file).then((res) => {

                if(res.state === true){
                    listItem.innerHTML = `${file.name} <button class="remove-file">Remove</button>`;
                    listItem.querySelector(".remove-file").addEventListener("click", () => {
                        dropzoneInstance.removeFile(file);
                        listItem.remove();
                        uploadedFiles = uploadedFiles.filter(f => f !== file.name);
                    });
                    listItem.classList.add("success-file")
                    $("#uploadButton").prop("disabled", false);
                }else{
                    listItem.innerHTML = `<span class="text-danger fw-bold">${file.name} - Upload Failed : ${res.message}</span>`;
                    listItem.classList.add("error-file");
                    dropzoneInstance.removeFile(file);
                }

            });

            fileList.appendChild(listItem);
        });

        $('#addSponsorModal').on('hidden.bs.modal', function () {
            $("#fileList").empty();
            uploadedFiles = [];
            dropzoneInstance.removeAllFiles();
            $("#uploadButton").prop("disabled", false);

        });

        document.getElementById("uploadButton").addEventListener("click", function () {
            dropzoneInstance.processQueue()

            $("#uploadButton").prop("disabled", true);

        });

    },
    success : function(file, response){
        let uploadModal = bootstrap.Modal.getInstance(document.getElementById("addSponsorModal"));
        uploadModal.hide();
        this.removeFile(file)
        document.getElementById("uploadSummary").innerHTML = `
                <p>Total Uploaded: ${totalFiles}</p>
                <p class="text-success">Successful Uploads: ${successCount}</p>
                <p class="text-warning">Partial Uploads: ${partialSuccessCount}</p>
                <p class="text-danger">Failed Uploads: ${failCount}</p>
            `;

        successModal.show();
        $("#uploadButton").prop("disabled", false);
        window.populateChildrenTable()
    }
})


function validateExcelContent(file) {
    return new Promise((resolve, reject) => {
        let status = {
            message: "",
            state: true
        };

        const reader = new FileReader();

        reader.onload = function (e) {
            try {
                const data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data);
                var firstSheet = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[firstSheet];
                var excelRows = XLSX.utils.sheet_to_json(worksheet);

                if (
                    worksheet['A1'].v !== "child_code" ||
                    worksheet['B1'].v !== "sponsor_name" ||
                    worksheet['C1'].v !== "sponsor_category"
                ) {
                    status.message = "Column mismatch: Check table headers";
                    status.state = false;
                    return resolve(status);
                }

                // check semua cells
                for (const row of excelRows) {
                    if (!row["child_code"] || !row["sponsor_name"] || !row["sponsor_category"]) {
                        status.message = "Incomplete column data";
                        status.state = false;
                        return resolve(status);
                    }
                }
                resolve(status);
            } catch (error) {
                reject({ message: "Error processing file", state: false });
            }
        };

        reader.onerror = () => reject({ message: "Failed to read file", state: false });

        reader.readAsArrayBuffer(file);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Populate year dropdown
    const yearDropdown = document.getElementById("yearDropdown");
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 10; // Adjust the range as needed

    for (let year = currentYear; year >= startYear; year--) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        yearDropdown.appendChild(option);
    }
});
document.getElementById("uploadButton").addEventListener("click", function () {
    const selectedYear = document.getElementById("yearDropdown").value;
    const selectedMonth = document.getElementById("monthDropdown").value;

    console.log("Selected Year:", selectedYear);
    console.log("Selected Month:", selectedMonth);

    // Proceed with your upload logic
    dropzoneInstance.processQueue();
    $("#uploadButton").prop("disabled", true);
});
