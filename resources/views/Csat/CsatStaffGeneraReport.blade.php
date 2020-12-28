@extends('layouts.app')

@section('content')
<div class="page-content">

    <?php
    $transfile = 'report'?>
    <br />
    <div id="chartNPSStatistic"></div>
    <div class="table-responsive">
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            1.1. Đối với CSAT 1,2 Nhân viên Kinh doanh
        </h3>

        <table id="table-NPS" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
            <thead>
                <tr>
                    <th rowspan="3">Vùng</th>
                    <th colspan="10">Sau Triển khai</th>
                </tr>
                <tr>
                    <th colspan="6">Ghi nhận qua kênh Happy Call</th>
                    <th colspan="4">Chưa báo cáo xử lý</th>
                </tr>
                <tr>
                    <th>Tổng số KS</th>
                    <th>CSAT 1</th>
                    <th>CSAT 2</th>
                    <th>Tổng</th>
                    <th>Tỷ lệ %"không hài lòng"</th>
                    <th>CSAT TB</th>
                    <th>CSAT 1</th>
                    <th>CSAT 2</th>
                    <th>Tổng</th>
                    <th>Tỷ lệ %</th>
                </tr>
            </thead>

            <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

            </tr>
            </tbody>
        </table>
    </div>
</div>
@stop