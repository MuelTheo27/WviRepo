import { resetSelectAllOptionBox, enableSelectAll } from "./selectAll";
import { renderTableAfterFetch } from "./TableData";
import { url_params } from "../services/DataService";
import { selectedIds } from "./selectAll";
const sponsorMapping = {
    "No Filter" : 0,
    "Mass Sponsor": 1,
    "Middle Sponsor": 2,
    "Major Sponsor": 3,
    "Hardcopy" : 4
};

export let mappedValue = 0

$("#sortBySponsor").change(function() {
    var selectedText = $("#sortBySponsor option:selected").text();
    mappedValue = sponsorMapping[selectedText]
       
    
    Object.assign(url_params, {category : mappedValue});
    

    renderTableAfterFetch();        
        
    })

