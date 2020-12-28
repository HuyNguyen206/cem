<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th class="center">STT</th>
        <th><i class="icon-time bigger-120"></i>{{trans('common.DayRecord')}}</th>
        <th><i class="icon-phone bigger-120"></i>{{trans('common.Phone')}}</th>
        <th>{{trans('common.Action')}}Hành động</th>
    </tr>
    </thead>
    <tbody>
@if(!empty($listVoiceRecord))
    @foreach($listVoiceRecord as $index => $voice)

        <tr>
           <td class="center">
               {{$index + 1}}
           </td>
            <td>
                {{$voice['StopRecordTime']}}
            </td>
            <td>
                {{$voice['CalledID']}}
            </td>
            <td>
                <audio class="audio_class" controls id="audio_control_1">
                    <source src="http://172.27.16.78:2122/{{$voice['idCharsetRecord']}}"
                            type="audio/wav">
                </audio>
            </td>
        </tr>
    @endforeach
    @else
    <tr>
        <td colspan="4">
            {{trans('common.NotFoundVoiceRecord')}}
        </td>

    </tr>
    @endif

    </tbody>
</table>

<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#time_from').datetimepicker({
            format: 'dd-mm-yyyy h:i:s',
            autoclose: true,
        });
        $('#time_to').datetimepicker({
            format: 'dd-mm-yyyy h:i:s',
            autoclose: true,
        });
    });
</script>
