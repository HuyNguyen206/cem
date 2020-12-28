<?php

namespace App\Component;

class BuildDataCSATExcel {

    //--------------------------------public function---------------------------------
    //--------------------------------csat Dịch vụ------------------------------------
    public function formatExcelCsatServiceGeneral(&$sheet, $needObject, $arrayTypeSurvey) {
        $this->formatExcelGeneral($sheet, $needObject);

        //Tiêu đề 1, 1.1
        $rowBegin = 'rowBeginTitle1';
        $rowEnd = 'rowEndTitle1';
        $colEnd = 'colEndTable1';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 1.1
        $rowBegin = 'rowBeginTable1';
        $rowEnd = 'rowEndTable1';
        $i = 4;
        $j = 5;
        $arrayColumnFormat = [];
        foreach ($arrayTypeSurvey as $key => $value) {
            $arrayColumnFormat[$needObject->columnName[$i] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$i] . ($needObject->$rowEnd)] = '0.00%';
            $arrayColumnFormat[$needObject->columnName[$j] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$j] . ($needObject->$rowEnd)] = '0.00';
            $i+=5;
            $j = $i + 1;
        }
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat($arrayColumnFormat);

        //Tiêu đề 1.2
        $rowBegin = 'rowBeginTitle2';
        $rowEnd = 'rowEndTitle2';
        $colEnd = 'colEndTable2';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 1.2
        $rowBegin = 'rowBeginTable2';
        $rowEnd = 'rowEndTable2';
        $i = 4;
        $j = 5;
        $arrayColumnFormat = [];
        foreach ($arrayTypeSurvey as $key => $value) {
            $arrayColumnFormat[$needObject->columnName[$i] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$i] . ($needObject->$rowEnd)] = '0.00%';
            $arrayColumnFormat[$needObject->columnName[$j] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$j] . ($needObject->$rowEnd)] = '0.00';
            $i+=5;
            $j = $i + 1;
        }
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd), function($cells) {
                    $cells->setBackground('#FFC000');
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat($arrayColumnFormat);

        //Tiêu đề 2 và 2.1
        $rowBegin = 'rowBeginTitle3';
        $rowEnd = 'rowEndTitle3';
        $colEnd = 'colEndTable3';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 2.1
        $rowBegin = 'rowBeginTable3';
        $rowEnd = 'rowEndTable3';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[12] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[12] . ($needObject->$rowEnd), function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[19] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[19] . ($needObject->$rowEnd), function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . ($needObject->$rowEnd) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd) => '0.00%',
        ));

        //Tiêu đề 2.2
        $rowBegin = 'rowBeginTitle4';
        $rowEnd = 'rowEndTitle4';
        $colEnd = 'colEndTable4';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 2.2
        $rowBegin = 'rowBeginTable4';
        $rowEnd = 'rowEndTable4';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[15] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[15] . ($needObject->$rowEnd), function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[22] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[22] . ($needObject->$rowEnd), function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . ($needObject->$rowEnd) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd) => '0.00%',
        ));

        //Tiêu đề 3 và 3.1
        $rowBegin = 'rowBeginTitle5';
        $rowEnd = 'rowEndTitle5';
        $colEnd = 'colEndTable5';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 3.1
        $rowBegin = 'rowBeginTable5';
        $rowEnd = 'rowEndTable5';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setBackground('#9BC2E6');
        })
            ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            })
            ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd), function($cells) {
                $cells->setBackground('#FFC000');
            })
            ->cells($needObject->columnName[12] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[12] . ($needObject->$rowEnd), function($cells) {
                $cells->setFontColor('#FF0000');
            })
            ->cells($needObject->columnName[16] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[16] . ($needObject->$rowEnd), function($cells) {
                $cells->setFontColor('#FF0000');
            })
            ->setColumnFormat(array(
                $needObject->columnName[1] . ($needObject->$rowEnd) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd) => '0.00%',
            ));

        //Tiêu đề 3.2
        $rowBegin = 'rowBeginTitle6';
        $rowEnd = 'rowEndTitle6';
        $colEnd = 'colEndTable6';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 3.2
        $rowBegin = 'rowBeginTable6';
        $rowEnd = 'rowEndTable6';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setBackground('#9BC2E6');
        })
            ->cells($needObject->columnName[0] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            })
            ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd), function($cells) {
                $cells->setBackground('#FFC000');
            })
            ->cells($needObject->columnName[15] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[15] . ($needObject->$rowEnd), function($cells) {
                $cells->setFontColor('#FF0000');
            })
            ->cells($needObject->columnName[19] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[19] . ($needObject->$rowEnd), function($cells) {
                $cells->setFontColor('#FF0000');
            })
            ->setColumnFormat(array(
                $needObject->columnName[1] . ($needObject->$rowEnd) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd) => '0.00%',
            ));
    }

    public function measureBorderCsatServiceExcel($dataExcel, $template, $sendMail = false) {
        $needObject = new \stdClass;
        $needObject->numTable = 6;
        if ($dataExcel['viewStatus'] == 0) {
            $needObject->numRowEachTable = count(explode(',', $dataExcel['region']));
        } else {
            $needObject->numRowEachTable = count($dataExcel['branch']);
        }

        //Phan dau
        if ($sendMail) {
            $needObject->rowBeginSubject = 3;
        } else {
            $needObject->rowBeginSubject = 1;
        }
        $needObject->rowEndSubject = $needObject->rowBeginSubject + 2;

        //Table 1
        $needObject->rowBeginTitle1 = $needObject->rowEndSubject + 2;
        $needObject->rowEndTitle1 = $needObject->rowBeginTitle1 + 1;
        $needObject->rowBeginTable1 = $needObject->rowEndTitle1 + 2;
        $needObject->rowEndTable1 = 1 + $needObject->rowBeginTable1 + $needObject->numRowEachTable + 1;

        //Table 2
        $needObject->rowBeginTitle2 = $needObject->rowEndTable1 + 2;
        $needObject->rowEndTitle2 = $needObject->rowBeginTitle2;
        $needObject->rowBeginTable2 = $needObject->rowEndTitle2 + 2;
        $needObject->rowEndTable2 = 1 + $needObject->rowBeginTable2 + $needObject->numRowEachTable + 1;

        //Table 3
        $needObject->rowBeginTitle3 = $needObject->rowEndTable2 + 2;
        $needObject->rowEndTitle3 = $needObject->rowBeginTitle3 + 1;
        $needObject->rowBeginTable3 = $needObject->rowEndTitle3 + 2;
        $needObject->rowEndTable3 = 1 + $needObject->rowBeginTable3 + $needObject->numRowEachTable + 2;

        //Table 4
        $needObject->rowBeginTitle4 = $needObject->rowEndTable3 + 2;
        $needObject->rowEndTitle4 = $needObject->rowBeginTitle4;
        $needObject->rowBeginTable4 = $needObject->rowEndTitle4 + 2;
        $needObject->rowEndTable4 = 1 + $needObject->rowBeginTable4 + $needObject->numRowEachTable + 2;

        //Table 5
        $needObject->rowBeginTitle5 = $needObject->rowEndTable4 + 2;
        $needObject->rowEndTitle5 = $needObject->rowBeginTitle5 + 1;
        $needObject->rowBeginTable5 = $needObject->rowEndTitle5 + 2;
        $needObject->rowEndTable5 = 1 + $needObject->rowBeginTable5 + $needObject->numRowEachTable + 2;

        //Table 6
        $needObject->rowBeginTitle6 = $needObject->rowEndTable5 + 2;
        $needObject->rowEndTitle6 = $needObject->rowBeginTitle6;
        $needObject->rowBeginTable6 = $needObject->rowEndTitle6 + 2;
        $needObject->rowEndTable6 = 1 + $needObject->rowBeginTable6 + $needObject->numRowEachTable + 2;

        $needObject->columnWidth = [
            /* 1 */ 'A' => 15,
            /* 2 */ 'B' => 10,
            /* 3 */ 'C' => 10,
            /* 4 */ 'D' => 10,
            /* 5 */ 'E' => 10,
            /* 6 */ 'F' => 10,
            /* 7 */ 'G' => 10,
            /* 8 */ 'H' => 10,
            /* 9 */ 'I' => 10,
            /* 10 */ 'J' => 10,
            /* 11 */ 'K' => 10,
            /* 12 */ 'L' => 10,
            /* 13 */ 'M' => 10,
            /* 14 */ 'N' => 10,
            /* 15 */ 'O' => 10,
            /* 16 */ 'P' => 10,
            /* 17 */ 'Q' => 10,
            /* 18 */ 'R' => 10,
            /* 19 */ 'S' => 10,
            /* 20 */ 'T' => 10,
            /* 21 */ 'U' => 10,
            /* 22 */ 'V' => 10,
            /* 23 */ 'W' => 10,
            /* 24 */ 'X' => 10,
            /* 25 */ 'Y' => 10,
            /* 26 */ 'Z' => 10,
            /* 27 */ 'AA' => 10,
            /* 28 */ 'AB' => 10,
            /* 29 */ 'AC' => 10,
            /* 30 */ 'AD' => 10,
            /* 31 */ 'AE' => 10,
            /* 32 */ 'AF' => 10,
            /* 33 */ 'AG' => 10,
            /* 34 */ 'AH' => 10,
            /* 35 */ 'AI' => 10,
            /* 36 */ 'AJ' => 10,
            /* 37 */ 'AK' => 10,
            /* 38 */ 'AL' => 10,
            /* 39 */ 'AM' => 10,
            /* 40 */ 'AN' => 10,
            /* 41 */ 'AO' => 10,
        ];
        $needObject->columnName = array_keys($needObject->columnWidth);
        $needObject->colEndTable1 = 41;
        $needObject->colEndTable2 = 41;
        $needObject->colEndTable3 = 20;
        $needObject->colEndTable4 = 23;
        $needObject->colEndTable5 = 17;
        $needObject->colEndTable6 = 20;

        $needObject->colMaxColTable = 41;
        $needObject->template = $template;
        $needObject->dataExcel = $dataExcel;
        return $needObject;
    }

    public function formatExcelCsatServiceDetailNet(&$sheet, $needObject) {
        $this->formatExcelGeneral($sheet, $needObject);

        $rowBegin = 'rowBeginTable1';
        $rowEnd = 'rowEndTable1';
        $colEnd = 'colEndTable1';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
        ->setAutoFilter($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin));
    }

    public function measureBorderCsatServiceExcelDetailNet($dataExcel, $template, $sendMail = false) {
        $needObject = new \stdClass;
        $needObject->numTable = 1;
        if ($dataExcel['viewStatus'] == 0) {
            $needObject->numRowEachTable = count(explode(',', $dataExcel['region']));
        } else {
            $needObject->numRowEachTable = $dataExcel['rowEnd']['net'];
        }

        //Phan dau
        if ($sendMail) {
            $needObject->rowBeginSubject = 3;
        } else {
            $needObject->rowBeginSubject = 1;
        }
        $needObject->rowEndSubject = $needObject->rowBeginSubject + 2;

        //Table 1
        $needObject->rowBeginTable1 = $needObject->rowEndSubject + 2;
        $needObject->rowEndTable1 = $needObject->rowBeginTable1 + $needObject->numRowEachTable;

        $needObject->columnWidth = [
            /* 1 */ 'A' => 2,
            /* 2 */ 'B' => 8,
            /* 3 */ 'C' => 15,
            /* 4 */ 'D' => 15,
            /* 5 */ 'E' => 15,
            /* 6 */ 'F' => 15,
            /* 7 */ 'G' => 25,
            /* 8 */ 'H' => 30,
            /* 9 */ 'I' => 20,
            /* 10 */ 'J' => 10,
            /* 11 */ 'K' => 35,
            /* 12 */ 'L' => 50,
            /* 13 */ 'M' => 40,
            /* 14 */ 'N' => 10,
            /* 15 */ 'O' => 10,
            /* 16 */ 'P' => 10,
            /* 17 */ 'Q' => 10,
            /* 18 */ 'R' => 10,
            /* 19 */ 'S' => 10,
            /* 20 */ 'T' => 10,
            /* 21 */ 'U' => 10,
            /* 22 */ 'V' => 10,
            /* 23 */ 'W' => 10,
            /* 24 */ 'X' => 10,
            /* 25 */ 'Y' => 10,
            /* 26 */ 'Z' => 10,
            /* 27 */ 'AA' => 10,
            /* 28 */ 'AB' => 10,
            /* 29 */ 'AC' => 10,
            /* 30 */ 'AD' => 10,
            /* 31 */ 'AE' => 10,
        ];
        $needObject->columnName = array_keys($needObject->columnWidth);
        $needObject->colEndTable1 = 13;

        $needObject->colMaxColTable = 13;
        $needObject->template = $template;
        $needObject->dataExcel = $dataExcel;
        return $needObject;
    }

    public function formatExcelCsatServiceDetailTv(&$sheet, $needObject) {
        $this->formatExcelGeneral($sheet, $needObject);

        $rowBegin = 'rowBeginTable1';
        $rowEnd = 'rowEndTable1';
        $colEnd = 'colEndTable1';
        $sheet->cells($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->setAutoFilter($needObject->columnName[0] . ($needObject->$rowBegin) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin));
    }

    public function measureBorderCsatServiceExcelDetailTv($dataExcel, $template, $sendMail = false) {
        $needObject = new \stdClass;
        $needObject->numTable = 1;
        if ($dataExcel['viewStatus'] == 0) {
            $needObject->numRowEachTable = count(explode(',', $dataExcel['region']));
        } else {
            $needObject->numRowEachTable = $dataExcel['rowEnd']['tv'];
        }

        //Phan dau
        if ($sendMail) {
            $needObject->rowBeginSubject = 3;
        } else {
            $needObject->rowBeginSubject = 1;
        }
        $needObject->rowEndSubject = $needObject->rowBeginSubject + 2;

        //Table 1
        $needObject->rowBeginTable1 = $needObject->rowEndSubject + 2;
        $needObject->rowEndTable1 = $needObject->rowBeginTable1 + $needObject->numRowEachTable;

        $needObject->columnWidth = [
            /* 1 */ 'A' => 2,
            /* 2 */ 'B' => 8,
            /* 3 */ 'C' => 15,
            /* 4 */ 'D' => 15,
            /* 5 */ 'E' => 15,
            /* 6 */ 'F' => 15,
            /* 7 */ 'G' => 25,
            /* 8 */ 'H' => 30,
            /* 9 */ 'I' => 20,
            /* 10 */ 'J' => 10,
            /* 11 */ 'K' => 35,
            /* 12 */ 'L' => 50,
            /* 13 */ 'M' => 40,
            /* 14 */ 'N' => 10,
            /* 15 */ 'O' => 10,
            /* 16 */ 'P' => 10,
            /* 17 */ 'Q' => 10,
            /* 18 */ 'R' => 10,
            /* 19 */ 'S' => 10,
            /* 20 */ 'T' => 10,
            /* 21 */ 'U' => 10,
            /* 22 */ 'V' => 10,
            /* 23 */ 'W' => 10,
            /* 24 */ 'X' => 10,
            /* 25 */ 'Y' => 10,
            /* 26 */ 'Z' => 10,
            /* 27 */ 'AA' => 10,
            /* 28 */ 'AB' => 10,
            /* 29 */ 'AC' => 10,
            /* 30 */ 'AD' => 10,
            /* 31 */ 'AE' => 10,
        ];
        $needObject->columnName = array_keys($needObject->columnWidth);
        $needObject->colEndTable1 = 13;

        $needObject->colMaxColTable = 13;
        $needObject->template = $template;
        $needObject->dataExcel = $dataExcel;
        return $needObject;
    }

    //--------------------------------csat nhân viên----------------------------------
    public function formatExcelCsatStaffGeneral(&$sheet, $needObject) {
        $this->formatExcelGeneral($sheet, $needObject);

        //Tiêu đề 1, 1.1
        $rowBegin = 'rowBeginTitle1';
        $rowEnd = 'rowEndTitle1';
        $colEnd = 'colEndTable1';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 1.1
        $rowBegin = 'rowBeginTable1';
        $rowEnd = 'rowEndTable1';
        $colEnd = 'colEndTable1';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#9BC2E6');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[5] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[5] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[10] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[10] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[6] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[6] . ($needObject->$rowEnd) => '0.00',
        ));

        //Tiêu đề 1.2
        $rowBegin = 'rowBeginTitle2';
        $rowEnd = 'rowEndTitle2';
        $colEnd = 'colEndTable2';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 1.2
        $rowBegin = 'rowBeginTable2';
        $rowEnd = 'rowEndTable2';
        $colEnd = 'colEndTable2';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#9BC2E6');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[5] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[5] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[10] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[10] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[15] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[15] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[20] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[20] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[25] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[25] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[30] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[30] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[6] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[6] . ($needObject->$rowEnd) => '0.00',
                    $needObject->columnName[16] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[16] . ($needObject->$rowEnd) => '0.00',
                    $needObject->columnName[26] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[26] . ($needObject->$rowEnd) => '0.00',
        ));
       
        //Tiêu đề 1.3
        $rowBegin = 'rowBeginTitle3';
        $rowEnd = 'rowEndTitle3';
        $colEnd = 'colEndTable3';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 1.3
        $rowBegin = 'rowBeginTable3';
        $rowEnd = 'rowEndTable3';
        $colEnd = 'colEndTable3';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#9BC2E6');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[5] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[5] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[10] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[10] . ($needObject->$rowEnd) => '0.00%',
                    $needObject->columnName[6] . ($needObject->$rowBegin + 3) . ':' . $needObject->columnName[6] . ($needObject->$rowEnd) => '0.00',
        ));
                
        //Tiêu đề 2 và 2.1
        $rowBegin = 'rowBeginTitle4';
        $rowEnd = 'rowEndTitle4';
        $colEnd = 'colEndTable4';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 2.1
        $rowBegin = 'rowBeginTable4';
        $rowEnd = 'rowEndTable4';
        $colEnd = 'colEndTable4';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
            $cells->setBackground('#9BC2E6');
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $sheet->mergeCells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#BDD7EE');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 2] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd - 1), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[9] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[9] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[15] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[15] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 2] . $needObject->$rowEnd => '0.00%',
        ));

        //Tiêu đề 2.2
        $rowBegin = 'rowBeginTitle5';
        $rowEnd = 'rowEndTitle5';
        $colEnd = 'colEndTable5';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 2.2
        $rowBegin = 'rowBeginTable5';
        $rowEnd = 'rowEndTable5';
        $colEnd = 'colEndTable5';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
            $cells->setBackground('#9BC2E6');
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $sheet->mergeCells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#BDD7EE');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 2] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd - 1), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[11] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[11] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[17] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[17] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 2] . $needObject->$rowEnd => '0.00%',
        ));

        //Bảng 2.3
        $rowBegin = 'rowBeginTable6';
        $rowEnd = 'rowEndTable6';
        $colEnd = 'colEndTable6';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
            $cells->setBackground('#9BC2E6');
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $sheet->mergeCells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#BDD7EE');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 2] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd - 1), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[11] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[11] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[17] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[17] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 2] . $needObject->$rowEnd => '0.00%',
        ));

        //Bảng 2.4
        $rowBegin = 'rowBeginTable7';
        $rowEnd = 'rowEndTable7';
        $colEnd = 'colEndTable7';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
            $cells->setBackground('#9BC2E6');
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $sheet->mergeCells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#BDD7EE');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 2] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd - 1), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[11] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[11] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[17] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[17] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 2] . $needObject->$rowEnd => '0.00%',
        ));
                
                 //Tiêu đề 2.5
        $rowBegin = 'rowBeginTitle8';
        $rowEnd = 'rowEndTitle8';
        $colEnd = 'colEndTable8';
        $this->formatTitleGeneral($sheet, $needObject, $rowBegin, $rowEnd, $colEnd);

        //Bảng 2.5
        $rowBegin = 'rowBeginTable8';
        $rowEnd = 'rowEndTable8';
        $colEnd = 'colEndTable8';
        $sheet->mergeCells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[0] . $needObject->$rowBegin . ':' . $needObject->columnName[0] . ($needObject->$rowBegin + 2), function($cells) {
            $cells->setBackground('#9BC2E6');
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
        $sheet->mergeCells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2));
        $sheet->cells($needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setBackground('#BDD7EE');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                })
                ->cells($needObject->columnName[1] . $needObject->$rowBegin . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowBegin, function($cells) {
                    $cells->setBackground('#8DB4E2');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 1) . ':' . $needObject->columnName[$needObject->$colEnd - 2] . ($needObject->$rowBegin + 1), function($cells) {
                    $cells->setBackground('#9BC2E6');
                })
                ->cells($needObject->columnName[1] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowBegin + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                })
                ->cells($needObject->columnName[0] . ($needObject->$rowEnd - 1) . ':' . $needObject->columnName[$needObject->$colEnd - 1] . ($needObject->$rowEnd - 1), function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[0] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $needObject->$rowEnd, function($cells) {
                    $cells->setBackground('#FFC000');
                })
                ->cells($needObject->columnName[9] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[9] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->cells($needObject->columnName[15] . ($needObject->$rowBegin + 2) . ':' . $needObject->columnName[15] . $needObject->$rowEnd, function($cells) {
                    $cells->setFontColor('#FF0000');
                })
                ->setColumnFormat(array(
                    $needObject->columnName[1] . $needObject->$rowEnd . ':' . $needObject->columnName[$needObject->$colEnd - 2] . $needObject->$rowEnd => '0.00%',
        ));
    }

    public function measureBorderCsatStaffExcel($dataExcel, $template) {
        $needObject = new \stdClass;
        $needObject->numTable = 8;
        if ($dataExcel['viewStatus'] == 0) {
            $needObject->numRowEachTable = count(explode(',', $dataExcel['region']));
        } else {
            $needObject->numRowEachTable = count($dataExcel['branch']);
        }


        //Phan dau
        $needObject->rowBeginSubject = 1;
        $needObject->rowEndSubject = $needObject->rowBeginSubject + 2;

        //Table 1
        $needObject->rowBeginTitle1 = $needObject->rowEndSubject + 2;
        $needObject->rowEndTitle1 = $needObject->rowBeginTitle1 + 1;
        $needObject->rowBeginTable1 = $needObject->rowEndTitle1 + 2;
        $needObject->rowEndTable1 = 2 + $needObject->rowBeginTable1 + $needObject->numRowEachTable + 1;

        //Table 2
        $needObject->rowBeginTitle2 = $needObject->rowEndTable1 + 2;
        $needObject->rowEndTitle2 = $needObject->rowBeginTitle2;
        $needObject->rowBeginTable2 = $needObject->rowEndTitle2 + 2;
        $needObject->rowEndTable2 = 2 + $needObject->rowBeginTable2 + $needObject->numRowEachTable + 1;
        
        //Table 3
        $needObject->rowBeginTitle3 = $needObject->rowEndTable2 + 2;
        $needObject->rowEndTitle3 = $needObject->rowBeginTitle3;
        $needObject->rowBeginTable3 = $needObject->rowEndTitle3 + 2;
        $needObject->rowEndTable3 = 2 + $needObject->rowBeginTable3 + $needObject->numRowEachTable + 1;

        //Table 4
        $needObject->rowBeginTitle4 = $needObject->rowEndTable3 + 2;
        $needObject->rowEndTitle4 = $needObject->rowBeginTitle4 + 1;
        $needObject->rowBeginTable4 = $needObject->rowEndTitle4 + 2;
        $needObject->rowEndTable4 = 2 + $needObject->rowBeginTable4 + $needObject->numRowEachTable + 2;


        //Table 5
        $needObject->rowBeginTitle5 = $needObject->rowEndTable4 + 2;
        $needObject->rowEndTitle5 = $needObject->rowBeginTitle5;
        $needObject->rowBeginTable5 = $needObject->rowEndTitle5 + 2;
        $needObject->rowEndTable5 = 2 + $needObject->rowBeginTable5 + $needObject->numRowEachTable + 2;

        //Table 6
        $needObject->rowBeginTable6 = $needObject->rowEndTable5 + 2;
        $needObject->rowEndTable6 = 2 + $needObject->rowBeginTable6 + $needObject->numRowEachTable + 2;

        //Table 7
        $needObject->rowBeginTable7 = $needObject->rowEndTable6 + 2;
        $needObject->rowEndTable7 = 2 + $needObject->rowBeginTable7 + $needObject->numRowEachTable + 2;
        
        //Table 8
        $needObject->rowBeginTitle8 = $needObject->rowEndTable7 + 2;
        $needObject->rowEndTitle8 = $needObject->rowBeginTitle8;
        $needObject->rowBeginTable8 = $needObject->rowEndTitle8 + 2;
        $needObject->rowEndTable8 = 2 + $needObject->rowBeginTable8 + $needObject->numRowEachTable + 2;

        $needObject->columnWidth = [
            /* 1 */ 'A' => 15,
            /* 2 */ 'B' => 10,
            /* 3 */ 'C' => 10,
            /* 4 */ 'D' => 10,
            /* 5 */ 'E' => 10,
            /* 6 */ 'F' => 10,
            /* 7 */ 'G' => 10,
            /* 8 */ 'H' => 10,
            /* 9 */ 'I' => 10,
            /* 10 */ 'J' => 10,
            /* 11 */ 'K' => 10,
            /* 12 */ 'L' => 10,
            /* 13 */ 'M' => 10,
            /* 14 */ 'N' => 10,
            /* 15 */ 'O' => 10,
            /* 16 */ 'P' => 10,
            /* 17 */ 'Q' => 10,
            /* 18 */ 'R' => 10,
            /* 19 */ 'S' => 10,
            /* 20 */ 'T' => 10,
            /* 21 */ 'U' => 10,
            /* 22 */ 'V' => 10,
            /* 23 */ 'W' => 10,
            /* 24 */ 'X' => 10,
            /* 25 */ 'Y' => 10,
            /* 26 */ 'Z' => 10,
            /* 27 */ 'AA' => 10,
            /* 28 */ 'AB' => 10,
            /* 29 */ 'AC' => 10,
            /* 30 */ 'AD' => 10,
            /* 31 */ 'AE' => 10,
        ];
        $needObject->columnName = array_keys($needObject->columnWidth);
        $needObject->colEndTable1 = 11;
        $needObject->colEndTable2 = 31;
        $needObject->colEndTable3 = 11;
        $needObject->colEndTable4 = 17;
        $needObject->colEndTable5 = 19;
        $needObject->colEndTable6 = 19;
        $needObject->colEndTable7 = 19;
        $needObject->colEndTable8 = 17;

        $needObject->colMaxColTable = 31;
        $needObject->template = $template;
        $needObject->dataExcel = $dataExcel;
        return $needObject;
    }

    //--------------------------------private function--------------------------------
    private function formatExcelGeneral(&$sheet, $needObject) {
        $sheet->loadView($needObject->template)
                ->with($needObject->dataExcel);

        //Phần đầu
        for ($i = $needObject->rowBeginSubject; $i <= $needObject->rowEndSubject; $i++) {
            $sheet->mergeCells($needObject->columnName[0] . $i . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $i);
            $sheet->cells($needObject->columnName[0] . $i . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $i, function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
            });
        }

        $sheet->setFreeze($needObject->columnName[1] . ($needObject->rowEndSubject + 1));

        for ($i = 1; $i <= $needObject->numTable; $i++) {
            $rowBeginTable = 'rowBeginTable' . $i;
            $rowEndTable = 'rowEndTable' . $i;
            $colEndTable = 'colEndTable' . $i;

            //Đường viền
            $sheet->setBorder($needObject->columnName[0] . $needObject->$rowBeginTable . ':' . $needObject->columnName[$needObject->$colEndTable - 1] . $needObject->$rowEndTable, 'thin');
        }
    }

    private function formatTitleGeneral(&$sheet, $needObject, $begin, $end, $colEnd) {
        for ($j = $needObject->$begin; $j <= $needObject->$end; $j++) {
            $sheet->mergeCells($needObject->columnName[0] . $j . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $j);
            $sheet->cells($needObject->columnName[0] . $j . ':' . $needObject->columnName[$needObject->$colEnd - 1] . $j, function($cells) {
                $cells->setFontColor('#FF0000');
            });
        }
    }

}
