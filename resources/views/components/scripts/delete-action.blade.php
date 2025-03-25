
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('data-table-delete', async function (event) {
        const { index } = event.detail;

        const row = document.querySelector(`tr[data-index="${index}"]`)

      
        const childId = row ? row.cells[1].textContent.trim() : index;

        if (!confirm(`Are you sure you want to delete Child ID: ${childId}?`)) {
            return;
        }

        try {
            console.log("deleting = " , childId)
            // const params = {
            //     child_idn: index
            // };

            // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // const response = await fetch('/api/delete', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': csrfToken
            //     },
            //     body: JSON.stringify(params)
            // });

            // const result = await response.json();

            // if (response.ok) {
            //     console.log(`Child ID: ${childId} deleted successfully.`);
                
            //     const row = document.querySelector(`tr[data-index="${index}"]`);
            //     if (row) {
            //         row.remove();
            //     }

            //     document.dispatchEvent(new CustomEvent('row-deleted', {
            //         detail: { childId, index }
            //     }));
            // } else {
            //     console.error(`Failed to delete Child ID: ${childId}.`, result.message || result);
            //     alert(`Error: ${result.message || 'Failed to delete the record.'}`);
            // }
        } catch (error) {
            console.error(`An error occurred while deleting Child ID: ${childId}.`, error);
            alert('An unexpected error occurred. Please try again.');
        }
    });
});

</script>