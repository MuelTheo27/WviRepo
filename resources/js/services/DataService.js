
export let tableData = [];
export let url_params = {};

export function fetchTableData() {
    console.log(url_params)
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "api/results",
            type: "GET",
            data: url_params,
            dataType: "json",
            success: function(data) {
                try {
                    tableData.length = 0;
                    data.forEach((item, index) => {
                        tableData.push({
                            id: index,
                            child_idn: item.child_idn,
                            sponsor_id: item.sponsor_id,
                            sponsor_name: item.sponsor_name,
                            sponsor_category: item.sponsor_category,
                            fiscal_year: item.fiscal_year
                        });
                    });
                    resolve(tableData);
                } catch (error) {
                    reject(error);
                }
            },
            error: function(xhr, status, error) {
                console.error("API request failed:", error);
                reject(error);
            }
        });
    });
}
