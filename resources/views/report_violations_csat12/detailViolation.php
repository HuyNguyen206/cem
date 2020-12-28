<?php 
$controller = 'history';    
$prefix = main_prefix;
?>
<div class="col-xs-12">
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="supporter"><?php echo ($type == 1) ?'Họ tên NVKD' :'Họ tên NVTK trực tiếp'?> (*)</label>

        <div class="col-sm-8">
            <input class="col-sm-12" type="text" name='supporter' placeholder="Nhập..." class="col-xs-10 col-sm-5" value="<?php echo !empty($detail['section_supporter']) ?$detail['section_supporter'] :''?>"/>
        </div>
    </div>
    
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="explanation">Thông tin giải trình của cá nhân/tổ/đơn vị liên quan (*)</label>
        
        <textarea name='explanation' placeholder="Nhập..." class="autosize-transition form-control" rows="10"><?php echo !empty($detail['explanation_desc']) ?$detail['explanation_desc'] :''?></textarea>
    </div>
    
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="verify">Quản lý kiểm chứng (*)</label>
        
        <textarea name='verify' placeholder="Nhập..." class="autosize-transition form-control" rows="10"><?php echo !empty($detail['qs_verify']) ?$detail['qs_verify'] :''?></textarea>
    </div>
    
    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="type">Loại lỗi (*)</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optType' id="optType">
                <option value="1" <?php echo ($detail['violations_type'] == 1) ?'selected' :''?> >Sai hẹn với khách hàng</option>
                <option value="2" <?php echo ($detail['violations_type'] == 2) ?'selected' :''?> >Thái độ với khách hàng không tốt</option>
                <option value="3" <?php echo ($detail['violations_type'] == 3) ?'selected' :''?> >Không thực hiện các yêu cầu phát sinh của khách hàng</option>
                <option value="4" <?php echo ($detail['violations_type'] == 4) ?'selected' :''?> >Không hướng dẫn khách hàng</option>
                <option value="5" <?php echo ($detail['violations_type'] == 5) ?'selected' :''?> >Làm bừa, bẩn nhà khách hàng</option>
                <option value="6" <?php echo ($detail['violations_type'] == 6) ?'selected' :''?> >Nghiệp vụ kỹ thuật</option>
                <option value="7" <?php echo ($detail['violations_type'] == 7) ?'selected' :''?> >Tiến độ xử lý chậm</option>
                <option value="8" <?php echo ($detail['violations_type'] == 8) ?'selected' :''?> >Vòi vĩnh khách hàng</option>
                <option value="9" <?php echo ($detail['violations_type'] == 9) ?'selected' :''?> >Tư vấn không rõ ràng, đầy đủ</option>
                <option value="10" <?php echo ($detail['violations_type'] == 10) ?'selected' :''?> >Tư vấn sai</option>
                <option value="12" <?php echo ($detail['violations_type'] == 12) ?'selected' :''?> >Lỗi không thuộc về nhân viên</option>
                <option value="11" <?php echo ($detail['violations_type'] == 11) ?'selected' :''?> >Khác</option>
            </select>
        </div>
    </div>

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="punishment">Loại chế tài bổ sung (*)</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optPunish'>
                <option value="1" <?php echo ($detail['punishment'] == 1) ?'selected' :''?> >Phạt tiền</option>
                <option value="2" <?php echo ($detail['punishment'] == 2) ?'selected' :''?> >Cảnh cáo/nhắc nhở</option>
                <option value="3" <?php echo ($detail['punishment'] == 3) ?'selected' :''?> >Buộc thôi việc</option>
                <option value="4" <?php echo ($detail['punishment'] == 4) ?'selected' :''?> >Không chế tài bổ sung</option>
                <option value="5" <?php echo ($detail['punishment'] == 5) ?'selected' :''?> >Khác</option>
            </select>
        </div>
    </div>
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="punishment_desc">Diễn giải chế tài (*)</label>
        
        <textarea name='punishment_desc' placeholder="Nhập..." class="autosize-transition form-control"><?php echo !empty($detail['punishment_desc']) ?$detail['punishment_desc'] :''?></textarea>
    </div>
    


    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="remedy">Hành động khắc phục với KH</label>

        <textarea name='remedy' placeholder="Nhập..." class="autosize-transition form-control"><?php echo !empty($detail['remedy']) ?$detail['remedy'] :''?></textarea>
    </div>

    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="description">Ghi chú</label>

        <textarea name='description' placeholder="Nhập..." class="autosize-transition form-control"><?php echo !empty($detail['description']) ?$detail['description'] :''?></textarea>
    </div>
    <?php if(!empty($detail['created_user'])){ ?>
    <div>
        <label class="control-label padding-20" for="description">Người báo cáo: <span><b><i><?php echo $detail['created_user'];?></i></b></span></label>
    </div>
    <div>
        <label class="control-label padding-20" for="description">Ngày báo cáo: <span><b><i><?php echo date('d-m-Y H:i:s', strtotime($detail['insert_at']));?></i></b></span></label>
    </div>
    <?php } ?>
    <?php if(!empty($detail['modify_user'])){ ?>
    <div>
        <label class="control-label padding-20" for="description">Người sửa: <span><b><i><?php echo $detail['modify_user'];?></i></b></span></label>
    </div>
    <div>
        <label class="control-label padding-20" for="description">Ngày sửa: <span><b><i><?php echo date('d-m-Y H:i:s', strtotime($detail['updated_at']));?></i></b></span></label>
    </div>
    <?php } ?>
    
    <div class="space-4"></div>
    
    <div class="form-group">
        <label class="col-sm-4 no-padding-right"><b style='color: blue'>Dành cho FTQ:</b></label>
    </div>
    
    <div class="space-4"></div>
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="discipline_ftq">FTQ kiểm chứng, điều chỉnh</label>

        <div class="col-sm-8">
            <input class="col-sm-12" type="text" name='discipline_ftq' placeholder="Nhập..." class="col-xs-10 col-sm-5" value="<?php echo !empty($detail['discipline_ftq']) ?$detail['discipline_ftq'] :''?>" />
        </div>
    </div>
    
    <div class="space-4"></div>
    
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="optPunishAdditional">Chế tài bổ sung</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optPunishAdditional'>
                <option value="1" <?php echo ($detail['punishmentAdditional'] == 1) ?'selected' :''?> >Phạt tiền</option>
                <option value="2" <?php echo ($detail['punishmentAdditional'] == 2) ?'selected' :''?> >Cảnh cáo/nhắc nhở</option>
                <option value="3" <?php echo ($detail['punishmentAdditional'] == 3) ?'selected' :''?> >Buộc thôi việc</option>
                <option value="4" <?php echo ($detail['punishmentAdditional'] == 4) ?'selected' :''?> >Không chế tài bổ sung</option>
                <option value="5" <?php echo ($detail['punishmentAdditional'] == 5) ?'selected' :''?> >Khác</option>
            </select>
        </div>
    </div>
    <div class="space-4"></div>

    <div class="form-group" id="accept_staff_dont_has_mistake">
        <label class="col-sm-4 no-padding-right" for="accept_staff_dont_has_mistake"> Xác nhận nhân viên không có lỗi</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='has_mistake'>
                <option value="no" <?php echo ($detail['accept_staff_dont_has_mistake'] == 'no') ?'selected' :''?> >Không chấp nhận</option>
                <option value="yes" <?php echo ($detail['accept_staff_dont_has_mistake'] == 'yes') ?'selected' :''?> >Chấp nhận</option>
            </select>
        </div>
    </div>
    <input type="hidden" name="sID" value="<?php echo $id;?>" />
    <input type="hidden" name="type" value="<?php echo $type;?>" />
    <input type="hidden" name="status" value="<?php echo $status;?>" />
    <?php if(!$flagCount){ ?>
    <button class="btn btn-sm btn-success pull-left" style="margin-top: 20px" >
        <i class="icon-arrow-right icon-on-right bigger-110"></i>
        <span id='btnSave'>Hoàn tất</span>
    </button>
    <?php } ?>
    <button class="btn btn-sm btn-danger pull-left" style="margin: 20px 0px 0px 10px;" data-dismiss="modal">
        <i class="icon-remove"></i>
        Đóng
    </button>
