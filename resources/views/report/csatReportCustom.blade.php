<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table, th, td {
                border: 1px solid #000000;
                border-collapse: collapse;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Vùng</th>
                    <th>Tỉnh thành</th>
                    <th>Đối tác</th>
                    <th>Tổ con</th>
                    <th>Hợp đồng</th>
                    <th>Ngày đánh giá</th>
                    <th>Csat intenet TK</th>
                    <th>Csat intenet BT</th>
                    <th>Csat truyền hình TK</th>
                    <th>Csat truyền hình BT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($survey as $val){ 
                    if( $val->csat_net_point !== null || 
                        $val->csat_maintenance_net_point !== null ||
                        $val->csat_tv_point !== null ||
                        $val->csat_maintenance_tv_point !== null){
                ?>
                <tr>
                    <td>{{$val->vung}}</td>
                    <td>{{$val->tenViTri}}</td>
                    <td>{{$val->section_supporter}}</td>
                    <td>{{$val->section_subsupporter}}</td>
                    <td>{{$val->soHopDong}}</td>
                    <td>{{$val->thoiGianGhiNhan}}</td>
                    <td>{{$val->csat_net_point}}</td>
                    <td>{{$val->csat_maintenance_net_point}}</td>
                    <td>{{$val->csat_tv_point}}</td>
                    <td>{{$val->csat_maintenance_tv_point}}</td>
                </tr>
                <?php 
                
                }} ?>
            </tbody>
        </table>
    </body>
</html>





