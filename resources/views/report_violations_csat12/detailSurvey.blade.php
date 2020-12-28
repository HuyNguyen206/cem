<p class="question answer"><b>Số HĐ: {{$contract}}</b></p>
<p class="question answer"><b>Số điện thoại liên hệ: {{$phone}}</b></p>
<?php
    if(empty($detail)){//không có câu trả lời khảo sát
        echo "Khách hàng không trả lời khảo sát.";
    } else {
    	// 1 câu hỏi có nhiều câu tra lời sử 
    	// chỉ lặp câu hỏi không lặp kết quả trả lờ
    	$flag = $tempQuestion = '';
        foreach ($detail as $detailSurvey){
            if ($detailSurvey->question_id != $flag ){
        		$flag = $detailSurvey->question_id ;
        		echo "<p class='question'>".$detailSurvey->question_title."</p>";
        	}
             ?>
    
    <?php
        $colorText = ($detailSurvey->survey_result_answer_id == '-1') ?'text-warning' :'text-primary';
        if(is_numeric($detailSurvey->answers_title)){
            if($detailSurvey->answers_title >= 0 && $detailSurvey->answers_title <= 6) {
                $detailSurvey->answers_title = $detailSurvey->answers_title.' (Không ủng hộ)';
            } else if($detailSurvey->answers_title >= 7 && $detailSurvey->answers_title <= 8) {
                $detailSurvey->answers_title = $detailSurvey->answers_title.' (Trung lập)';
            } else {
                $detailSurvey->answers_title = $detailSurvey->answers_title.' (Ủng hộ)';
            }
        }
        if(!empty($detailSurvey->survey_result_note)){
            $detailSurvey->survey_result_note = '('.$detailSurvey->survey_result_note.')';
        }
        if ($tempQuestion == $detailSurvey->question_id) {//nếu câu hỏi có nhiều câu trả lời, có ghi chú thì chỉ hiện ghi chú ở 1 câu trả lời
            $detailSurvey->survey_result_note = '';
        }
        if(!empty($detailSurvey->answers_extra_title)){
            $detailSurvey->answers_extra_title = 'Câu trả lời phụ: '.$detailSurvey->answers_extra_title;
        }
    ?>
    <p class="answer {{$colorText}}">
        <b>{{$detailSurvey->answers_title}} {{$detailSurvey->survey_result_note}}</b>
        <br/><b>{{$detailSurvey->answers_extra_title}}</b>
    </p>
    <?php 
        $tempQuestion = $detailSurvey->question_id;
        }
    } ?>
<style>
    .question {
        font-size: medium;
    }
    .answer {
        font-size: large;
    }
</style>