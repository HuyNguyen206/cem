<?php
$transFile = 'csat-service';
?>
<!--<div class="container">-->          
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>{{trans($transFile.'.ON')}}</th>
                <th>{{trans($transFile.'.FileName')}}</th>
                <th>{{trans($transFile.'.Action')}}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(isset($listFileExel))
            {
                $i=1;
                foreach ($listFileExel as $key => $value) {
                    
                
            ?>
            <tr>
                <td>
                    {{$i}}
                </td>
                <td>
                    {{$value}}
                </td>
                <td>
                    <span style="cursor: pointer;
                          color: blue;" class="downloadTrigger"  data="{{$value}}"  >
                        {{trans($transFile.'.Download')}}
                    </span>
                </td>
            </tr>
            <?php
            }
            $i++;
            }
            else
            {
            ?>
            <tr>
                <td>
                    1
                </td>
                <td>
                    {{$PathExcel['file']}}
                </td>
                <td>
                    <span style="cursor: pointer;
                          color: blue;" class="downloadTrigger"  data="<?php echo $PathExcel['file']; ?>"  >
                        {{trans($transFile.'.Download')}}
                    </span>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<!--</div>-->
<script>
    $(".downloadTrigger").click(function () {
        $(this).css('pointer-events', 'none');
var nameFile = $(this).attr('data');
//Xóa file
        $.ajax({
            url: '<?php echo url('/customer-voice/dashboard/report/deleteExcelFile') ?>',
            context:  $(this),
            cache: false,
            type: "POST",
            dataType: "json",
            data: {_token: $('input[name=_token]').val(), fileName: nameFile},
            beforeSend: function () {                            
                var file_path = '/storage/' + nameFile;
                var a = document.createElement('A');
                a.href = file_path;
                a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                  $(this).text(' {{trans($transFile.'.DownloadingPleaseWaitToDownloadNextFileUntilFinnishDownloadCurrentFile')}}')
            },
            complete: function () {
                  $(this).text('{{trans($transFile.'.Downloaded')}}')
            },
            success: function (data) {

            },
        });


    });
</script>
