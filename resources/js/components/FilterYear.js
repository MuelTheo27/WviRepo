import { renderTableAfterFetch } from './TableData';

// Keep track of currently selected year
export let selectedFiscalYear = "";

export function populateFiscalYearDropdown() {
    const startYear = 2019;
    const endYear = 2025;
    
    const $yearDropdown = $("#yearDropdown");
    $yearDropdown.find("option:not(:first)").remove();
    
    for (let year = endYear; year >= startYear; year--) {
        $yearDropdown.append(
            $("<option>", {
                value: year,
                text: `FY ${year}`
            })
        );
    }
    
    if (selectedFiscalYear) {
        $yearDropdown.val(selectedFiscalYear);
    }
}
export function initFiscalYearFilter() {
    populateFiscalYearDropdown();
    
    // Handle changes to the dropdown
    $("#yearDropdown").on("change", function() {
        selectedFiscalYear = $(this).val();
        console.log("Fiscal year filter changed to:", selectedFiscalYear);
        renderTableAfterFetch();
    });
}