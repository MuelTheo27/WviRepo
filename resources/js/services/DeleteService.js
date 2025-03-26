
export async function handleDelete(childIdn){
    if (!childIdn) {
        console.error("childIdn is required");  
        return;
    }
 
        const response = await fetch('api/delete', {
            method: 'POST', // Gunakan POST untuk mengirim data JSON
            headers: {
                'Content-Type': 'application/json', // Tentukan tipe konten sebagai JSON
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Add CSRF token
            },
            body: JSON.stringify(childIdn), // Konversi objek JSON ke string
        });

        console.log(response)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

}
