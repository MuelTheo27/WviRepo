import { renderTableAfterFetch } from "./TableData";
import { url_params } from "../services/DataService";


let selectedFiscalYear = "";

$("#yearDropdown").change(function() {
    selectedFiscalYear = $("#yearDropdown option:selected").text();
    
    if(selectedFiscalYear === "No Filter"){
        selectedFiscalYear = "";
    }
    Object.assign(url_params, {fiscal_year : selectedFiscalYear});
    
    renderTableAfterFetch();        
        
})

export {selectedFiscalYear}