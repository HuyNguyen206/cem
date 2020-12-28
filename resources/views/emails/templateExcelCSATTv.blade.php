<?php
    $arrayPointContacts = [
        '1' => 'Sau triển khai DirectSales',
        '6' => 'Sau Triển khai TeleSales',
        '9' => 'Sau triển khai Sales tại quầy',
        '10' => 'Sau swap',
    ];

    $cs = 'cs';
    $department = $cs;
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <div>
            <div>
                <table>
                    <tr>
                        <td>
                            <b style="font-size: 14px;">Hệ thống CEM - Customer Voice</b>                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b style="font-size: 14px;">Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Truyền hình</b>                            
                        </td>
                    </tr>
                    <tr>
                        <td>                            
                            Vùng <b style="font-size: 14px;">{{$region}}</b> ngày <b>{{$day}}</b>
                        </td>
                    </tr>
                </table>
            </div>
            <div></div>
            <div>
                <table class="center">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Vùng</th>
                            <th>Chi nhánh</th>
                            <th>Điểm tiếp xúc</th>
                            <th>Kênh ghi nhận</th>
                            <th>Số HĐ</th>
                            <th>Nhân viên kinh doanh</th>
                            <th>Nhân viên Triển khai/Bảo trì</th>
                            <th>Thời gian ghi nhận</th>
                            <th>CSAT</th>
                            <th>Loại lỗi ghi nhận</th>
                            <th>Ghi chú</th>
                            <th><b style="color: #DD1144;">Hành động xử lý của CSKH Happy Call</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                            @foreach($detailReports as $report)
                                @if($report->vung == 'Vung '.$region && ((!empty($report->csat_tv_point) && in_array($report->csat_tv_point, [1,2])) || (!empty($report->csat_maintenance_tv_point && in_array($report->csat_maintenance_tv_point, [1,2])))))
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$report->vung}}</td>
                                        <td>
                                            @if($region == 1 || $region == 5)
                                                <?php $temp = explode(' - ', $report->tenViTri); ?>
                                                {{$temp[0] . $report->chiNhanh.'-'.$temp[1]}}
                                            @else
                                                {{$report->tenViTri}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($report->loaiKhaoSat == 2)
                                                @if(str_contains($report->section_supporter, 'INDO'))
                                                    {{'Sau bảo trì INDO'}}
                                                @else
                                                    {{'Sau bảo trì TIN/PNC'}}
                                                @endif
                                            @else
                                                {{$arrayPointContacts[$report->loaiKhaoSat]}}
                                            @endif
                                        </td>
                                        <td>CS/HappyCall</td>
                                        <td>{{$report->soHopDong}}</td>
                                        <td>{{$report->nhanVienKinhDoanh}}</td>
                                        <td>{{$report->section_supporter.' '.$report->section_subsupporter}}</td>
                                        <td>{{$report->thoiGianGhiNhan}}</td>
                                        <td style="font-weight: bold;">{{(!empty($report->csat_tv_point))?$report->csat_tv_point:$report->csat_maintenance_tv_point}}</td>
                                        <td>{{(!empty($report->csat_maintenance_tv_answer_extra_id))?$statusesTv[$report->csat_maintenance_tv_answer_extra_id]->answers_title:$statusesTv[$report->csat_tv_answer_extra_id]->answers_title}}</td>
                                        <td>{{(!empty($report->csat_tv_note))?$report->csat_tv_note:$report->csat_maintenance_tv_note}}</td>
                                        <td>{{!empty($report->result_action_tv)?$actions[$department][$report->result_action_tv]->answers_title:''}}</td>
                                    </tr>
                                <?php $i++; ?>
                                @endif
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>