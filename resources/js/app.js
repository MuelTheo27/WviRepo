import './bootstrap';
import { disableSelectAll,  } from './components/selectAll';
import { renderTableAfterFetch } from './components/TableData';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { initFiscalYearFilter } from './components/FilterYear';
window.Alpine = Alpine;

Alpine.start();

$(document).ready(function() {
    $('#sortBySponsor').val('a');
    renderTableAfterFetch();
    initFiscalYearFilter();
});



window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: window.appConfig.pusherKey,
//     cluster: window.appConfig.pusherCluster,
//     forceTLS: true,
//     authEndpoint: '/broadcasting/auth'
// });