import { url_params } from "../services/DataService";
import { renderTableAfterFetch } from "./TableData";
let sortOrder;

$("#sortButton").on("click", (e) => handleSort(e));


function handleSort(e){

    e.stopPropagation();
        
    sortOrder = sortOrder === "desc" ? "asc" : "desc";
        
    Object.assign(url_params, {option : sortOrder});
        
    renderTableAfterFetch();
    
}

export {
    handleSort,
    sortOrder
}