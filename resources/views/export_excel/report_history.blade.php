<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
$controller = 'history';
$title = 'List Report';
$transfile = $controller;
$common = 'common';
$prefix = main_prefix;
$temp = '';
?>
<table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        @foreach($columnView as $key => $val)
            @if($key != 'section_id')
                @if($key == 'csat_salesman_note' || $key == 'csat_deployer_note' || $key == 'csat_maintenance_staff_note' || $key == 'csat_transaction_note' || $key == 'csat_transaction_staff_note' || $key == 'csat_charge_at_home_note' || $key == 'csat_charge_at_home_staff_note')
                    <th width="50">{{$val}}</th>
                @else
                    <th>{{$val}}</th>
                @endif
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($dataPage as $stt => $data)
    <tr>
        @foreach($data as $key => $val)
            @if($key != 'section_id')
                <td>{{$val}}</td>
            @endif
        @endforeach
    </tr>
    @endforeach
    </tbody>
</table>