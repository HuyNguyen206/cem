<?php
use App\Component\ExtraFunction;
$ext = new ExtraFunction();

$violation = [
    'sales' => [
        '1' => 'Sai hẹn với khách hàng',
        '2' => 'Thái độ với khách hàng không tốt',
        '3' => 'Không thực hiện các yêu cầu phát sinh của khách hàng',
        '8' => 'Vòi vĩnh khách hàng',
        '9' => 'Tư vấn không rõ ràng, đầy đủ',
        '10' => 'Tư vấn sai',
        '11' => 'Khác',
        '12' => 'Lỗi không thuộc về nhân viên',
    ],
      'nvtc' => [
        '1' => 'Sai hẹn với khách hàng',
        '2' => 'Thái độ với khách hàng không tốt',
        '3' => 'Không thực hiện các yêu cầu phát sinh của khách hàng',
        '8' => 'Vòi vĩnh khách hàng',
        '9' => 'Tư vấn không rõ ràng, đầy đủ',
        '10' => 'Tư vấn sai',
        '11' => 'Khác',
        '12' => 'Lỗi không thuộc về nhân viên',
    ],
    'staff' => [
        '1' => 'Sai hẹn với khách hàng',
        '2' => 'Thái độ với khách hàng không tốt',
        '3' => 'Không thực hiện các yêu cầu phát sinh của khách hàng',
        '8' => 'Vòi vĩnh khách hàng',
        '4' => 'Không hướng dẫn khách hàng',
        '5' => 'Làm bừa, bẩn nhà khách hàng',
        '6' => 'nghiệp vụ kỹ thuật',
        '7' => 'Tiến độ xử lý chậm',
        '11' => 'Khác',
        '12' => 'Lỗi không thuộc về nhân viên',
    ],
];
$punish = [
    '1' => 'Phạt tiền',
    '2' => 'Cảnh cáo/ Nhắc nhở',
    '3' => 'Buộc thôi việc',
    '4' => 'Không chế tài bổ sung',
    '5' => 'Khác',
];
$tempZone = explode(',', $region);
if(count($tempZone) == 7){
    $textTitleRegion = 'Toàn quốc';
    $textRegion = 'toàn quốc';
}else{
    $textTitleRegion = 'Vùng '.$region;
    $textRegion = 'V'.$region;
}

