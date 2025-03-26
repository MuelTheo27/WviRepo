import {setLoadingState} from '../state/setLoadingState';
import {fetchTableData} from '../services/DataService';
import {handleDelete} from '../services/DeleteService';
import {selectedIds, updateSelectAllOptionBox } from './selectAll';
import {handleDownload} from '../services/DownloadService';
let tableData = [];
const itemsPerPage = 10;
let currentPage = 1;

 function renderTableAfterFetch(){
    setLoadingState(true);

    fetchTableData()
            .then((data) => {
                tableData = data;
                renderTable();
               
            })
            .catch(error => {
                console.error("Error fetching table data:", error);
            })
            .finally(() => {
                setLoadingState(false);
                
            });
};

function renderEmptyTable() {
    $('#sponsorTable').html('<tr><td colspan="7" class="text-center">No data available</td></tr>');
}

function renderTable() {
    const $tableBody = $('#sponsorTable');
    const $paginationContainer = $('#pagination');
    $tableBody.empty();
    $paginationContainer.empty();

    if (!tableData || tableData.length === 0) {
        renderEmptyTable();
        return;
    }

    let totalItems = tableData.length;
    let pageCount = Math.ceil(totalItems / itemsPerPage);
    let startIndex = (currentPage - 1) * itemsPerPage;
    let endIndex = startIndex + itemsPerPage;
  
    let paginatedChildren = tableData.slice(startIndex, endIndex);

    $.each(paginatedChildren, function(index, child) {
        const $row = $('<tr>');
        const $checkbox = $('<input>', {
            type: 'checkbox',
            class: 'row-checkbox',
            'data-id': startIndex + index,
            checked: selectedIds.has(child.id),
        });

        $checkbox.on("change", function () {
            let isChecked = $(this).prop("checked"); 
            let id = $(this).data("id"); 
        
            if (isChecked) {
                selectedIds.add(id); 
            } else {
                selectedIds.delete(id); 
            }
            if (selectedIds.size === 0) {
                $("#selectAll").prop("checked", false);
            }
            console.log(selectedIds)
            updateSelectAllOptionBox(); 
        });
        
        // Append checkbox to row
        $row.append($('<td>').append($checkbox));
        $row.append($('<td>').text(child.child_idn));
        $row.append($('<td>').text(child.sponsor_id));
        $row.append($('<td>').text(child.sponsor_name));
        $row.append($('<td>').text(child.sponsor_category));
        $row.append($('<td>').text(child.fiscal_year));
        const $actionsCell = $('<td>');

        const $downloadButton = $('<button>')
            .addClass('btn btn-primary btn-sm')
            .text('Download')
            .on('click', function() {
                const jsonData = {
                    child_idn: child.child_idn
                };
                handleDownload(jsonData);
            });

        const $deleteButton = $('<button>')
            .addClass('btn btn-danger btn-sm')
            .text('Delete')
            .on('click', function() {
                handleDelete({child_idn : [child.child_idn]});
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
                renderTable();
            }
        });

        $paginationContainer.append($pageButton);
    }
}

export {
    renderTableAfterFetch,
    renderTable,
    renderEmptyTable,
    currentPage,
    itemsPerPage,
    tableData
};