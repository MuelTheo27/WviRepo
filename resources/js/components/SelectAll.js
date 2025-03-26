import { mappedValue } from "./FilterButton";
import { tableData, renderTable } from "./TableData";
import { handleDownload } from "../services/DownloadService";
import { selectedFiscalYear } from "./FilterYear";
let selectedIds = new Set();

export function disableSelectAll(){
    $("#selectAll").prop("disabled", true);
}

export function enableSelectAll(){ 
    $("#selectAll").prop("disabled", false);
}

export function resetSelectAllOptionBox(){
    selectedIds.clear();
    $("#selectedCount").text(0);
    $("#selectionInfo").hide();
}


    /* click -> checklist all items -> display a box */
    $(document).on("change", "#selectAll", function() {
        let isChecked = this.checked;
        if(isChecked){
            tableData.forEach((item)=>{
                selectedIds.add(item.id);
            });
        }
        else{
            selectedIds.clear();
        }
        renderTable();
        updateSelectAllOptionBox();
        
    });  

    export function updateSelectAllOptionBox() {
        if (selectedIds.size > 0) {
            $("#selectedCount").text(selectedIds.size);
        }
        
        // Show/hide download button based on conditions
        if (mappedValue !== 0 && selectedFiscalYear !== "") {
            $("#downloadSelected").removeClass("d-none"); // Show
            $("#downloadSelected").prop("disabled", false); // Enable
        } else {
            $("#downloadSelected").addClass("d-none"); // Hide
        }
        
        // Toggle selection info based on selected items count
        $("#selectionInfo").toggleClass("d-none", selectedIds.size === 0);
    }

$(document).on("click", "#downloadSelected", function() {
    const selectedData = [];

    tableData.forEach((data) => {
        if (selectedIds.has(data.id)) {  // ✅ Check if `data.id` is selected
            selectedData.push(data);
        }
    });

    const jsonData = {
        child_idn: selectedData.map(item => item.child_idn)
    };

    // handleDownload(jsonData);
});


export {selectedIds}