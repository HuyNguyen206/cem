<?php $transfile = 'report' ?>
<br />
<div class="table-responsive" style="overflow: auto;">
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.Transaction')}}
    </h3>
    <table id="table-Productivity" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
        <thead>
            <tr>
                <th class="text-center">Nội dung</th>
                <th class="text-center">  {{trans($transfile.'.After Paid Counter')}}</th>
                <th class="text-center">  {{trans($transfile.'.Maintenance MobiPay')}}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($result as $key => $value) {
                ?>
                <tr>
                     <td class="<?php echo $key != 'Tỉ lệ phản hồi' ? '' : 'foot_average ' ?>">
                        <span>
                            {{$key}}
                        </span>
                    </td>
                    <td class="<?php echo $key != 'Tỉ lệ phản hồi' ? '' : 'foot_average ' ?>">
                        <span>
                            {{$value['SLGDTQ']}}
                        </span>
                    </td>
                    <td class="<?php echo $key != 'Tỉ lệ phản hồi' ? '' : 'foot_average ' ?>">
                        <span>
                           {{$value['SLGDTCTN']}}
                        </span>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <input type="hidden" id="typeReport" value="9">
</div>
<style type="text/css">
    .number {
        float:right;
    }
    #table-Productivity th.headerSecond:hover {
        color: #547ea8;
    }
    #table-Productivity th.headerSecond {
        color: #307ecc;
    }
    .totalAttribute {
        font-weight: bold;
        color: blue;
    }
</style>

<style>
    #table-responsive
    {
        overflow: auto !important;
    }
</style>