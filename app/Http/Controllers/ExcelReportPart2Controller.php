<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests;
use App\Models\SurveySections;
use App\Models\SurveyReport;
use App\Models\Location;
use App\Models\SummaryCsat;
use App\Models\SummaryNps;
use App\Component\ExtraFunction;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelDashboardController;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use File;

class ExcelReportPart2Controller extends Controller {

    protected $modelSurveySections;
    protected $extraFunc;
    protected $userGranted;

    public function __construct() {
        $this->modelSurveySections = new SurveySections();
        $this->extraFunc = new ExtraFunction();
//        $this->userGranted = $this->extraFunc->getUserGranted();
//        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
//        $temp = [];
//        foreach ($this->selNPSImprovement as $k => $val) {
//            $temp[$val->answer_id] = $val;
//        }
//        $this->selNPSImprovement = $temp;
    }

    //Tạo bảng thống kê chi tiết CSAT theo đối tượng, đã rút gọn
    public function createDetailObjectCsat($sheet, $detailObjectCSAT, $rowIndex) {
//        dump($detailObjectCSAT['totalCSAT'],$detailObjectCSAT['averagePoint'] );die;
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function($cell) {
                    $cell->setValue('2. '.trans('report.StatisticalOfSatisfactionCustomerForRatingObject'));
                    $this->setTitleTable($cell);
                })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 2))->cell('A' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.EvaluatedObject'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('A' . ($rowIndex + 3) . ':B' . ($rowIndex + 3))->cell('A' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Rating Point'));
                    $this->setTitleHeaderTable($cell);
                })->cell('B' . ($rowIndex + 1), function($cell) {

                    $cell->setBorder('thin', 'none', 'none', 'none');
                })->cell('B' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('B' . ($rowIndex + 3), function($cell) {

                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->mergeCells('C' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Rating Quality Service'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
        $this->extraFunc->setColumnTitleHeaderTable('C', 10, $sheet, $rowIndex + 1);
        $sheet->mergeCells('I' . ($rowIndex + 1) . ':Q' . ($rowIndex + 1))->cell('I' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Rating Staff'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })
                ->mergeCells('C' . ($rowIndex + 2) . ':E' . ($rowIndex + 2))->cell('C' . ($rowIndex + 2), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'thin');
                    $cell->setBackground('#8DB4E2');
                })->cell('C' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.InternetService'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowIndex + 3, 'thin-thin-thin-thin');
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowIndex + 2, 'thin-thin-thin-thin');
        $sheet->mergeCells('F' . ($rowIndex + 2) . ':H' . ($rowIndex + 2))->cell('F' . ($rowIndex + 3), function($cell) {
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'none', 'none', 'none');
                })->cell('F' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Service Quality Statistical'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'none', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->mergeCells('I' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('I' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Saler'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('L' . ($rowIndex + 2) . ':N' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Technical Staff'));
                    $this->setTitleHeaderTable($cell);
                })
               ->mergeCells('O' . ($rowIndex + 2) . ':Q' . ($rowIndex + 2))->cell('O' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Staff Statistical'));
                    $this->setTitleHeaderTable($cell);
//            var_dump('1');
                })
                ->cell('C' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('D' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })->cell('E' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })
                ->cell('F' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('G' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })->cell('H' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })
                ->cell('I' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })->cell('K' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })
                ->cell('L' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('M' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })->cell('N' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })
                ->cell('O' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('P' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                })->cell('Q' . ($rowIndex + 3), function($cell) {
                    $cell->setValue(trans('report.Percent'));
                    $this->setTitleHeaderTable($cell);
                }) ;

        $rowStart = $rowIndex + 4;
        foreach ($detailObjectCSAT['totalCSAT'] as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
                $cell->setValue(trans('report.'.$value['Csat']));
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })->cell('C' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['Net']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NetPercent']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell('E' . $rowStart, function($cell) use($value) {
                    $cell->setValue($value['NetPercent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine = $value['NetPercent'] + $detailObjectCSAT['totalCSAT'][2]['NetPercent'] . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
                else
                    $rowCombine = $value['NetPercent'] + $detailObjectCSAT['totalCSAT'][5]['NetPercent'] . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
                $sheet->cell('E' . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                $this->setTitleBodyTable($cell);
                });
            }
            $sheet->cell('F' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NetAndTV']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NetAndTVPercent']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell('H' . $rowStart, function($cell) use($value) {
                    $cell->setValue($value['NetAndTVPercent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine = $value['NetAndTVPercent'] + $detailObjectCSAT['totalCSAT'][2]['NetAndTVPercent'] . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
                else
                    $rowCombine = $value['NetAndTVPercent'] + $detailObjectCSAT['totalCSAT'][5]['NetAndTVPercent'] . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
                $sheet->cell('H' . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                });
            }

            $sheet->cell('I' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NVKinhDoanh']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('J' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NVKinhDoanhPercent']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell('K' . $rowStart, function($cell) use($value) {
                    $cell->setValue($value['NVKinhDoanhPercent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine = $value['NVKinhDoanhPercent'] + $detailObjectCSAT['totalCSAT'][2]['NVKinhDoanhPercent'] . '%';
//                                        echo $res['NVKinhDoanhPercent'] + $totalCSAT[2]['NVKinhDoanhPercent'] . '%';
                else
                    $rowCombine = $value['NVKinhDoanhPercent'] + $detailObjectCSAT['totalCSAT'][5]['NVKinhDoanhPercent'] . '%';
//                                        echo $res['NVKinhDoanhPercent'] + $totalCSAT[5]['NVKinhDoanhPercent'] . '%';
                $sheet->cell('K' . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                });
            }

            $sheet->cell('L' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NVKT']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('M' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['NVKTPercent']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell('N' . $rowStart, function($cell) use($value) {
                    $cell->setValue($value['NVKTPercent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine = $value['NVKTPercent'] + $detailObjectCSAT['totalCSAT'][2]['NVKTPercent'] . '%';
//                                        echo $res['NVKTPercent'] + $totalCSAT[2]['NVKTPercent'] . '%';
                else
                    $rowCombine = $value['NVKTPercent'] + $detailObjectCSAT['totalCSAT'][5]['NVKTPercent'] . '%';
//                                        echo $res['NVKTPercent'] + $totalCSAT[5]['NVKTPercent'] . '%';
                $sheet->cell('N' . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                });
            }



            $sheet->cell('O' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['TongHopNV']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('P' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['TongHopNVPercent']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell('Q' . $rowStart, function($cell) use($value) {
                    $cell->setValue($value['TongHopNVPercent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine = $value['TongHopNVPercent'] + $detailObjectCSAT['totalCSAT'][2]['TongHopNVPercent'] . '%';
//                                        echo $res['TongHopNVPercent'] + $totalCSAT[2]['TongHopNVPercent'] . '%';
                else
                    $rowCombine = $value['TongHopNVPercent'] + $detailObjectCSAT['totalCSAT'][5]['TongHopNVPercent'] . '%';
//                                        echo $res['TongHopNVPercent'] + $totalCSAT[5]['TongHopNVPercent'] . '%';
                $sheet->cell('Q' . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                });
            }
            $rowStart++;
        }
        //Tạo row điểm trung bình
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function($cell) use($value) {
                    $cell->setValue(trans('report.Average Point'));
                    $this->setTitleMainRow($cell);
                });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowStart, 'thin-thin-thin-thin');
        $sheet->mergeCells('C' . ($rowStart) . ':E' . ($rowStart))->cell('C' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
                    $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NET']);
                    $this->setTitleMainRow($cell);
                })->mergeCells('F' . ($rowStart) . ':H' . ($rowStart))->cell('F' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
                    $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NetAndTV']);
                    $this->setTitleMainRow($cell);
                })->mergeCells('I' . ($rowStart) . ':K' . ($rowStart))->cell('I' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
                    $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NVKinhDoanh']);
                    $this->setTitleMainRow($cell);
                })
                ->mergeCells('L' . ($rowStart) . ':N' . ($rowStart))->cell('L' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
                    $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NVKT']);
                    $this->setTitleMainRow($cell);
                })
                ->mergeCells('O' . ($rowStart) . ':Q' . ($rowStart))->cell('O' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
                    $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_TongHopNV']);
                    $this->setTitleMainRow($cell);
                })
        ;

        return $rowStart;
    }

    //Tạo bảng thống kê chi tiết CSAT theo chi nhánh, đã rút gọn
    public function createDetailBranchCsat($sheet, $surveyCSATBranch, $rowIndex) {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function($cell) {
            $cell->setValue('3. '.trans('report.BranchCustomerSatisfactionofEachEvaluatedObject'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 1))->cell('A' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Location'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1), function($cell) {
            $cell->setBorder('thin', 'thin', 'thin', 'none');
        });
              $columnStart='C';
                foreach ($surveyCSATBranch['all'] as $value) {
                    $c2=$this->setColumn($columnStart, 8, $sheet, $rowIndex + 1);
//                    $c3=$this->setColumn($columnStart, 2);
                    $sheet->mergeCells($columnStart . ($rowIndex + 1) . ':'.($c2) . ($rowIndex + 1))->cell($columnStart . ($rowIndex + 1), function($cell) use($value) {
                        $cell->setValue(($value == 'WholeCountry') ?  trans('report.'.$value) : $value);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $cell->setBackground('#8DB4E2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'none');
                    });
                    $columnStart=$this->setColumn($columnStart, 9, $sheet, $rowIndex + 1);
                }


        $sheet->mergeCells('A' . ($rowIndex + 2) . ':B' . ($rowIndex + 2))->cell('A' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.EvaluatedObject'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 2), function($cell) {
//
            $cell->setBorder('thin', 'thin', 'thin', 'none');
        });;
               $columnStart='C';
        foreach ($surveyCSATBranch['all'] as $value) {
            $c1=$this->setColumn($columnStart, 3, $sheet, $rowIndex);
            $c2=$this->setColumn($columnStart, 6, $sheet, $rowIndex);

            $columnStartEnd = $this->setColumn($columnStart, 2, $sheet, $rowIndex);
            $c1End = $this->setColumn($c1, 2, $sheet, $rowIndex);
            $c2End = $this->setColumn($c2, 2, $sheet, $rowIndex);
            $sheet->mergeCells($columnStart . ($rowIndex + 2) . ':'.($columnStartEnd) . ($rowIndex + 2))->cell($columnStart . ($rowIndex + 2), function($cell) use($value) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('thin', 'thin', 'thin', 'none');
            })->mergeCells($c1 . ($rowIndex + 2) . ':'.($c1End) . ($rowIndex + 2))->cell($c1 . ($rowIndex + 2), function($cell) use($value) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('thin', 'thin', 'thin', 'none');
            })->mergeCells($c2 . ($rowIndex + 2) . ':'.($c2End) . ($rowIndex + 2))->cell($c2 . ($rowIndex + 2), function($cell) use($value) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('thin', 'thin', 'thin', 'none');
            })->cell($columnStart . ($rowIndex + 2), function($cell)  {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->cell(($c1) . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Saler'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c2) . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Deployer'));
                $this->setTitleHeaderTable($cell);
            });
            $columnStart=$this->setColumn($columnStart, 9, $sheet, $rowIndex);
        }

        $sheet->mergeCells('A' . ($rowIndex + 3) . ':B' . ($rowIndex + 3))->cell('A' . ($rowIndex + 3), function($cell) {
            $cell->setValue(trans('report.Rating Point'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 3), function($cell) {
//
            $cell->setBorder('thin', 'thin', 'thin', 'none');
        });;
        $columnStart='C';
        foreach ($surveyCSATBranch['all'] as $value) {
            $c1 = $this->setColumn($columnStart, 1, $sheet, $rowIndex);
            $c2 = $this->setColumn($columnStart, 2, $sheet, $rowIndex);
            $c3 = $this->setColumn($columnStart, 3, $sheet, $rowIndex);
            $c4 = $this->setColumn($columnStart, 4, $sheet, $rowIndex);
            $c5 = $this->setColumn($columnStart, 5, $sheet, $rowIndex);
            $c6 = $this->setColumn($columnStart, 6, $sheet, $rowIndex);
            $c7 = $this->setColumn($columnStart, 7, $sheet, $rowIndex);
            $c8 = $this->setColumn($columnStart, 8, $sheet, $rowIndex);
            $sheet->cell($columnStart . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c1) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c2) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c3) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c4) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c5) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c6) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c7) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c8) . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            });
            $columnStart = $this->setColumn($columnStart, 9, $sheet, $rowIndex);
        }
        $rowStart = $rowIndex + 4;

        foreach ($surveyCSATBranch['totalCSAT'] as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
                $cell->setValue(trans('report.'.$value['Csat']));
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            });
            $columnStart='C';
            foreach ($surveyCSATBranch['all'] as $value2) {
                $c1=$this->setColumn($columnStart, 1, $sheet, $rowStart);
                $c2=$this->setColumn($columnStart, 2, $sheet, $rowStart);
                $sheet->cell($columnStart . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'Net']) ? $value[$value2.'Net'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell($c1 . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'Net'.'Percent']) ? $value[$value2.'Net'.'Percent'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });

                if ($key == 3 || $key == 'total') {
                    $sheet->cell($c2 . $rowStart, function($cell) use($value, $value2) {
                        $cell->setValue($value[$value2.'Net'.'Percent']);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    });
                } else if ($key == 1 || $key == 4) {
                    if ($key == 1)
                        $rowCombine =  (!empty($value[$value2.'Net'.'Percent']) ? $value[$value2.'Net'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'Net'.'Percent']) ? $surveyCSATBranch['totalCSAT'][2][$value2.'Net'.'Percent'] : 0 ). '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
                    else
                        $rowCombine = (!empty($value[$value2.'Net'.'Percent']) ? $value[$value2.'Net'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'Net'.'Percent']) ? $surveyCSATBranch['totalCSAT'][5][$value2.'Net'.'Percent'] : 0 ). '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
                    $sheet->mergeCells($c2 . $rowStart. ':'.$c2 . ($rowStart + 1))->cell($c2 . $rowStart, function($cell) use($rowCombine) {
                        $cell->setValue($rowCombine);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'none', 'thin');
//                $this->setTitleBodyTable($cell);
                    });
                }
                
                $columnStartSaleMan = $this->setColumn($columnStart, 3, $sheet, $rowStart);
                $c3=$this->setColumn($columnStart, 4, $sheet, $rowStart);
                $c4=$this->setColumn($columnStart, 5, $sheet, $rowStart);
                $sheet->cell($columnStartSaleMan . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'SaleMan']) ? $value[$value2.'SaleMan'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell($c3 . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'SaleMan'.'Percent']) ? $value[$value2.'SaleMan'.'Percent'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });

                if ($key == 3 || $key == 'total') {
                    $sheet->cell($c4 . $rowStart, function($cell) use($value, $value2) {
                        $cell->setValue($value[$value2.'SaleMan'.'Percent']);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    });
                } else if ($key == 1 || $key == 4) {
                    if ($key == 1)
                        $rowCombine =  (!empty($value[$value2.'SaleMan'.'Percent']) ? $value[$value2.'SaleMan'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'SaleMan'.'Percent']) ? $surveyCSATBranch['totalCSAT'][2][$value2.'SaleMan'.'Percent'] : 0) . '%';
                    else
                        $rowCombine = (!empty($value[$value2.'SaleMan'.'Percent']) ? $value[$value2.'SaleMan'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'SaleMan'.'Percent']) ? $surveyCSATBranch['totalCSAT'][5][$value2.'SaleMan'.'Percent'] : 0) . '%';
                    $sheet->mergeCells($c4 . $rowStart. ':'.$c4 . ($rowStart + 1))->cell($c4 . $rowStart, function($cell) use($rowCombine) {
                        $cell->setValue($rowCombine);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'none', 'thin');
                    });
                }

                $columnStartSir = $this->setColumn($columnStart, 6, $sheet, $rowStart);
                $c5=$this->setColumn($columnStart, 7, $sheet, $rowStart);
                $c6=$this->setColumn($columnStart, 8, $sheet, $rowStart);
                $sheet->cell($columnStartSir . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'Sir']) ? $value[$value2.'Sir'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell($c5 . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue(!empty($value[$value2.'Sir'.'Percent']) ? $value[$value2.'Sir'.'Percent'] : 0);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });

                if ($key == 3 || $key == 'total') {
                    $sheet->cell($c6 . $rowStart, function($cell) use($value, $value2) {
                        $cell->setValue($value[$value2.'Sir'.'Percent']);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    });
                } else if ($key == 1 || $key == 4) {
                    if ($key == 1)
                        $rowCombine =  (!empty($value[$value2.'Sir'.'Percent']) ? $value[$value2.'Sir'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'Sir'.'Percent']) ? $surveyCSATBranch['totalCSAT'][2][$value2.'Sir'.'Percent'] : 0) . '%';
                    else
                        $rowCombine = (!empty($value[$value2.'Sir'.'Percent']) ? $value[$value2.'Sir'.'Percent'] : 0) + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'Sir'.'Percent']) ? $surveyCSATBranch['totalCSAT'][5][$value2.'Sir'.'Percent'] : 0) . '%';
                    $sheet->mergeCells($c6 . $rowStart. ':'.$c6 . ($rowStart + 1))->cell($c6 . $rowStart, function($cell) use($rowCombine) {
                        $cell->setValue($rowCombine);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'none', 'thin');
                    });
                }

                $columnStart = $this->setColumn($columnStart, 9, $sheet, $rowStart);
            }
            $rowStart++;
        }

        //Tạo row điểm trung bình
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function($cell) use($value) {
            $cell->setValue(trans('report.Average Point'));
            $this->setTitleMainRow($cell);
        })->cell('B' . ($rowStart), function($cell) {

            $cell->setBorder('none', 'none', 'thin', 'none');
        });
        $columnStart = $columnStartAveragePoint = 'C';
        foreach ($surveyCSATBranch['all'] as $value2) {
            $c2=$this->setColumn($columnStart, 2, $sheet, $rowStart);
            $sheet->cell($columnStartAveragePoint. ($rowStart), function($cell) {
                $cell->setBorder('none', 'none', 'thin', 'none');
            })->mergeCells($columnStart. ($rowStart) . ':'.$c2 . ($rowStart))->cell($columnStart . ($rowStart), function($cell) use($value, $surveyCSATBranch, $value2) {
                $cell->setBorder('thin', 'none', 'thin', 'none');
                $cell->setValue($surveyCSATBranch['averagePoint']['AVG_'.$value2.'Net']);
                $this->setTitleMainRow($cell);
            });

            $columnStartSaleMan = $this->setColumn($columnStart, 3, $sheet, $rowStart);
            $c3=$this->setColumn($columnStart, 5, $sheet, $rowStart);
            $sheet->cell($columnStartAveragePoint. ($rowStart), function($cell) {
                $cell->setBorder('none', 'none', 'thin', 'none');
            })->mergeCells($columnStartSaleMan. ($rowStart) . ':'.$c3 . ($rowStart))->cell($columnStartSaleMan . ($rowStart), function($cell) use($value, $surveyCSATBranch, $value2) {
                $cell->setBorder('thin', 'none', 'thin', 'none');
                $cell->setValue($surveyCSATBranch['averagePoint']['AVG_'.$value2.'SaleMan']);
                $this->setTitleMainRow($cell);
            });

            $columnStartSir = $this->setColumn($columnStart, 6, $sheet, $rowStart);
            $c4=$this->setColumn($columnStart, 8, $sheet, $rowStart);
            $sheet->cell($columnStartAveragePoint. ($rowStart), function($cell) {
                $cell->setBorder('none', 'none', 'thin', 'none');
            })->mergeCells($columnStartSir. ($rowStart) . ':'.$c4 . ($rowStart))->cell($columnStartSir . ($rowStart), function($cell) use($value, $surveyCSATBranch, $value2) {
                $cell->setBorder('thin', 'none', 'thin', 'none');
                $cell->setValue($surveyCSATBranch['averagePoint']['AVG_'.$value2.'Sir']);
                $this->setTitleMainRow($cell);
            });
            $columnStart=$this->setColumn($columnStart, 9, $sheet, $rowStart);
            $columnStartAveragePoint=$this->setColumn($columnStartAveragePoint, 1, $sheet, $rowStart);

        };
        return $rowStart;
    }
    
    //Tạo bảng thống kê chi tiết CSAT theo đối tượng
    public function createDetailHMICsat($sheet, $detailHMICSAT, $rowIndex) {
//        dump($detailObjectCSAT['totalCSAT'],$detailObjectCSAT['averagePoint'] );die;
//        print_r($detailHMICSAT['totalCSATHMI']);print_r($detailHMICSAT['averagePointHMI']);die;
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function($cell) {
                    $cell->setValue('3. Tổng hợp Sự hài lòng của Khách hàng toàn quốc đối với trường hợp khảo sát tại quầy qua kênh ghi nhận HMI');
                    $this->setTitleTable($cell);
                })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 1))->cell('A' . ($rowIndex + 1), function($cell) {
                    $cell->setValue('Điểm tiếp xúc Quầy Giao dịch Kênh Màn hình cảm ứng');
                    $this->setTitleHeaderTable($cell);
                })
                 ->cell('B' . ($rowIndex + 1), function($cell) {

                    $cell->setBorder('thin', 'thin', 'thin', 'none');
                })
              ;
                $columnStart='C';
