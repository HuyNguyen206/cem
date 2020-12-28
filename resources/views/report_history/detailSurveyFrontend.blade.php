
<p class="question answer"><b>Số HĐ: {{$contract}}</b></p>
<span style="font-size: 14px;"><b>Số điện thoại liên hệ: {{$contactPhone}}</b></span><br>
<span style="font-size: 14px;"><b>Ghi chú: {{$mainNote}}</b></span>

<?php
if (empty($detail)) {//không có câu trả lời khảo sát
    $arrayResult = [0 => 'Không cần liên hệ', 1 => 'Không liên lạc được', 2 => 'Gặp KH,KH từ chối CS', 3 => 'Không gặp người SD'];
    echo "<br><span style='font-size: 14px;'><b> Kết quả liên hệ:</b>". $arrayResult[$connected]."</span>";
} else {
    $i = 0;
    $len = count($detail);
// 1 câu hỏi có nhiều câu tra lời sử 
// chỉ lặp câu hỏi không lặp kết quả trả lờ
    $flag = $tempQuestion = '';
    foreach ($detail as $detailSurvey) {
        if ($detailSurvey->question_id != $flag) {
            $flag = $detailSurvey->question_id;
            echo "<p class='question'>" . $detailSurvey->question_title . "</p>";
        }
        ?>

        <?php
//        $colorText = ($detailSurvey->survey_result_answer_id == '-1') ? 'text-warning' : 'text-primary';
        $colorText = 'text-primary';
        if (is_numeric($detailSurvey->answers_title)) {
            if ($detailSurvey->answers_title >= 0 && $detailSurvey->answers_title <= 6) {
                $detailSurvey->answers_title = $detailSurvey->answers_title . ' (Không ủng hộ)';
            } else if ($detailSurvey->answers_title >= 7 && $detailSurvey->answers_title <= 8) {
                $detailSurvey->answers_title = $detailSurvey->answers_title . ' (Trung lập)';
            } else {
                $detailSurvey->answers_title = $detailSurvey->answers_title . ' (Ủng hộ)';
            }
        }
        if (!empty($detailSurvey->survey_result_note)) {
            $detailSurvey->survey_result_note = $detailSurvey->survey_result_note;
        }
//        if ($tempQuestion == $detailSurvey->question_id) {//nếu câu hỏi có nhiều câu trả lời, có ghi chú thì chỉ hiện ghi chú ở 1 câu trả lời
//            $detailSurvey->survey_result_note = '';
//        }
        if (!empty($detailSurvey->answers_extra_title)) {
            $detailSurvey->answers_extra_title = $detailSurvey->answers_extra_title;
        }
        ?>
        <p class="answer {{$colorText}}">
            <b>
                <?php if ($detailSurvey->survey_result_answer_id != -1) { ?>               
                    {{$detailSurvey->answers_title}} 
                    <?php
                } else {
                    ?>
                    {{ $detailSurvey->answers_extra_title}}
                <?php } ?><?php
                //Không phải câu 5,7 và ko phải cuối vòng lặp
                if ($detailSurvey->question_id != 5 && $detailSurvey->question_id != 7 && $i != $len - 1) {
                    ?><?php if ($detailSurvey->survey_result_note != '' && $detailSurvey->survey_result_note != NULL) { ?><br><?php } ?>
                    {{$detailSurvey->survey_result_note}}</b>

                <?php
            }
            //Câu 5,7 và ko phải cuối vòng lặp
        
            //Câu 5,7 và cuối vòng lặp
            else if (($detailSurvey->question_id == 5 || $detailSurvey->question_id == 7) && $i == $len - 1) {
                ?>
                <br>
                {{$detailSurvey->survey_result_note}}</b>
        <?php } ?>

        <!--<br/>-->
        </p>
        <?php
        $tempQuestion = $detailSurvey->question_id;
        $i++;
    }
}
?>
<style>
    .question {
        font-size: medium;
    }
    .answer {
        font-size: large;
    }
</style>