?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<div>
    <div>
        <div>
            Hệ thống CEM - Customer Voice
            <br/>
            Tổng hợp CSAT 1,2 Nhân viên Kinh doanh và Nhân viên Kỹ thuật Triển khai/ Bảo trì
            <br/>
            {{$textTitleRegion}} Ngày: {{date('d/m/Y',strtotime($from_date)) .' - '. date('d/m/Y',strtotime($to_date))}}
            <br/>
            <br/>
        </div>
        <div>
            <b>1. Thống kê CSAT 1,2 Nhân viên và Tỷ lệ báo cáo kết quả xử lý</b><br/>
            <b>1.1 Đối với CSAT 1,2 Nhân viên Kinh doanh</b><br/><br/>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="10" style="text-align: center; vertical-align: central;">
                        Sau triển khai
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Ghi nhận qua kênh Happy Call
                    </th>
                    <th colspan="4" style="text-align: center; vertical-align: central;">
                        Chưa báo cáo xử lý
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th>
                        Tổng số KS
                    </th>
                    <th>
                        CSAT 1
                    </th>
                    <th>
                        CSAT 2
                    </th>
                    <th >
                        Tổng
                    </th>
                    <th>
                        Tỷ lệ % <br/>không hài lòng
                    </th>
                    <th >
                        CSAT TB
                    </th>
                    <th>
                        CSAT 1
                    </th>
                    <th>
                        CSAT 2
                    </th>
                    <th >
                        Tổng
                    </th>
                    <th>
                        Tỷ lệ %
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($branch as $location) {
                $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <td>{{$data[$keyZone]['csat_tong_sales']}}</td>
                    <td>{{$data[$keyZone]['csat1_sales']}}</td>
                    <td>{{$data[$keyZone]['csat2_sales']}}</td>
                    <td>{{$data[$keyZone]['csat12_sales']}}</td>
                    <td>{{$data[$keyZone]['csat12_sales']/($data[$keyZone]['csat_tong_sales']==0?1:$data[$keyZone]['csat_tong_sales'])}}</td>
                    <td>{{$data[$keyZone]['csat12_diem_sales']/($data[$keyZone]['csat_tong_sales']==0?1:$data[$keyZone]['csat_tong_sales'])}}</td>
                    <td>{{$data[$keyZone]['csat1_cbc_sales']}}</td>
                    <td>{{$data[$keyZone]['csat2_cbc_sales']}}</td>
                    <td>{{$data[$keyZone]['csat12_cbc_sales']}}</td>
                    <td>{{$data[$keyZone]['csat12_cbc_sales']/($data[$keyZone]['csat12_sales']==0?1:$data[$keyZone]['csat12_sales'])}}</td>
                </tr>

                <?php
                    $arrayFields = [
                        'csat_tong_sales'=> 0, 'csat1_sales' => 0, 'csat2_sales' => 0, 'csat12_sales' => 0, 'csat12_diem_sales' => 0, 'csat1_cbc_sales' => 0, 'csat2_cbc_sales' => 0, 'csat12_cbc_sales' => 0,
                        'csat_tong_deploy'=> 0, 'csat1_deploy' => 0, 'csat2_deploy' => 0, 'csat12_deploy' => 0, 'csat12_diem_deploy' => 0, 'csat1_cbc_deploy' => 0, 'csat2_cbc_deploy' => 0, 'csat12_cbc_deploy' => 0,
                        'csat_tong_maintain'=> 0, 'csat1_maintain' => 0, 'csat2_maintain' => 0, 'csat12_maintain' => 0, 'csat12_diem_maintain' => 0, 'csat1_cbc_maintain' => 0, 'csat2_cbc_maintain' => 0, 'csat12_cbc_maintain' => 0,
                        'csat_tong_th'=> 0, 'csat1_th' => 0, 'csat2_th' => 0, 'csat12_th' => 0, 'csat12_diem_th' => 0, 'csat1_cbc_th' => 0, 'csat2_cbc_th' => 0, 'csat12_cbc_th' => 0,
                    ];
                    foreach($arrayFields as $key => $val){
                        $data['temp'][$key] += $data[$keyZone][$key];
                    }
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <td>{{$data['temp']['csat_tong_sales']}}</td>
                    <td>{{$data['temp']['csat1_sales']}}</td>
                    <td>{{$data['temp']['csat2_sales']}}</td>
                    <td>{{$data['temp']['csat12_sales']}}</td>
                    <td>{{$data['temp']['csat12_sales']/($data['temp']['csat_tong_sales']==0?1:$data['temp']['csat_tong_sales'])}}</td>
                    <td>{{$data['temp']['csat12_diem_sales']/($data['temp']['csat_tong_sales']==0?1:$data['temp']['csat_tong_sales'])}}</td>
                    <td>{{$data['temp']['csat1_cbc_sales']}}</td>
                    <td>{{$data['temp']['csat2_cbc_sales']}}</td>
                    <td>{{$data['temp']['csat12_cbc_sales']}}</td>
                    <td>{{$data['temp']['csat12_cbc_sales']/($data['temp']['csat12_sales']==0?1:$data['temp']['csat12_sales'])}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div>
            <b>1.2 Đối với CSAT 1,2 Nhân viên Kỹ thuật Triển khai/ Bảo trì</b><br/><br/>
        </div>
        <div>
            <table width="100%">
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="10" style="text-align: center; vertical-align: central;">
                        Sau triển khai
                    </th>
                    <th colspan="10" style="text-align: center; vertical-align: central;">
                        Sau bảo trì
                    </th>
                    <th colspan="10" style="text-align: center; vertical-align: central;">
                        Total
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @for($i = 1; $i <= 3; $i++)
                        <th colspan="6" style="text-align: center; vertical-align: central;">
                            Ghi nhận qua kênh Happy Call
                        </th>
                        <th colspan="4" style="text-align: center; vertical-align: central;">
                            Chưa báo cáo xử lý
                        </th>
                    @endfor
                </tr>
                <tr>
                    <th>
                    </th>
                    @for($i = 1; $i <= 3; $i++)
                        <th>
                            Tổng số KS
                        </th>
                        <th>
                            CSAT 1
                        </th>
                        <th>
                            CSAT 2
                        </th>
                        <th >
                            Tổng
                        </th>
                        <th>
                            Tỷ lệ % <br/>không hài lòng
                        </th>
                        <th >
                            CSAT TB
                        </th>
                        <th>
                            CSAT 1
                        </th>
                        <th>
                            CSAT 2
                        </th>
                        <th >
                            Tổng
                        </th>
                        <th>
                            Tỷ lệ %
                        </th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                <?php
                    $arrayFilter = ['deploy', 'maintain', 'th'];
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    @foreach($arrayFilter as $filter)
                        <td>{{$data[$keyZone]['csat_tong_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat1_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat2_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat12_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat12_'.$filter]/($data[$keyZone]['csat_tong_'.$filter]==0?1:$data[$keyZone]['csat_tong_'.$filter])}}</td>
                        <td>{{$data[$keyZone]['csat12_diem_'.$filter]/($data[$keyZone]['csat_tong_'.$filter]==0?1:$data[$keyZone]['csat_tong_'.$filter])}}</td>
                        <td>{{$data[$keyZone]['csat1_cbc_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat2_cbc_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat12_cbc_'.$filter]}}</td>
                        <td>{{$data[$keyZone]['csat12_cbc_'.$filter]/($data[$keyZone]['csat12_'.$filter]==0?1:$data[$keyZone]['csat12_'.$filter])}}</td>
                        <?php
                        if($filter != 'th'){
                            $data[$keyZone]['csat_tong_th'] += $data[$keyZone]['csat_tong_'.$filter];
                            $data[$keyZone]['csat1_th'] += $data[$keyZone]['csat1_'.$filter];
                            $data[$keyZone]['csat2_th'] += $data[$keyZone]['csat2_'.$filter];
                            $data[$keyZone]['csat12_th'] += $data[$keyZone]['csat12_'.$filter];
                            $data[$keyZone]['csat12_diem_th'] += $data[$keyZone]['csat12_diem_'.$filter];
                            $data[$keyZone]['csat1_cbc_th'] += $data[$keyZone]['csat1_cbc_'.$filter];
                            $data[$keyZone]['csat2_cbc_th'] += $data[$keyZone]['csat2_cbc_'.$filter];
                            $data[$keyZone]['csat12_cbc_th'] += $data[$keyZone]['csat12_cbc_'.$filter];
                        }else{
                            $data['temp']['csat_tong_th'] += $data[$keyZone]['csat_tong_'.$filter];
                            $data['temp']['csat1_th'] += $data[$keyZone]['csat1_'.$filter];
                            $data['temp']['csat2_th'] += $data[$keyZone]['csat2_'.$filter];
                            $data['temp']['csat12_th'] += $data[$keyZone]['csat12_'.$filter];
                            $data['temp']['csat12_diem_th'] += $data[$keyZone]['csat12_diem_'.$filter];
                            $data['temp']['csat1_cbc_th'] += $data[$keyZone]['csat1_cbc_'.$filter];
                            $data['temp']['csat2_cbc_th'] += $data[$keyZone]['csat2_cbc_'.$filter];
                            $data['temp']['csat12_cbc_th'] += $data[$keyZone]['csat12_cbc_'.$filter];
                        }
                        ?>
                    @endforeach
                </tr>

                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach($arrayFilter as $filter)
                        <td>{{$data['temp']['csat_tong_'.$filter]}}</td>
                        <td>{{$data['temp']['csat1_'.$filter]}}</td>
                        <td>{{$data['temp']['csat2_'.$filter]}}</td>
                        <td>{{$data['temp']['csat12_'.$filter]}}</td>
                        <td>{{$data['temp']['csat12_'.$filter]/($data['temp']['csat_tong_'.$filter]==0?1:$data['temp']['csat_tong_'.$filter])}}</td>
                        <td>{{$data['temp']['csat12_diem_'.$filter]/($data['temp']['csat_tong_'.$filter]==0?1:$data['temp']['csat_tong_'.$filter])}}</td>
                        <td>{{$data['temp']['csat1_cbc_'.$filter]}}</td>
                        <td>{{$data['temp']['csat2_cbc_'.$filter]}}</td>
                        <td>{{$data['temp']['csat12_cbc_'.$filter]}}</td>
                        <td>{{$data['temp']['csat12_cbc_'.$filter]/($data['temp']['csat12_'.$filter]==0?1:$data['temp']['csat12_'.$filter])}}</td>
                    @endforeach
                </tr>
                </tfoot>
            </table>
        </div>
            <div>
            <b>1.3 Đối với CSAT 1,2 Nhân viên thu cước</b><br/><br/>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="10" style="text-align: center; vertical-align: central;">
                        Sau thu cước tại nhà
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Ghi nhận qua kênh Email
                    </th>
                    <th colspan="4" style="text-align: center; vertical-align: central;">
                        Chưa báo cáo xử lý
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th>
                        Tổng số KS
                    </th>
                    <th>
                        CSAT 1
                    </th>
                    <th>
                        CSAT 2
                    </th>
                    <th >
                        Tổng
                    </th>
                    <th>
                        Tỷ lệ % <br/>không hài lòng
                    </th>
                    <th >
                        CSAT TB
                    </th>
                    <th>
                        CSAT 1
                    </th>
                    <th>
                        CSAT 2
                    </th>
                    <th >
                        Tổng
                    </th>
                    <th>
                        Tỷ lệ %
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($branch as $location) {
                $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <td>{{$data[$keyZone]['csat_tong_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat1_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat2_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat12_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat12_nvtc']/($data[$keyZone]['csat_tong_nvtc']==0?1:$data[$keyZone]['csat_tong_nvtc'])}}</td>
                    <td>{{$data[$keyZone]['csat12_diem_nvtc']/($data[$keyZone]['csat_tong_nvtc']==0?1:$data[$keyZone]['csat_tong_nvtc'])}}</td>
                    <td>{{$data[$keyZone]['csat1_cbc_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat2_cbc_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat12_cbc_nvtc']}}</td>
                    <td>{{$data[$keyZone]['csat12_cbc_nvtc']/($data[$keyZone]['csat12_nvtc']==0?1:$data[$keyZone]['csat12_nvtc'])}}</td>
                </tr>

                <?php
                    $arrayFields = [
                        'csat_tong_nvtc'=> 0, 'csat1_nvtc' => 0, 'csat2_nvtc' => 0, 'csat12_nvtc' => 0, 'csat12_diem_nvtc' => 0, 'csat1_cbc_nvtc' => 0, 'csat2_cbc_nvtc' => 0, 'csat12_cbc_nvtc' => 0,
                        'csat_tong_deploy'=> 0, 'csat1_deploy' => 0, 'csat2_deploy' => 0, 'csat12_deploy' => 0, 'csat12_diem_deploy' => 0, 'csat1_cbc_deploy' => 0, 'csat2_cbc_deploy' => 0, 'csat12_cbc_deploy' => 0,
                        'csat_tong_maintain'=> 0, 'csat1_maintain' => 0, 'csat2_maintain' => 0, 'csat12_maintain' => 0, 'csat12_diem_maintain' => 0, 'csat1_cbc_maintain' => 0, 'csat2_cbc_maintain' => 0, 'csat12_cbc_maintain' => 0,
                        'csat_tong_th'=> 0, 'csat1_th' => 0, 'csat2_th' => 0, 'csat12_th' => 0, 'csat12_diem_th' => 0, 'csat1_cbc_th' => 0, 'csat2_cbc_th' => 0, 'csat12_cbc_th' => 0,
                    ];
                    foreach($arrayFields as $key => $val){
                        $data['temp'][$key] += $data[$keyZone][$key];
                    }
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <td>{{$data['temp']['csat_tong_nvtc']}}</td>
                    <td>{{$data['temp']['csat1_nvtc']}}</td>
                    <td>{{$data['temp']['csat2_nvtc']}}</td>
                    <td>{{$data['temp']['csat12_nvtc']}}</td>
                    <td>{{$data['temp']['csat12_nvtc']/($data['temp']['csat_tong_nvtc']==0?1:$data['temp']['csat_tong_nvtc'])}}</td>
                    <td>{{$data['temp']['csat12_diem_nvtc']/($data['temp']['csat_tong_nvtc']==0?1:$data['temp']['csat_tong_nvtc'])}}</td>
                    <td>{{$data['temp']['csat1_cbc_nvtc']}}</td>
                    <td>{{$data['temp']['csat2_cbc_nvtc']}}</td>
                    <td>{{$data['temp']['csat12_cbc_nvtc']}}</td>
                    <td>{{$data['temp']['csat12_cbc_nvtc']/($data['temp']['csat12_nvtc']==0?1:$data['temp']['csat12_nvtc'])}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
        
        <div>
            <b>2. Thống kê kết quả xử lý: loại lỗi / Nguyên nhân, Chế tài bổ sung và Hành động khắc phục</b><br/>
            <b>2.1 Đối với CSAT 1,2 Nhân viên Kinh doanh</b><br/><br/>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="16" style="text-align: center; vertical-align: central;">
                        Sau Triển khai
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="9" style="text-align: center; vertical-align: central;">
                        Lỗi không phù hợp
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Chế tài bổ sung
                    </th>
                    <th>
                        FTQ điều chỉnh
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @foreach($violation['sales'] as $val)
                        <th style="text-align: center; vertical-align: central;">
                            {{$val}}
                        </th>
                    @endforeach
                    <th style="text-align: center; vertical-align: central;">
                        Tổng
                    </th>
                    @foreach($punish as $val)
                        <th style="text-align: center; vertical-align: central;">
                            {{$val}}
                        </th>
                    @endforeach
                    <th style="text-align: center; vertical-align: central;">
                        Tổng
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <?php foreach($violation['sales'] as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['violation_sales_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['violation_sales_'.$key] += $data[$keyZone]['violation_sales_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['violation_sales_t']}}
                        <?php $data['temp']['violation_sales_t'] += $data[$keyZone]['violation_sales_t'];  ?>
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['punish_sales_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['punish_sales_'.$key] += $data[$keyZone]['punish_sales_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['punish_sales_t']}}
                        <?php $data['temp']['punish_sales_t'] += $data[$keyZone]['punish_sales_t'];  ?>
                    </td>
                    <td>
                        {{$data[$keyZone]['ftq_sales']}}
                        <?php $data['temp']['ftq_sales'] += $data[$keyZone]['ftq_sales'];  ?>
                    </td>
                </tr>
                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <?php foreach($violation['sales'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_sales_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['violation_sales_t']}}
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_sales_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['punish_sales_t']}}
                    </td>
                    <td>
                        {{$data['temp']['ftq_sales']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tỷ lệ %
                    </td>
                    <?php foreach($violation['sales'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_sales_'.$key]/ ($data['temp']['violation_sales_t'] == 0 ? 1:$data['temp']['violation_sales_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_sales_'.$key]/ ($data['temp']['punish_sales_t'] == 0 ? 1:$data['temp']['punish_sales_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div>
            <b >2. Đối với CSAT 1,2 Nhân viên kỹ thuật Triển khai / Bảo trì</b><br/><br/>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="18" style="text-align: center; vertical-align: central;">
                        Sau Triển khai
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="11" style="text-align: center; vertical-align: central;">
                        Lỗi không phù hợp
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Chế tài bổ sung
                    </th>
                    <th>
                        FTQ điều chỉnh
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @foreach($violation['staff'] as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    @foreach($punish as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['violation_deploy_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['violation_deploy_'.$key] += $data[$keyZone]['violation_deploy_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['violation_deploy_t']}}
                        <?php $data['temp']['violation_deploy_t'] += $data[$keyZone]['violation_deploy_t'];  ?>
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['punish_deploy_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['punish_deploy_'.$key] += $data[$keyZone]['punish_deploy_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['punish_deploy_t']}}
                        <?php $data['temp']['punish_deploy_t'] += $data[$keyZone]['punish_deploy_t'];  ?>
                    </td>
                    <td>
                        {{$data[$keyZone]['ftq_deploy']}}
                        <?php $data['temp']['ftq_deploy'] += $data[$keyZone]['ftq_deploy'];  ?>
                    </td>
                </tr>
                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_deploy_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['violation_deploy_t']}}
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_deploy_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['punish_deploy_t']}}
                    </td>
                    <td>
                        {{$data['temp']['ftq_deploy']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tỷ lệ %
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_deploy_'.$key]/ ($data['temp']['violation_deploy_t'] == 0 ? 1:$data['temp']['violation_deploy_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_deploy_'.$key]/ ($data['temp']['punish_deploy_t'] == 0 ? 1:$data['temp']['punish_deploy_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                       Chi nhánh
                    </th>
                    <th colspan="18" style="text-align: center; vertical-align: central;">
                        Sau Bảo trì
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="11" style="text-align: center; vertical-align: central;">
                        Lỗi không phù hợp
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Chế tài bổ sung
                    </th>
                    <th>
                        FTQ điều chỉnh
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @foreach($violation['staff'] as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    @foreach($punish as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['violation_maintaince_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['violation_maintaince_'.$key] += $data[$keyZone]['violation_maintaince_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['violation_maintaince_t']}}
                        <?php $data['temp']['violation_maintaince_t'] += $data[$keyZone]['violation_maintaince_t'];  ?>
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['punish_maintaince_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['punish_maintaince_'.$key] += $data[$keyZone]['punish_maintaince_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['punish_maintaince_t']}}
                        <?php $data['temp']['punish_maintaince_t'] += $data[$keyZone]['punish_maintaince_t'];  ?>
                    </td>
                    <td>
                        {{$data[$keyZone]['ftq_maintaince']}}
                        <?php $data['temp']['ftq_maintaince'] += $data[$keyZone]['ftq_maintaince'];  ?>
                    </td>
                </tr>
                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_maintaince_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['violation_maintaince_t']}}
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_maintaince_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['punish_maintaince_t']}}
                    </td>
                    <td>
                        {{$data['temp']['ftq_maintaince']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tỷ lệ %
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_maintaince_'.$key]/ ($data['temp']['violation_maintaince_t'] == 0 ? 1:$data['temp']['violation_maintaince_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_maintaince_'.$key]/ ($data['temp']['punish_maintaince_t'] == 0 ? 1:$data['temp']['punish_maintaince_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="18" style="text-align: center; vertical-align: central;">
                        Total Sau Triển khai & Sau Bảo trì
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="11" style="text-align: center; vertical-align: central;">
                        Lỗi không phù hợp
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Chế tài bổ sung
                    </th>
                    <th>
                        FTQ điều chỉnh
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @foreach($violation['staff'] as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    @foreach($punish as $val)
                        <th>
                            {{$val}}
                        </th>
                    @endforeach
                    <th>
                        Tổng
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['violation_deploy_maintaince_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['violation_deploy_maintaince_'.$key] += $data[$keyZone]['violation_deploy_maintaince_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['violation_deploy_maintaince_t']}}
                        <?php $data['temp']['violation_deploy_maintaince_t'] += $data[$keyZone]['violation_deploy_maintaince_t'];  ?>
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['punish_deploy_maintaince_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['punish_deploy_maintaince_'.$key] += $data[$keyZone]['punish_deploy_maintaince_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['punish_deploy_maintaince_t']}}
                        <?php $data['temp']['punish_deploy_maintaince_t'] += $data[$keyZone]['punish_deploy_maintaince_t'];  ?>
                    </td>
                    <td>
                        {{$data[$keyZone]['ftq_deploy_maintaince']}}
                        <?php $data['temp']['ftq_deploy_maintaince'] += $data[$keyZone]['ftq_deploy_maintaince'];  ?>
                    </td>
                </tr>
                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_deploy_maintaince_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['violation_deploy_maintaince_t']}}
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_deploy_maintaince_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['punish_deploy_maintaince_t']}}
                    </td>
                    <td>
                        {{$data['temp']['ftq_deploy_maintaince']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tỷ lệ %
                    </td>
                    <?php foreach($violation['staff'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_deploy_maintaince_'.$key]/ ($data['temp']['violation_deploy_maintaince_t'] == 0 ? 1:$data['temp']['violation_deploy_maintaince_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_deploy_maintaince_'.$key]/ ($data['temp']['punish_deploy_maintaince_t'] == 0 ? 1:$data['temp']['punish_deploy_maintaince_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
            <div>
            <b>2.3 Đối với CSAT 1,2 Nhân viên thu cước</b><br/><br/>
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        Chi nhánh
                    </th>
                    <th colspan="16" style="text-align: center; vertical-align: central;">
                        Sau thu cước tại nhà
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th colspan="9" style="text-align: center; vertical-align: central;">
                        Lỗi không phù hợp
                    </th>
                    <th colspan="6" style="text-align: center; vertical-align: central;">
                        Chế tài bổ sung
                    </th>
                    <th>
                        FTQ điều chỉnh
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    @foreach($violation['nvtc'] as $val)
                        <th style="text-align: center; vertical-align: central;">
                            {{$val}}
                        </th>
                    @endforeach
                    <th style="text-align: center; vertical-align: central;">
                        Tổng
                    </th>
                    @foreach($punish as $val)
                        <th style="text-align: center; vertical-align: central;">
                            {{$val}}
                        </th>
                    @endforeach
                    <th style="text-align: center; vertical-align: central;">
                        Tổng
                    </th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($branch as $location) {
                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                    $temp = explode(' - ', $location->name);
                ?>
                <tr>
                    <td>
                        {{$temp[0].$location->branchcode}}
                    </td>
                    <?php foreach($violation['nvtc'] as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['violation_nvtc_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['violation_nvtc_'.$key] += $data[$keyZone]['violation_nvtc_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['violation_nvtc_t']}}
                        <?php $data['temp']['violation_nvtc_t'] += $data[$keyZone]['violation_nvtc_t'];  ?>
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data[$keyZone]['punish_nvtc_'.$key]}}
                    </td>
                    <?php
                    $data['temp']['punish_nvtc_'.$key] += $data[$keyZone]['punish_nvtc_'.$key];
                    } ?>
                    <td>
                        {{$data[$keyZone]['punish_nvtc_t']}}
                        <?php $data['temp']['punish_nvtc_t'] += $data[$keyZone]['punish_nvtc_t'];  ?>
                    </td>
                    <td>
                        {{$data[$keyZone]['ftq_nvtc']}}
                        <?php $data['temp']['ftq_nvtc'] += $data[$keyZone]['ftq_nvtc'];  ?>
                    </td>
                </tr>
                <?php
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    <?php foreach($violation['nvtc'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_nvtc_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['violation_nvtc_t']}}
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_nvtc_'.$key]}}
                    </td>
                    <?php } ?>
                    <td>
                        {{$data['temp']['punish_nvtc_t']}}
                    </td>
                    <td>
                        {{$data['temp']['ftq_nvtc']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tỷ lệ %
                    </td>
                    <?php foreach($violation['nvtc'] as $key => $val) { ?>
                    <td>
                        {{$data['temp']['violation_nvtc_'.$key]/ ($data['temp']['violation_nvtc_t'] == 0 ? 1:$data['temp']['violation_nvtc_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <?php foreach ($punish as $key => $val) { ?>
                    <td>
                        {{$data['temp']['punish_nvtc_'.$key]/ ($data['temp']['punish_nvtc_t'] == 0 ? 1:$data['temp']['punish_nvtc_t'])}}
                    </td>
                    <?php } ?>
                    <td>
                        1
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>