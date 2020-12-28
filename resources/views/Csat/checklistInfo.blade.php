<?php

    $prechecklistStatus = [
        0 => 'Chưa xử lý',
        2 => 'Đang xử lý',
        3 => 'Xử lý hoàn tất',
        99 => 'Hủy không xử lý'
    ];
    $departmentId = [
        '1' => 'IBB',
        '2' => 'TIN/PNC',
        '3' => 'Telesale',
        '4' => 'CUS',
        '5' => 'CS chi nhánh',
        '6' => 'CSHO',
        '7' => 'KDDA',
        '8' => 'NVTC',
        'IBB' => 'IBB',
        'TIN/PNC' => 'TIN/PNC',
        'Telesale' => 'Telesale',
        'CUS' => 'CUS',
        'CS chi nhánh' => 'CS chi nhánh',
        'CSHO' => 'CSHO',
        'KDDA' => 'KDDA',
        'NVTC' => 'NVTC'
    ];
    $firstStatusPreCL = [
        5 => 'Yeu cau nhap Checklist',
        1 => 'Mat ket noi',
        6 => 'IPTV',
        7 => 'Wifi',
        2 => 'Mạng chậm',
        3 => 'Mang chap chon',
        4 => 'Tinh trang khac',
        '' => ''
    ];
    $actionProcessPreCL = [
        1 => 'Đóng PreCL và tạo CL',
        2 => ' Đóng PreCL và không tạo CL',
        3 => ' PreCL Đang xử lý',
        4 => ' PreCL Chưa xử lý',
        5 => ' Đóng PreCL, lúc tạo PreCL HĐ có ESCL đang xử lý'
    ];
    $departmentPreCL = [
        4 => 'IBB',
        7 => 'CUS',
        43 => 'CS-HO',
        430 => 'CS-CN',
        39 => 'Telesales',
    ];
