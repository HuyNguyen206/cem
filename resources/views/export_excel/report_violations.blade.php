<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
    $controller = 'violations';
    $title = 'List Report';
    $transfile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = '';

?>
<table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th class="center" colspan="{{count($columnView) - 14}}">Thông tin ghi nhận từ hệ thống khảo sát CSKH</th>
        <th class="center" colspan="7">Thông tin xử lý CSAT</th>
        <th class="center" colspan="4">Thông tin của người làm báo cáo</th>
        <th class="center" colspan="3">FTQ kiểm chứng</th>
    </tr>
    <tr>
        <th>STT</th>
        @foreach ($columnView as $key => $val)
            @if ($key != 'section_id')
                <th>{{$val}}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    @foreach ($dataPage as $data)
        <tr>
            <td>{{$i}}</td>
            @foreach ($data as $key => $val)
                @if ($key != 'section_id')
                    <td>{{$val}}</td>
                @endif
            @endforeach
        </tr>
        <?php $i++; ?>
    @endforeach
    </tbody>
</table>