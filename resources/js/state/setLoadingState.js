import { mappedValue } from "../components/FilterButton";
import { tableData } from "../components/TableData";
import { selectedIds, updateSelectAllOptionBox } from "../components/selectAll";
export function setLoadingState(loading) {
  
  
 
    $("#sortBySponsor").prop("disabled", loading);
    $("#sortButton").prop("disabled", loading);
    $(".row-checkbox").prop("disabled", loading);
    
    selectedIds.clear()
  
    const shouldDisableSelectAll = loading || (mappedValue !== null && mappedValue === 0);
    
    $(".row-checkbox").prop("disabled", shouldDisableSelectAll);
 
    if (loading || shouldDisableSelectAll) {
        
        $("#selectAll").prop("checked", false);
      
    }


    updateSelectAllOptionBox();
    
    
}