$(document).ready(function() {
    populateChildrenTable();
    
});

$("#sortBySponsor").change(function() {
    var sponsorMapping = {
        "Mass Sponsor": 1,
        "Middle Sponsor": 2,
        "Major Sponsor": 3
    };

    var selectedText = $("#sortBySponsor option:selected").text();
    var mappedValue = sponsorMapping[selectedText] || 0  
    console.log(mappedValue)
    filterSponsor(mappedValue);
});

$("#sortByDate").change(function() {
    var sponsorMapping = {
        "Mass Sponsor": 1,
        "Middle Sponsor": 2,
        "Major Sponsor": 3
    };

    var selectedOrder = $("#sortByDate option:selected").text();
    sortData(selectedOrder);
});

async function populateChildrenTable(){
    let tableData = await fetchDataTable("/api/data/index")
    console.log(tableData)
    if(tableData){
    renderChildrenTable(tableData)
    }
    else{
        renderEmptyTable()
    }
}

function fetchDataTable(api) {
   
    return axios.get(api)
        .then(function(response) {
         
            return response.data

        })
        .catch(function(error) {
            console.error('Error fetching children data:', error);
            return []
        });
  
}

function renderChildrenTable(children) {
    const $tableBody = $('#sponsorTable');
    
    // Clear existing content
    $tableBody.empty();
    
    if (children && children.length > 0) {
        $.each(children, function(index, child) {
            const $row = $('<tr>');

            $row.append($('<td>').text(child.child_code));
            $row.append($('<td>').text(child.sponsor_name));
            $row.append($('<td>').text(child.sponsor_category));
            $row.append($('<td>').text(child.fiscal_year));
            
            const $actionsCell = $('<td>');
            
            const $downloadButton = $('<button>')
                .addClass('btn btn-primary btn-sm')
                .text('Download')
                .on('click', function() {
                    console.log(child.child_code)
                    handleDownload(child.child_code);
                });
            
            const $deleteButton = $('<button>')
                .addClass('btn btn-danger btn-sm')
                .text('Delete')
                .on('click', function() {
                    handleDelete(child.child_code);
                });
            
            $actionsCell.append($downloadButton);
            $actionsCell.append(' '); 
            $actionsCell.append($deleteButton);
            
            $row.append($actionsCell);
          
            $tableBody.append($row);
        });
    } else {
        renderEmptyTable();
    }
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
    let tableData = await fetchDataTable(`api/data/filter?category=${value}`);
    renderChildrenTable(tableData);
}

async function sortData(value){
    let tableData = await fetchDataTable(`/api/data/sort?order=${value}`);
    renderChildrenTable(tableData)
}