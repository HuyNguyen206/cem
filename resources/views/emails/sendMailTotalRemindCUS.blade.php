<b>Thông báo từ Hệ thống KS-CSKH “Customer Voice”- <span style="color: #FF0000;">Danh sách các hợp đồng CSAT 1,2 chưa được báo cáo xử lý</span></b><br/>
Tổng cộng <b>{{$param['total']}}</b> hợp đồng.<br/>
Danh sách:
<table border="1" style="border-collapse: collapse;">
	<tr>
		<td>STT</td>
		<td>Hợp đồng</td>
		<td>Email quản lý</td>
		<td>Chi nhánh</td>
		<td>Thời gian ghi nhận</td>
	</tr>
	@foreach($param['detail'] as $key => $result)
		<tr>
			<td>{{$key + 1}}</td>
			<td>{{$result->section_contract_num}}</td>
			<td>{{$result->email_list}}</td>
			<td>{{$result->branch_name.' - '.$result->branch_code}}</td>
			<td>{{$result->section_time_completed}}</td>
		</tr>
	@endforeach
</table>
@if($param['total'] - count($param['detail']) != 0)
	Lưu ý: Có <b>{{$param['total'] - count($param['detail'])}}</b> hợp đồng chưa có thông tin email quản lý.
@endif
<br/>
Quản lý nhận được email phải vào Tool Customer Voice(<a href="https://cem.fpt.vn">https://cem.fpt.vn</a>) tìm hiểu trường hợp phản ánh chi tiết của KH và tiến hành xử lý như Quy định!</br>