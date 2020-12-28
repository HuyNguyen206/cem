<?php

$ResultSurvey = [0 => "Không cần liên hệ", 1 => "Không liên lạc được", 2 => "Gặp KH, KH từ chối CS", 3 => "Không gặp người SD", 4 => "Gặp người SD"];
?>
<div class="container">          
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Lần khảo sát</th>
                <th>Nhân viên khảo sát</th>
                <th>Thời gian hoàn thành</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($timeHistory as $key => $value) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo $i++;
                        ?>    
                    </td>
                    <td>
                        <?php
                        echo $value->section_user_name;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $value->section_time_completed;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $ResultSurvey[$value->section_connected];
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