//                $columnStart=$columnStart + 1;
//                 print_r($columnStart++);
//                print_r($detailHMICSAT['allQGD']);die;
                foreach ($detailHMICSAT['allQGD'] as $value) {
                    $c2=$this->setColumn($columnStart, 2, $sheet, $rowIndex + 2);
//                    $c3=$this->setColumn($columnStart, 2);
                    $sheet->mergeCells($columnStart . ($rowIndex + 1) . ':'.($c2) . ($rowIndex + 1))->cell($columnStart . ($rowIndex + 2), function($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#8DB4E2');
                })->cell($columnStart . ($rowIndex + 1), function($cell) use($value) {
                    $cell->setValue($value);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'none');
                });
                $columnStart=$this->setColumn($columnStart, 3, $sheet, $rowIndex + 2);
                }
                
                       $sheet ->mergeCells('A' . ($rowIndex + 2) . ':B' . ($rowIndex + 2))
                ->cell('A' . ($rowIndex + 2), function($cell) {
                    $cell->setValue('Điểm đánh giá');
                    $this->setTitleHeaderTable($cell);
                })
                ->cell('B' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('thin', 'thin', 'thin', 'none');
                })
               ;
                //Format header
                $totalNum = count($detailHMICSAT['allQGD']) * 3;
                $d = 'C';
                for($i = 1; $i <=$totalNum; $i++ )
                {
                        $sheet->cell($d . ('31'), function($cell) {
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    $d= ++$d;
                }
                    $columnStart='C';
                foreach ($detailHMICSAT['allQGD'] as $value) {
                    $c1=$this->setColumn($columnStart, 1, $sheet, $rowIndex);
                      $c2=$this->setColumn($columnStart, 2, $sheet, $rowIndex);
                $sheet->cell($columnStart . ($rowIndex + 2), function($cell)  {
                    $cell->setValue('Số lượng');
                    $this->setTitleHeaderTable($cell);
                })->cell(($c1) . ($rowIndex + 2), function($cell) {
                    $cell->setValue('Tỷ lệ ( %)');
                    $this->setTitleHeaderTable($cell);
                })->cell(($c2) . ($rowIndex + 2), function($cell) {
                    $cell->setValue('Tỷ lệ ( %)');
                    $this->setTitleHeaderTable($cell);
                });
                $columnStart=$this->setColumn($columnStart, 3, $sheet, $rowIndex);
                }

        $rowStart = $rowIndex + 3;
//        dump($detailCSAT);die;
//        $detailHMICSAT['avg'] = (object) $detailHMICSAT['avg'];
//        $detailHMICSAT['total'] = (object) $detailHMICSAT['total'];
//         dump((object)$detailHMICSAT['totalCSAT'],(object)$detailHMICSAT['averagePoint'] );die;
        foreach ($detailHMICSAT['totalCSATHMI'] as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
                $cell->setValue($value['Csat']);
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            });
            $columnStart='C';
            foreach ($detailHMICSAT['allQGD'] as $value2) {
                $c1=$this->setColumn($columnStart, 1, $sheet, $rowStart);
                $c2=$this->setColumn($columnStart, 2, $sheet, $rowStart);
            $sheet->cell($columnStart . $rowStart, function($cell) use($value, $value2) {
                $cell->setValue(!empty($value[$value2]) ? $value[$value2] : 0);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell($c1 . $rowStart, function($cell) use($value, $value2) {
                $cell->setValue(!empty($value[$value2.'Percent']) ? $value[$value2.'Percent'] : 0);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });

            if ($key == 3 || $key == 'total') {
                $sheet->cell($c2 . $rowStart, function($cell) use($value, $value2) {
                    $cell->setValue($value[$value2.'Percent']);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            } else if ($key == 1 || $key == 4) {
                if ($key == 1)
                    $rowCombine =  !empty($value[$value2.'Percent']) ? $value[$value2.'Percent'] : 0 + !empty($detailHMICSAT['totalCSATHMI'][2][$value2.'Percent']) ? $detailHMICSAT['totalCSATHMI'][2][$value2.'Percent'] : 0 . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
                else
                    $rowCombine = !empty($value[$value2.'Percent']) ? $value[$value2.'Percent'] : 0 + !empty($detailHMICSAT['totalCSATHMI'][5][$value2.'Percent']) ? $detailHMICSAT['totalCSATHMI'][5][$value2.'Percent'] : 0 . '%';
//                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
                $sheet->cell($c2 . $rowStart, function($cell) use($rowCombine) {
                    $cell->setValue($rowCombine);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                $this->setTitleBodyTable($cell);
                });
            }
            $columnStart = $this->setColumn($columnStart, 3, $sheet, $rowStart);
            }        
                 $rowStart++;
        }
        //Tạo row điểm trung bình
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function($cell) use($value) {
                    $cell->setValue('Điểm trung bình');
                    $this->setTitleMainRow($cell);
                })->cell('B' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                });
                $columnStart = $columnStartAveragePoint = 'C';
                    foreach ($detailHMICSAT['allQGD'] as $value2) {
                    $c2=$this->setColumn($columnStart, 2, $sheet, $rowStart);
                    $sheet->cell($columnStartAveragePoint. ($rowStart), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                    })->mergeCells($columnStart. ($rowStart) . ':'.$c2 . ($rowStart))->cell($columnStart . ($rowStart), function($cell) use($value, $detailHMICSAT, $value2) {
                        $cell->setBorder('thin', 'none', 'thin', 'none');
                    $cell->setValue($detailHMICSAT['averagePointHMI']['ĐTB_'.$value2]);
                    $this->setTitleMainRow($cell);
                });
                    $columnStart=$this->setColumn($columnStart, 3, $sheet, $rowStart);
                    $columnStartAveragePoint=$this->setColumn($columnStartAveragePoint, 1, $sheet, $rowStart);

                    };
        return $rowStart;
    }


//}

    public function setTitleTable($cell) {
        $cell->setFontWeight('bold');
        $cell->setAlignment('left');
        $cell->setFontColor('#ff0000');
    }

    public function setTitleHeaderTable($cell) {
        $cell->setAlignment('center');
        $cell->setValignment('center');
        $cell->setBackground('#8DB4E2');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
        $cell->setFontWeight('bold');
    }

    public function setTitleBodyTable($cell) {
        $cell->setAlignment('center');
        $cell->setValignment('center');
        $cell->setBackground('#C5D9F1');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    public function setBorderCell($cell) {
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    public function setTitleMainRow($cell) {
        $cell->setFontWeight('bold');
        $cell->setAlignment('center');
        $cell->setFontColor('#ff0000');
        $cell->setBackground('#ffa500');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }
    
    public function setColumn($char,$step, $sheet, $rowIndex)
    {
        $d=$char;
         for($i=1;$i<=$step;$i++)
        {
            if($rowIndex != 30 && $rowIndex != 5)
            {
                $sheet->cell($d . ($rowIndex), function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            }

            $d= ++$char;
        }
        return $d;
    }

}
