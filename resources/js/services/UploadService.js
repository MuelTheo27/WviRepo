import { createStatusModal, setListItems } from "../components/UploadStatusModal"

const successModal = new bootstrap.Modal(document.getElementById("uploadSuccessModal"));
const uploadModal = new bootstrap.Modal(document.getElementById("addSponsorModal"));

const errorList = [];
let listItemIds = [];
Dropzone.autoDiscover = false
var myDropzone = new Dropzone("div#fileDropzone", {
    url: "api/upload/xlsx",
    paramName: "file",
    maxFiles: null,  // Allows unlimited files
    acceptedFiles: ".xlsx",
    previewsContainer: null,  // Prevents Dropzone from adding previews
    createImageThumbnails: false,  // Disables image thumbnails
    addRemoveLinks: false,
    autoProcessQueue: false,
    uploadMultiple: true,
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

        $("#uploadButton").prop("disabled", true);

        dropzoneMessage.style.display = "block";

        this.on("addedfile", function (file) {
            $("#uploadButton").prop("disabled", false);
            if (file.previewElement) {
                file.previewElement.remove();
            }

            const itemId = file.upload.uuid

            let listItem = $("<li>", {
                id: itemId,
                class: 'success-file d-flex flex-column p-2',
                html: `<div class="d-flex justify-content-between align-items-center w-100"><div>${file.name}</div> <button class="remove-file" data-file-id="${itemId}">Remove</button></div>`
            });

   
            $('#fileList').append(listItem);

            listItemIds.push({ fileName: file.name, uuid: file.upload.uuid });

            $(document).on("click", ".remove-file", function() {
                const fileId = $(this).data('file-id');
                console.log(`Removing file with ID: ${fileId}`);
                
                listItemIds = listItemIds.filter(item => item.uuid !== fileId);
      
                const fileToRemove = dropzoneInstance.files.find(f => f.upload.uuid === fileId);
                if (fileToRemove) {
                    dropzoneInstance.removeFile(fileToRemove);
                }

                $(`#${fileId}`).remove();
                

                if (listItemIds.length === 0) {
                    $("#uploadButton").prop("disabled", true);
                }
            });
    

            $('#fileList').append(listItem);
            Object.assign(this.options.headers, { uploadId: itemId })

        });

        $('#addSponsorModal').on('hidden.bs.modal', function () {
            $("#fileList").empty();
            dropzoneInstance.removeAllFiles();
            listItemIds = [];
        

        });

        document.getElementById("uploadButton").addEventListener("click", async function () {
          
            dropzoneInstance.processQueue();
            createStatusModal(listItemIds);
            uploadModal.hide()

        });

    },
    sending: function (file, xhr, formData) {
        formData.append("fileId", JSON.stringify(listItemIds));
        formData.append("fiscalYear", 2024);

    },
    success: function (file, response) {
        console.log(response)

        // let uploadModal = bootstrap.Modal.getInstance(document.getElementById("addSponsorModal"));
        // uploadModal.hide();
        // this.removeFile(file)
        // successModal.show();
        // $("#uploadButton").prop("disabled", false);
        // window.populateChildrenTable()
    },
    error: function (file, response) {
        console.log(response)
    }
})


function closeUploadModal(){
    uploadModal.hide();
    listItemIds = [];
    myDropzone.removeAllFiles();
}