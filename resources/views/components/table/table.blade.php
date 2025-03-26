

<div class="table-responsive">
    <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="selectAll"
                    />
                </th>
                <th class="sortable" data-sort="child_idn" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        Child Code
                        <div style="display: inline-flex; flex-direction: column; padding: 4px; border-radius: 4px; background-color: white;">
                            <span style="font-size: 8px; line-height: 0.8;">▲</span>
                            <span style="font-size: 8px; line-height: 0.8;">▼</span>
                        </div>
                    </div>
                </th>
                <th>Sponsor ID</th>
                <th>Sponsor Name</th>
                <th>Sponsor Category</th>
                <th>Fiscal Year</th>
                <th>Action</th>
            </tr>
        </thead>
        
        @if($isLoading ?? false)
            <tbody>
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data...</p>
                    </td>
                </tr>
            </tbody>
        @elseif(empty($data ?? []))
            <tbody>
                <tr>
                    <td colspan="7" class="text-center py-4">
                        No data available
                    </td>
                </tr>
            </tbody>
        @else
            <tbody id="sponsorTable">
                @foreach($data as $index => $row)
                    <tr data-index="{{ $index }}" data-id="{{ $row['id'] ?? $index }}">
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input row-checkbox"
                                data-index="{{ $index }}"
                            />
                        </td>
                        <td>{{ $row['child_idn'] ?? '' }}</td>
                        <td>{{ $row['sponsor_id'] ?? '' }}</td>
                        <td>{{ $row['sponsor_name'] ?? '' }}</td>
                        <td>{{ $row['sponsor_category'] ?? '' }}</td>
                        <td>{{ $row['fiscal_year'] ?? '' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                @if(($row['sponsor_category'] ?? '') == "Major Sponsor" || ($row['sponsor_category'] ?? '') == "Hardcopy")
                                    <button
                                        class="btn btn-sm btn-primary download-btn"
                                        data-index="{{ $row['child_idn'] }}"
                                    >
                                        Download
                                    </button>
                                @endif
                                <button
                                    class="btn btn-sm btn-danger delete-btn"
                                    data-index="{{ $row['child_idn']}}"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
   
    const sortableHeaders = document.querySelectorAll('th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort');
            const currentDirection = this.classList.contains('asc') ? 'desc' : 'asc';
            
            
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            
          
            this.classList.add(currentDirection);
       
            const sortEvent = new CustomEvent('data-table-sort', {
                detail: { key: sortKey, direction: currentDirection }
            });
            document.dispatchEvent(sortEvent);
        });
    });
    
    const downloadButtons = document.querySelectorAll('.download-btn');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            
            // Dispatch custom event
            const downloadEvent = new CustomEvent('data-table-download', {
                detail: { index: index }
            });
            document.dispatchEvent(downloadEvent);
        });
    });
    
    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            
            // Dispatch custom event
            const deleteEvent = new CustomEvent('data-table-delete', {
                detail: { index: index }
            });
            document.dispatchEvent(deleteEvent);
        });
    });
    
    // Dispatch a table-ready event when the table is fully initialized
    document.dispatchEvent(new CustomEvent('data-table-ready', {
        detail: { 
            rowCount: document.querySelectorAll('#sponsorTable tr').length 
        }
    }));
});
</script>