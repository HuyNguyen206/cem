<?php 
$controller = 'history';
$transFile = 'violations';
$prefix = main_prefix;
?>
<div class="col-xs-12">
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="supporterName">
            @if(in_array($type, [1,4]))
                {{trans($transFile.'.SaleStaffName')}}
            @else
                {{trans($transFile.'.TechnicalStaffName')}}
            @endif
            (*)
        </label>

        <div class="col-sm-8">
            <input class="col-sm-12" type="text" name='supporterName' placeholder="{{trans($transFile.'.InputHere')}}..." class="col-xs-10 col-sm-5" value="{{!empty($detail['supporterName']) ?$detail['supporterName'] :''}}" />
        </div>
    </div>

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="supporterID">
            {{trans($transFile.'.StaffID')}}(*)
        </label>

        <div class="col-sm-8">
            <input class="col-sm-12" type="text" name='supporterID' placeholder="{{trans($transFile.'.InputHere')}}..." class="col-xs-10 col-sm-5" value="{{!empty($detail['supporterID']) ?$detail['supporterID'] :''}}" />
        </div>
    </div>
    
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="explanationDescription">{{trans($transFile.'.ThePersonalGroupUnitsInformationOfExplanationRelatingTo')}} (*)</label>
        
        <textarea name='explanationDescription' placeholder="{{trans($transFile.'.InputHere')}}..." class="autosize-transition form-control" rows="10">{{!empty($detail['explanationDescription']) ?$detail['explanationDescription'] :''}}</textarea>
    </div>
    
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="verify">{{trans($transFile.'.VerificationManagement')}} (*)</label>
        
        <textarea name='verify' placeholder="{{trans($transFile.'.InputHere')}}..." class="autosize-transition form-control" rows="10">{{!empty($detail['qs_verify']) ?$detail['qs_verify'] :''}}</textarea>
    </div>
    
    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="optType">{{trans($transFile.'.TypesOfViolations')}} (*)</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optType' id="optType">
                @foreach($violationTypes as $violationType)
                    <option value="{{$violationType->answer_id}}" @if(!empty($detail['violationsType']) && ($detail['violationsType'] == $violationType->answer_id)) selected @endif>{{trans($transFile.'.'.$violationType->answers_key)}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="punishment">{{trans($transFile.'.RemediesPenalty')}} (*)</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optPunish'>
                @foreach($punishments as $punishment)
                    <option value="{{$punishment->answer_id}}" @if(!empty($detail['punishment']) && ($detail['punishment'] == $punishment->answer_id)) selected @endif>{{trans($transFile.'.'.$punishment->answers_key)}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="punishmentDescription">{{trans($transFile.'.SanctionedExplanation')}} (*)</label>
        
        <textarea name='punishmentDescription' placeholder="{{trans($transFile.'.InputHere')}}..." class="autosize-transition form-control"><?php echo !empty($detail['punishmentDescription']) ?$detail['punishmentDescription'] :''?></textarea>
    </div>
    


    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="remedy">{{trans($transFile.'.CorrectiveActionWithTheCustomerSolutionForCustomer')}}</label>

        <textarea name='remedy' placeholder="{{trans($transFile.'.InputHere')}}..." class="autosize-transition form-control"><?php echo !empty($detail['remedy']) ?$detail['remedy'] :''?></textarea>
    </div>

    <div class="space-4"></div>

    <div>
        <label class="control-label padding-20" for="description">{{trans($transFile.'.Note')}}</label>

        <textarea name='Note' placeholder="{{trans($transFile.'.InputHere')}}..." class="autosize-transition form-control"><?php echo !empty($detail['Note']) ?$detail['Note'] :''?></textarea>
    </div>
    @if(!empty($detail['created_user']))
        <div>
            <label class="control-label padding-20" for="description">{{trans($transFile.'.ReportBy')}}: <span><b><i>{{$detail['created_user']}}</i></b></span></label>
        </div>
        <div>
            <label class="control-label padding-20" for="description">{{trans($transFile.'.ReportAt')}}: <span><b><i>{{date('d-m-Y H:i:s', strtotime($detail['insert_at']))}}</i></b></span></label>
        </div>
    @endif
    @if(!empty($detail['modify_user']))
        <div>
            <label class="control-label padding-20" for="description">{{trans($transFile.'.EditBy')}}: <span><b><i>{{$detail['modify_user']}}</i></b></span></label>
        </div>
        <div>
            <label class="control-label padding-20" for="description">{{trans($transFile.'.EditAt')}}: <span><b><i>{{date('d-m-Y H:i:s', strtotime($detail['updated_at']))}}</i></b></span></label>
        </div>
    @endif
    
    <div class="space-4"></div>
    
    <div class="form-group">
        <label class="col-sm-4 no-padding-right"><b style='color: blue'>{{trans($transFile.'.ForFTQ')}}:</b></label>
    </div>
    
    <div class="space-4"></div>
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="discipline_ftq">{{trans($transFile.'.QAVerifyAdjust')}}</label>

        <div class="col-sm-8">
            <input class="col-sm-12" type="text" name='discipline_ftq' placeholder="{{trans($transFile.'.InputHere')}}..." class="col-xs-10 col-sm-5" value="<?php echo !empty($detail['discipline_ftq']) ?$detail['discipline_ftq'] :''?>" />
        </div>
    </div>
    
    <div class="space-4"></div>
    
    <div class="form-group">
        <label class="col-sm-4 no-padding-right" for="optPunishAdditional">{{trans($transFile.'.AdditionalRemediesPenaltyAdded')}}</label>

        <div class="col-sm-8">
            <select class="col-sm-12" name='optPunishAdditional'>
                @foreach($punishments as $punishment)
                    <option value="{{$punishment->answer_id}}" @if(!empty($detail['punishmentAdditional']) && ($detail['punishmentAdditional'] == $punishment->answer_id)) selected @endif>{{trans($transFile.'.'.$punishment->answers_key)}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="space-4"></div>

    <div class="form-group" id="accept_staff_dont_has_mistake">
        <label class="col-sm-4 no-padding-right" for="accept_staff_dont_has_mistake">{{trans($transFile.'.ConfirmationOfStaffsHaveNoMistakes')}}</label>
        <div class="col-sm-8">
            <select class="col-sm-12" name='has_mistake'>
                <option value="no" {{!empty($detail['accept_staff_dont_has_mistake']) && ($detail['accept_staff_dont_has_mistake'] == 'no') ?'selected' :''}} >{{trans($transFile.'.DoNotAccept')}}</option>
                <option value="yes" {{!empty($detail['accept_staff_dont_has_mistake']) && ($detail['accept_staff_dont_has_mistake'] == 'yes') ?'selected' :''}} >{{trans($transFile.'.Accept')}}</option>
            </select>
        </div>
    </div>
    <input type="hidden" name="sID" value="{{$id}}" />
    <input type="hidden" name="type" value="{{$type}}" />
    <input type="hidden" name="status" value="{{$status}}" />
    @if(!$flagCount)
    <button class="btn btn-sm btn-success pull-left" style="margin-top: 20px" >
        <i class="icon-arrow-right icon-on-right bigger-110"></i>
        <span id='btnSave'>{{trans($transFile.'.Done')}}</span>
    </button>
    @endif
    <button class="btn btn-sm btn-danger pull-left" style="margin: 20px 0px 0px 10px;" data-dismiss="modal">
        <i class="icon-remove"></i>
        {{trans($transFile.'.Close')}}
    </button>
</div>
<script type="text/javascript">
$(document).ready(function() {
    //optType\
    $("#accept_staff_dont_has_mistake").hide();
    if( $( "#optType" ).val() == 217  || $( "#optType" ).val() == 227){
       $("#accept_staff_dont_has_mistake").show();
    }

    $('#formViolations').validate({
        rules: {
            supporterName: {
               required: true
            },
            supporterID: {
                required: true
            },
            explanationDescription: {
               required: true
            },
            verify: {
               required: true
            },
            punishmentDescription: {
               required: true
            }
        },
        messages: {
            supporterName: {
                required: "{{trans($transFile.'.MustFillNameOfStaff')}}!"
            },
            supporterID: {
                required: "{{trans($transFile.'.MustFillIDOfStaff')}}!"
            },
            explanationDescription: {
               required: "{{trans($transFile.'.MustFillExplanation')}}!"
            },
            verify: {
               required: "{{trans($transFile.'.MustFillVerificationManagement')}}!"
            },
            punishmentDescription: {
               required: "{{trans($transFile.'.MustFillSanctionedExplanation')}}!"
            }
        },
        submitHandler: function(form) {
            if($('#formViolations').valid()){
                $('#formViolations').attr('action', '{{url('/' . $prefix . '/' . $controller . '/save-violation')}}');
                var formData = $('#formViolations').serializeArray(); // data
                $.ajax({
                        url: '{{url('/' . $prefix . '/' . $controller . '/save-violation')}}',
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