
@php

$currentPage = $currentPage ?? 0;
$pageSize = $pageSize ?? 10;
$totalItems = $totalItems ?? 0;
$totalPages = ceil($totalItems / $pageSize);


$showingFrom = $totalItems > 0 ? min($totalItems, 1 + $currentPage * $pageSize) : 0;
$showingTo = min($totalItems, ($currentPage + 1) * $pageSize);
@endphp

<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        Showing {{ $showingFrom }} to {{ $showingTo }} of {{ $totalItems }} entries
    </div>
    
    @if($totalPages > 0)
        <div class="btn-group">
            <button
                class="btn btn-outline-secondary"
                id="prevPage"
                {{ $currentPage <= 0 ? 'disabled' : '' }}
            >
                Previous
            </button>
            
            @for($i = 0; $i < min(5, $totalPages); $i++)
                @php
                    // Calculate which page number to show (matching React logic)
                    if ($currentPage <= 2) {
                        $pageNum = $i;
                    } elseif ($currentPage >= $totalPages - 3) {
                        $pageNum = $totalPages - 5 + $i;
                    } else {
                        $pageNum = $currentPage - 2 + $i;
                    }
                @endphp
                
                @if($pageNum < $totalPages && $pageNum >= 0)
                    <button
                        class="btn {{ $currentPage == $pageNum ? 'btn-primary' : 'btn-outline-secondary' }}"
                        data-page="{{ $pageNum }}"
                    >
                        {{ $pageNum + 1 }}
                    </button>
                @endif
            @endfor
            
            <button
                class="btn btn-outline-secondary"
                id="nextPage"
                {{ $currentPage >= $totalPages - 1 ? 'disabled' : '' }}
            >
                Next
            </button>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = {{ $currentPage }};
    const pageSize = {{ $pageSize }};
    const totalItems = {{ $totalItems }};
    const totalPages = {{ $totalPages }};
    
    // Event listeners for pagination buttons
    document.getElementById('prevPage')?.addEventListener('click', function() {
        if (currentPage > 0) {
            changePage(currentPage - 1);
        }
    });
    
    document.getElementById('nextPage')?.addEventListener('click', function() {
        if (currentPage < totalPages - 1) {
            changePage(currentPage + 1);
        }
    });
    
    document.querySelectorAll('.btn-group .btn:not(#prevPage):not(#nextPage)').forEach(button => {
        button.addEventListener('click', function() {
            const pageNum = parseInt(this.getAttribute('data-page'));
            changePage(pageNum);
        });
    });
    
    // Function to handle page changes
    function changePage(newPage) {
        updateActiveButtonStyles(newPage);
        
        const pageChangeEvent = new CustomEvent('pagination-change', {
            detail: { 
                previousPage: currentPage,
                currentPage: newPage,
                pageSize: pageSize
            }
        });
        
        currentPage = newPage;
        
        document.dispatchEvent(pageChangeEvent);
    }
    
    function updateActiveButtonStyles(newPage) {
        document.querySelectorAll('.btn-group .btn:not(#prevPage):not(#nextPage)').forEach(button => {
            button.classList.remove('btn-primary');
            button.classList.add('btn-outline-secondary');
        });
        
        const activeButton = document.querySelector(`.btn-group .btn[data-page="${newPage}"]`);
        if (activeButton) {
            activeButton.classList.remove('btn-outline-secondary');
            activeButton.classList.add('btn-primary');
        }
        
        document.getElementById('prevPage').disabled = (newPage <= 0);
        document.getElementById('nextPage').disabled = (newPage >= totalPages - 1);
    }
    
    document.addEventListener('update-pagination', function(event) {
        const { totalItems, currentPage } = event.detail;
        
        updateActiveButtonStyles(currentPage);
    });
});
</script>