?>
<div class="container">          
    <table class="table table-striped">
        <thead>
            <tr>
                <th>STT</th>
                <th>Số HĐ</th>
                <th>Loại khảo sát</th>
                <?php  
                if($type == 3)
                {
                ?>
                <th>Ghi nhận sự cố ban đầu</th>
                <th>Thông tin ghi nhận</th>
                <th>Lần hỗ trợ</th>
                <th>Tình trạng</th>
                <th>NV ghi nhận</th>
                <th>Thời gian xử lý</th>
                <th>Thời gian hẹn</th>
                <th>Tổng số phút</th>
                <th>Hành động xử lý</th>

                <th>Thời gian nhập</th>
                <th>Phân công</th>
                <th>Thời gian tồn</th>
                <th>Vị trí xảy ra lỗi</th>
                <th>Mô tả lỗi</th>
                <th>Mô tả nguyên nhân</th>
                <th>Hướng xử lý</th>
                <th>Ghi chú</th>
                <th>Loại CL</th>
                <th>CL lặp</th>
                <th>Tình trạng</th>
                <th>Thời gian hoàn tất</th>
                <th>Tổng số phút</th>
                <?php
                } else 
                {
                ?>
                                                     <th>Ngày gửi</th>
                                                    <th>Nội dung gửi</th>
                                                    <th>Bộ phận chuyển tiếp</th>
                                                    <th>Bộ phận tiếp nhận</th>
                                                    <th>Nội dung xử lý</th>
                                                    <th>Kết quả XL</th>
                                                    <th>Người Xử lý</th>
                                                    <th>Thời gian xử lý</th>
                                                    <th>Tổng số phút</th>
                                                    <th>Số lượng chuyển tiếp</th>
                <?php
                }
                ?>
            </tr>
        <tbody>
            <?php  
            if(empty($result))
            {
                ?>
            <tr>
                <td>
                    Không có dữ liệu
                </td>
            </tr>
            <?php
            }
            else
            {
            $i=1;
            foreach ($result as $key => $value) {
                ?>
        
        <tr>
           <td> {{$i++}}
               
                       </td>
                           <td> {{$value['section_contract_num']}}
               
                       </td>
                           <td>   <?php
                                                    if ($value['section_survey_id'] == 1) {
                                                        echo 'Sau triển khai DirectSales';
                                                    } else if ($value['section_survey_id'] == 6) {
                                                        echo 'Sau triển khai TeleSales';
                                                    } else {
                                                        echo 'Sau bảo trì';
                                                    }
                                                    ?>
               
                      </td>
                      
                      <?php if($type == 3) { ?>
                           <td>
                                                        <?php if (isset($firstStatusPreCL[$value['first_status']])) { ?>
                                                            {{$firstStatusPreCL[$value['first_status']]}}
                                                        <?php } ?>

                                                    </td>
                                                    <td>
                                                        {{$value['description']}}
                                                    </td>
                                                    <td>
                                                        {{$value['count_sup']}}
                                                    </td>
                                                    <td>
                                                        <?php if (isset($prechecklistStatus[$value['sup_status_id']])) { ?>
                                                            {{$prechecklistStatus[$value['sup_status_id']]}}
                                                        <?php } ?>

                                                    </td>
                                                    <td>
                                                        {{$value['create_by']}}
                                                    </td>
                                                    <td>
                                                        {{$value['updated_at']}}
                                                    </td>
                                                    <td>
                                                        {{$value['appointment_timer']}}
                                                    </td>
                                                    <td>
                                                        {{$value['total_minute']}}
                                                    </td>
                                                    <td>
                                                        <?php if (isset($actionProcessPreCL[$value['action_process']])) { ?>
                                                            {{$actionProcessPreCL[$value['action_process']]}}
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    
                                                    
                                                     <td>
                                                        {{$value['input_time']}}
                                                    </td>
                                                     <td>
                                                        {{$value['assign']}}
                                                    </td>
                                                     <td>
                                                        {{$value['store_time']}}
                                                    </td>
                                                     <td>
                                                        {{$value['error_position']}}
                                                    </td>
                                                     <td>
                                                        {{$value['error_description']}}
                                                    </td>
                                                     <td>
                                                        {{$value['reason_description']}}
                                                    </td>
                                                     <td>
                                                        {{$value['way_solving']}}
                                                    </td>
                                                     <td>
                                                        {{$value['s_description']}}
                                                    </td>
                                                     <td>
                                                        {{$value['checklist_type']}}
                                                    </td>
                                                     <td>
                                                        {{$value['repeat_checklist']}}
                                                    </td>
                                                     <td>
                                                        {{$value['final_status']}}
                                                    </td>
                                                     <td>
                                                        {{$value['finish_date']}}
                                                    </td>
                                                     <td>
                                                        {{$value['total_minute']}}
                                                    </td>
                                                    <?php
                      } else
                      {
                                                    ?>
                                                      <td> {{$value['created_at']}}</td>
                                                    <td> {{$value['content']}}</td>
                                                    <td>
                                                        <?php if (isset($departmentId[$value['department_transfer']])) {
                                                            ?>
                                                            {{$departmentId[$value['department_transfer']]}}
                                                        <?php } ?>
                                                    </td>
                                                    <td> 
                                                        <?php
                                                        if (isset($departmentId[$value['department']]))
                                                            echo $departmentId[$value['department']];
                                                        else if (isset($departmentId[explode(',', $value['department'])[0]]))
                                                            echo $departmentId[explode(',', $value['department'])[0]];
                                                        else
                                                            echo '';
                                                        ?>
                                                    </td>
                                                    <td> {{$value['description']}}</td>
                                                    <td> {{$value['status']}}</td>
                                                    <td> {{$value['logon_user']}}</td>
                                                    <td> {{$value['update_date']}}</td>
                                                    <td> {{$value['total_minute']}}</td>
                                                    
                                                    <?php
                      } 
                      
                                                    ?>


        </tr>
            <?php
            }
            }
            ?>
        </tbody>




    </table>
</div>

