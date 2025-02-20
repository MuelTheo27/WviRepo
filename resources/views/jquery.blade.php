<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>

<script>

    var pdfLinks : string[] = [];
    var pdfPreview : string[] = [];
    var fileAmount = 0;
    
    let downloadPercentage = 0;


    $.post("", {}, (data : {uploadResponse : {pdfPreview : string[], fileAmount : number, pdfLinks : string[]}})=>{
        pdfLinks = data.uploadResponse.childCodes;
        pdfPreview = data.uploadResponse.pdfPreview;
        fileAmount = data.uploadResponse.fileAmount;
    })

   
    $("button").on("click", (e) => {
        e.preventDefault();
        
    })

    function downloadFromURI(uri : string){
       $.get(uri, )
    }

</script>
</html>