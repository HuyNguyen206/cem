<?php
$transfile = 'report';
$note = ['CSAT_12' => 'CSAT 1 & 2',
    'CSAT_3' => 'CSAT 3',
    'CSAT_45' => 'CSAT 4 & 5'];
$arrTotalPercent = $arrUnsatisfiedPercent = $arrNeutralPercent = $arrSatisfiedPercent = [
    'NVKinhDoanh' => 0,
    'NVTrienKhai' => 0,
    'DGDichVu_Net' => 0,
    'DGDichVu_TV' => 0,
    'NVKinhDoanhTS' => 0,
    'NVTrienKhaiTS' => 0,
    'DGDichVuTS_Net' => 0,
    'DGDichVuTS_TV' => 0,
    'NVBaoTriTIN' => 0,
    'NVBaoTriINDO' => 0,
    'DVBaoTriTIN_Net' => 0,
    'DVBaoTriTIN_TV' => 0,
    'DVBaoTriINDO_Net' => 0,
    'DVBaoTriINDO_TV' => 0,
    'NVThuCuoc' => 0,
    'DGDichVu_MobiPay_Net' => 0,
    'DGDichVu_MobiPay_TV' => 0,
    'DGDichVu_Counter' => 0,
    'NV_Counter' => 0,
    'NVKinhDoanhSS' => 0,
    'NVTrienKhaiSS' => 0,
    'DGDichVuSS_Net' => 0,
    'DGDichVuSS_TV' => 0,
    'NVBT_SSW' => 0,
    'DGDichVuSSW_Net' => 0,
    'DGDichVuSSW_TV' => 0,
    'NVBT_SSW' => 0,
    'DGDichVuSSW_Net' => 0,
    'DGDichVuSSW_TV' => 0];
//var_dump($survey);
//die;
?>


    <script type="text/javascript">
        $(document).ready(function () {
        });</script>

<input type="hidden" id="typeReport" value="4">
<script type="text/javascript">
    $(document).ready(function () {
    })
</script>
<style>
    #table-CSATReport_wrapper, #table-CSAT12ServiceReport_wrapper,#table-CSAT12StaffReport_wrapper, #table-CSAT12ActionServiceReport_wrapper
    {
        overflow: auto;
    }
</style>