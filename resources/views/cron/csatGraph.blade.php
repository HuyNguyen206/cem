<?php
//    dump($graph);die;
?>

<html>
    <head>
        <meta charset="utf-8" />
        <title>CEM</title>


        <!-- basic styles -->

        <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" />
        <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
    </head>
    <body>
        <div id="csatGraph" style="width: 1000px; height: 400px; margin: 0 auto">
            
        </div>
    </body>
</html>


<script>
    window.jQuery || document.write("<script src='{{asset('assets/js/jquery-2.0.3.min.js')}}'>" + "<" + "/script>");
</script>
<script src="{{asset('assets/js/highcharts.js')}}"></script>
<script src="{{asset('assets/js/html2canvas.js')}}"></script>
<script src="{{asset('assets/js/html2canvas.svg.js')}}"></script>
<script src="{{asset('assets/js/grouped-categories.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        var colors = ['#5B9BD5', '#ED7D31'];
        var chart = {type:"column"};
        var title = {text:""};
        var subtitle = {text:""};
        var xAxis = {categories: [
                <?php foreach($graph as $data){
                    echo "{name: '".$data['net']."', categories:[{name:'".$data['tv']."', categories: ['".date_format(date_create($data['date']), "d/m")."']}]},"; 
                }?>
            ]};
        var yAxis = {
            allowDecimals: false, 
            title:{text: ''}, 
            plotLines:[{value:0, width: 1, color: '#808080'}],
            maxPadding: 0.01
        };
        var tooltip = {enabled: false};
        var legend = {layout: 'vertical', align: 'left', verticalAlign: 'bottom', borderWidth: 1, enabled: true};
        var series = [
            {
                name: 'Internet',
                data: [<?php foreach($graph as $data){
                    echo $data['net'].","; 
                }?>]
            }, 
            {
                name: 'Tivi',
                data: [<?php foreach($graph as $data){
                    echo $data['tv'].","; 
                }?>]
            }
        ];
        var credits = {
            enabled: false
        };
        
        var plotOptions = {series:{borderWidth: 0, dataLabels:{enabled:true}}};
        
        var json = {};
        json.chart = chart;
        json.colors = colors;
        json.title = title;
        json.subtitle = subtitle;
        json.xAxis = xAxis;
        json.yAxis = yAxis;
        json.tooltip = tooltip;
//        json.legend = legend;
        json.credits = credits;
        json.series = series;
        json.plotOptions = plotOptions;
        $('#csatGraph').highcharts(json);
        
        setTimeout(function(){
            html2canvas([document.getElementById('csatGraph')], {
                allowTaint: true,
                timeout: 10000,
                letterRendering: true,
                taintTest: false,
                useCORS: true,
                onrendered: function (canvas) {
                    var data = canvas.toDataURL("image/png", 1);
                    window.open(data);
                    
//                    var data = canvas.toDataURL('image/png');
//                    // AJAX call to send `data` to a PHP file that creates an PNG image from the dataURI string and saves it to a directory on the server
//                    var file= dataURLtoBlob(data);
//                    // Create new form data
//                    var fd = new FormData();
//                    fd.append("csatData", file);
//                    fd.append('_token', "<?php //echo csrf_token();?>");
//
//                    $.ajax({
//                       url: "<?php //echo url('saveCsatGraph'); ?>",
//                       type: "POST",
//                       data: fd,
//                       processData: false,
//                       contentType: false
//                    });

                }
            });
        }, 5000);
    });
    
    

    function dataURLtoBlob(dataURL) {
        // Decode the dataURL    
        var binary = atob(dataURL.split(',')[1]);
        // Create 8-bit unsigned array
        var array = [];
        for(var i = 0; i < binary.length; i++) {
            array.push(binary.charCodeAt(i));
         }
        // Return our Blob object
        return new Blob([new Uint8Array(array)], {type: 'image/png'});
    }
</script>