<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <?php foreach($columnView as $key => $val){ ?>
        <th>{{$val}}</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($dataPage as $stt => $data){ ?>
    <tr>
        <?php foreach($data as $key => $val){
            echo '<td class="hidden-480">'.$val.'</td>';
        } ?>
    </tr>
    <?php } ?>
    </tbody>
</table>