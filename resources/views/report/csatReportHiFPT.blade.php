<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Số hợp đồng</th>
                    <th>Mã CheckList</th>
                    <th>Ghi chú</th>
                    <th>Điểm</th>
                    <th>Ngày hoàn thành khảo sát</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($survey as $val){
                    $pos = strpos($val->section_code, 'hifpt');
                ?>
                <tr>
                    <td>{{$val->hopdong}}</td>
                    <td>{{($pos === false)?$val->section_code: ''}}</td>
                    <td>{{$val->ghichu}}</td>
                    <td>{{$val->diem}}</td>
                    <td>{{$val->ngay}}</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
</html>





