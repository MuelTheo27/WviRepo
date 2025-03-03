    let url_params = {};
    let child_data = []
    let selectedItems = new Map();
    let sortOrder = "asc";
    
    $(document).ready(function() {
        $("#selectAll").prop("disabled", true);
        populateChildrenTable();
        $("#sortBySponsor").prop("selectedIndex", 0);
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
$(document).on("change", "#selectAll", function() { 
   
    let isChecked = this.checked;
    if(isChecked){
    selectedItems.forEach((item)=>{
        item.selected = true})
        
    }

    else{
        selectedItems.forEach((item)=>{
        item.selected = false})
        
    }
    updateSelectionInfo()
    renderChildrenTable()
    

});

$(document).on("click", "#downloadSelected", function() {
    const selectedChildData = [];
  
   
    child_data.forEach((data, index) => {
 
      const item = selectedItems.get(index);
      
      if (item && item.selected) {
        handleDownload(data.child_code)
      }
    });

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
            $("#selectAll").prop("disabled", false);

        }
    })
}

const itemsPerPage = 1; 
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
                console.log(child.child_code);
                handleDownload(child.child_code);
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

async function handleDownload(child_code) {
    if (!child_code) {
        console.error("child_code is required");
        return;
    }

    try {
        // Fetch file from the backend
        const response = await fetch(`api/download?child_code=${encodeURIComponent(child_code)}`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const blob = await response.blob();

        // Create a temporary download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;

        // Extract filename from Content-Disposition header (if available)
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = 'downloaded_file';
        if (contentDisposition) {
            const match = contentDisposition.match(/filename="(.+?)"/);
            if (match) filename = match[1];
        }

        link.download = filename; // Set filename
        document.body.appendChild(link);
        link.click(); // Trigger the download

        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);
    } catch (error) {
        console.error("Download failed:", error);
        alert("Failed to download file.");
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