</div>
<script type="text/javascript">
$(document).ready(function() {
    //optType
    if( $( "#optType" ).val() == 12){
       $("#accept_staff_dont_has_mistake").hide();
    }
<?php if($type != 1) {//!= sales?>
    $('#optType option[value=9], #optType option[value=10]').hide();
    $('#optType option[value=4], #optType option[value=5], #optType option[value=6], #optType option[value=7]').show();
<?php } else { ?>
    $('#optType option[value=9], #optType option[value=10]').show();
    $('#optType option[value=4], #optType option[value=5], #optType option[value=6], #optType option[value=7]').hide();
<?php } ?>
    $('#formViolations').validate({
        rules: {
            supporter: {
               required: true
            },
            explanation: {
               required: true
            },
            verify: {
               required: true
            },
            punishment_desc: {
               required: true
            }
        },
        messages: {
            supporter: {
                required: "Bạn cần nhập vào nhân viên thực hiện."
            },
            explanation: {
               required: "Bạn cần nhập vào thông tin giải trình."
            },
            verify: {
               required: "Bạn cần nhập vào kiểm chứng."
            },
            punishment_desc: {
               required: "Bạn cần nhập vào chế tài."
            }
        },
        submitHandler: function(form) {
            if($('#formViolations').valid()){
                $('#formViolations').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>');
                var formData = $('#formViolations').serializeArray(); // data
                $.ajax({
                        url: '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>',
                        cache: false,
                        type: "POST",
                        dataType: "json",
                        data: {'_token': $('input[name=_token]').val(), 'data': formData},
                        success: function (data) {
                            $('#'+ data.object + data.id).html(data.resStatus);
                            $('#modal-table-violation').modal('hide');
                        },
                });
            }
        }
    })
    $('#formViolations').on('submit', function(e) {
        e.preventDefault();
    })
});
</script>
<style>
.error { color: #ff0000; }
</style>