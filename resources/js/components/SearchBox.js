import {renderTableAfterFetch} from './TableData';
import {url_params} from '../services/DataService';
import { sortOrder } from './SortButton';

$('#search').on('keyup', ()=>{
    handleSearch($('#search')[0].value)
  
});

export function handleSearch(query){
    Object.assign(url_params, {search_query : query});
   
    renderTableAfterFetch();
}