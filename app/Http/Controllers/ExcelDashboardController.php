<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\SurveySections;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Report;
use App\Component\ExtraFunction;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SummaryCsat;
use App\Models\SummaryNps;
use App\Models\SummaryOpinion;
use App\Http\Controllers\DashboardController;
use DB;

class ExcelDashboardController extends Controller {

    var $extraFunc;

    
      public function __construct() {
        $this->extraFunc = new ExtraFunction();
    }

    //Tạo bảng thống kê chi tiết CSAT
    public  function createDetailCsat($sheet, $detailCSAT, $rowIndex) {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function($cell) {
                    $cell->setValue('2. '.trans('report.SatisfactionOfCustomerStatistical'));
                    $this->setTitleTable($cell);
                })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 4))->cell('A' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Rating Point'));
                    $this->setTitleHeaderTable($cell);
                })->cell('B' . ($rowIndex + 1), function($cell) {

                    $cell->setBorder('thin', 'none', 'none', 'none');
                })->cell('B' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('B' . ($rowIndex + 4), function($cell) {

                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->mergeCells('C' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Deployment'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })->cell('D' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })
                ->cell('E' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })
                ->cell('F' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('G' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('H' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('I' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('L' . ($rowIndex + 1), function($cell) {

                    $this->setTitleHeaderTable($cell);
                })->cell('K' . ($rowIndex + 1), function($cell) {

                $cell->setBorder('thin', 'none', 'none', 'none');
            })->mergeCells('I' . ($rowIndex + 1) . ':L' . ($rowIndex + 1))->cell('I' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Maintenance'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })
                ->mergeCells('C' . ($rowIndex + 2) . ':D' . ($rowIndex + 2))->mergeCells('C' . ($rowIndex + 3) . ':D' . ($rowIndex + 3))->cell('C' . ($rowIndex + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'thin');
                    $cell->setBackground('#8DB4E2');
                })->cell('C' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Saler'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })
                ->cell('I' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('D' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('D' . ($rowIndex + 3), function($cell) {

                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('H' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('J' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('J' . ($rowIndex + 3), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('L' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('L' . ($rowIndex + 3), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                })
                ->cell('H' . ($rowIndex + 3), function($cell) {

                    $cell->setBorder('none', 'thin', 'none', 'none');
                }) ->cell('L' . ($rowIndex + 2), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            }) ->mergeCells('E' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->mergeCells('E' . ($rowIndex + 3) . ':F' . ($rowIndex + 3))->cell('E' . ($rowIndex + 3), function($cell) {
                        $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'none', 'none', 'none');
                })->cell('E' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Deployer'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'none', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->mergeCells('G' . ($rowIndex + 2) . ':H' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Rating Quality Service'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('G' . ($rowIndex + 3) . ':H' . ($rowIndex + 3))->cell('G' . ($rowIndex + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })
                ->mergeCells('I' . ($rowIndex + 2) . ':J' . ($rowIndex + 2))->mergeCells('I' . ($rowIndex + 3) . ':J' . ($rowIndex + 3))->cell('I' . ($rowIndex + 3), function($cell) {
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'none', 'none', 'none');
                })->cell('I' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.MaintainanceStaff'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->mergeCells('K' . ($rowIndex + 2) . ':L' . ($rowIndex + 2))->cell('K' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Rating Quality Service'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('K' . ($rowIndex + 3) . ':L' . ($rowIndex + 3))->cell('K' . ($rowIndex + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })

                ->cell('C' . ($rowIndex + 4), function($cell) {
                    $cell->setValue(trans('report.Quantity'));
                    $this->setTitleHeaderTable($cell);
                })->cell('D' . ($rowIndex + 4), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('I' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('J' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('K' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('L' . ($rowIndex + 4), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        $rowStart = $rowIndex + 5;
//        dump($detailCSAT);die;
        $detailCSAT->avg = (object) $detailCSAT->avg;
        $detailCSAT->total = (object) $detailCSAT->total;
        foreach ($detailCSAT->survey as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
                        $cell->setValue(trans('report.'.$value->DanhGia));
                        $this->setTitleBodyTable($cell);
                    })->cell('B' . ($rowStart), function($cell) {

                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('C' . $rowStart, function($cell) use($value) {
                        $cell->setValue($value->NVKinhDoanh);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('D' . $rowStart, function($cell) use($value, $detailCSAT) {
                        if ($detailCSAT->avg->NVKinhDoanh != 0) {
                            $cell->setValue(number_format(round(($value->NVKinhDoanh / ($detailCSAT->total->NVKinhDoanh)) * 100, 2), 2) . " %");
                        } else {
                            $cell->setValue("0 %");
                        }
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })
                    ->cell('E' . $rowStart, function($cell) use($value) {
                        $cell->setValue($value->NVTrienKhai);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('F' . $rowStart, function($cell) use($value, $detailCSAT) {
                        if ($detailCSAT->avg->NVTrienKhai != 0) {
                            $cell->setValue(number_format(round(($value->NVTrienKhai / ($detailCSAT->total->NVTrienKhai)) * 100, 2), 2) . " %");
                        } else {
                            $cell->setValue("0 %");
                        }
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('G' . $rowStart, function($cell) use($value) {
                        $cell->setValue($value->DGDichVu_Net);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('H' . $rowStart, function($cell) use($value, $detailCSAT) {
                        if ($detailCSAT->avg->DGDichVu_Net != 0) {
                            $cell->setValue(number_format(round(($value->DGDichVu_Net / ($detailCSAT->total->DGDichVu_Net)) * 100, 2), 2) . " %");
                        } else {
                            $cell->setValue("0 %");
                        }

                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })
                    ->cell('I' . $rowStart, function($cell) use($value) {
                        $cell->setValue($value->NVBaoTri);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('J' . $rowStart, function($cell) use($value, $detailCSAT) {
                        if ($detailCSAT->avg->NVBaoTri != 0) {
                            $cell->setValue(number_format(round(($value->NVBaoTri / ($detailCSAT->total->NVBaoTri)) * 100, 2), 2) . " %");
                        } else {
                            $cell->setValue("0 %");
                        }

                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('K' . $rowStart, function($cell) use($value) {
                        $cell->setValue($value->DVBaoTri_Net);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    })->cell('L' . $rowStart, function($cell) use($value, $detailCSAT) {
                        if ($detailCSAT->avg->DVBaoTri_Net != 0) {
                            $cell->setValue(number_format(round(($value->DVBaoTri_Net / ($detailCSAT->total->DVBaoTri_Net)) * 100, 2), 2) . " %");
                        } else {
                            $cell->setValue("0 %");
                        }
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                        $this->setBorderCell($cell);
                    });
            $rowStart++;
        }
        //Tạo row tổng cộng,total
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
                    $cell->setValue(trans('report.Total'));
                    $this->setTitleBodyTable($cell);
                    $cell->setFontWeight('bold');
                })->cell('B' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('C' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->total->NVKinhDoanh);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('D' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue("100 %");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })
                ->cell('E' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->total->NVTrienKhai);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('F' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue("100 %");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('G' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->total->DGDichVu_Net);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('H' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue("100 %");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })
                ->cell('I' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->total->NVBaoTri);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('J' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue("100 %");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('K' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->total->DVBaoTri_Net);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('L' . $rowStart, function($cell) use($value, $detailCSAT) {
                    $cell->setValue("100 %");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                });
        //Tạo row điểm trung bình
        $rowStart++;
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function($cell) use($value) {
                    $cell->setValue(trans('report.Average Point'));
                    $this->setTitleMainRow($cell);
                })->cell('B' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('D' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('F' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('H' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('J' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('L' . ($rowStart), function($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->mergeCells('C' . ($rowStart) . ':D' . ($rowStart))->cell('C' . ($rowStart), function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->avg->NVKinhDoanh);
                    $this->setTitleMainRow($cell);
                })->mergeCells('E' . ($rowStart) . ':F' . ($rowStart))->cell('E' . ($rowStart), function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->avg->NVTrienKhai);
                    $this->setTitleMainRow($cell);
                })->mergeCells('G' . ($rowStart) . ':H' . ($rowStart))->cell('G' . ($rowStart), function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->avg->DGDichVu_Net);
                    $this->setTitleMainRow($cell);
                })->mergeCells('I' . ($rowStart) . ':J' . ($rowStart))->cell('I' . ($rowStart), function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->avg->NVBaoTri);
                    $this->setTitleMainRow($cell);
                })->mergeCells('K' . ($rowStart) . ':L' . ($rowStart))->cell('K' . ($rowStart), function($cell) use($value, $detailCSAT) {
                    $cell->setValue($detailCSAT->avg->DVBaoTri_Net);
                    $this->setTitleMainRow($cell);
                });
        return $rowStart;
    }

    //Tạo bảng thống kê chi tiết NPS, đã rút gọn
    public function createDetailNps($sheet, $detailNPS, $rowIndex) {
        $sheet->cell('A' . $rowIndex, function($cell) use($detailNPS) {
                    $cell->setValue('3. '.trans('report.Rating Point NPS Statistic'));
                    $this->setTitleTable($cell);
                })->cell('A' . ($rowIndex + 1), function($cell) use($detailNPS) {
                    $cell->setValue('3.1. '.trans('report.Rating Point'));
                    $this->setTitleTable($cell);
                })->cell('B' . ($rowIndex + 2), function($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->mergeCells('A' . ($rowIndex + 2) . ':B' . ($rowIndex + 3))->cell('A' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Rating Point'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('C' . ($rowIndex + 2) . ':D' . ($rowIndex + 2))->cell('C' . ($rowIndex + 2), function($cell) {
                    $cell->setValue(trans('report.Deployment'));
                    $this->setTitleHeaderTable($cell);
                });
             $this->extraFunc->setColumnByFormat('C', 6, $sheet, $rowIndex + 2, 'thin-thin-thin-thin');
              $sheet->mergeCells('E' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->cell('E' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowIndex + 2) . ':H' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowIndex + 3), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('D' . ($rowIndex + 3), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowIndex + 3), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowIndex + 3), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowIndex + 3), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowIndex + 3), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        //Tạo row NPS
        $rowStart2 = $rowIndex + 4;
//        dump($detailNPS);die;
         $detailNPS = (object)  $detailNPS;
        $detailNPS->total = (object) $detailNPS->total;
        foreach ($detailNPS->survey as $key => $value) {
            $sheet->mergeCells('A' . $rowStart2 . ':B' . $rowStart2)->cell('A' . $rowStart2, function($cell) use($value) {
                $cell->setValue($value->answers_point);
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart2), function($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('C' . $rowStart2, function($cell) use($value) {
                $cell->setValue($value->SauTK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart2, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->SauTK != 0) {
                    $cell->setValue(number_format(round(($value->SauTK / ($detailNPS->total->SauTK)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                ;
            })->cell('E' . $rowStart2, function($cell) use($value) {
                $cell->setValue($value->SauBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart2, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->SauBT != 0) {
                    $cell->setValue(number_format(round(($value->SauBT / ($detailNPS->total->SauBT)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                ;
            })->cell('G' . $rowStart2, function($cell) use($value) {
                $cell->setValue($value->Total);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart2, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->Total != 0) {
                    $cell->setValue(number_format(round(($value->Total / ($detailNPS->total->Total)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });
            $rowStart2++;
        }
        //Tạo row tổng cộng
        $sheet->mergeCells('A' . $rowStart2 . ':B' . $rowStart2)->cell('A' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleBodyTable($cell);
            $cell->setFontWeight('bold');
        })->cell('B' . ($rowStart2), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue($detailNPS->total->SauTK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('D' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('E' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue($detailNPS->total->SauBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('F' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('G' . $rowStart2, function($cell) use($value, $detailNPS) {
            $cell->setValue($detailNPS->total->Total);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('H' . $rowStart2, function($cell) use($value) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        });
        return $rowStart2;
    }

    //Tạo bảng thống kê NPS theo nhóm, đã rút gọn
    public function createGroupNps($sheet, $detailNPS, $rowIndex) {
        $sheet->cell('A' . $rowIndex, function($cell) use($detailNPS) {
                    $cell->setValue('3.2. '.trans('report.Rating Point NPS'));
                    $this->setTitleTable($cell);
                })->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 2))->cell('A' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Rating Point'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('C' . ($rowIndex + 1) . ':D' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function($cell) {
                    $cell->setValue(trans('report.Deployment'));
                    $this->setTitleHeaderTable($cell);
                });
            $this->extraFunc->setColumnByFormat('B', 7, $sheet, $rowIndex + 1, 'thin-thin-thin-thin');
           $sheet->mergeCells('E' . ($rowIndex + 1) . ':F' . ($rowIndex + 1))->cell('E' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('G' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('D' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowIndex + 2), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        $rowStart3 = ($rowIndex + 3);
        $detailNPS = (object) $detailNPS;
        $detailNPS->total = (object) $detailNPS->total;
        foreach ($detailNPS->groupNPS as $key => $value) {
            $value = (object) $value;
            $sheet->mergeCells('A' . $rowStart3 . ':B' . $rowStart3)->cell('A' . $rowStart3, function($cell) use($value) {
                $cell->setValue($value->type);
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart3), function($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('C' . $rowStart3, function($cell) use($value) {
                $cell->setValue($value->SauTK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart3, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->SauTK != 0) {
                    $cell->setValue(number_format(round(($value->SauTK / ($detailNPS->total->SauTK)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart3, function($cell) use($value) {
                $cell->setValue($value->SauBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart3, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->SauBT != 0) {
                    $cell->setValue(number_format(round(($value->SauBT / ($detailNPS->total->SauBT)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart3, function($cell) use($value) {
                $cell->setValue($value->Total);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart3, function($cell) use($value, $detailNPS) {
                if ($detailNPS->total->Total != 0) {
                    $cell->setValue(number_format(round(($value->Total / ($detailNPS->total->Total)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });
            $rowStart3++;
        }
        //Tạo row tổng cộng
        $sheet->mergeCells('A' . $rowStart3 . ':B' . $rowStart3)->cell('A' . $rowStart3, function($cell) use($value, $detailNPS) {
                    $cell->setValue(trans('report.Total'));
                    $this->setTitleBodyTable($cell);
                    $cell->setFontWeight('bold');
                })->cell('B' . ($rowStart3), function($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('C' . $rowStart3, function($cell) use($value, $detailNPS) {
                    $cell->setValue($detailNPS->total->SauTK);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->cell('D' . $rowStart3, function($cell) use($value, $detailNPS) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('E' . $rowStart3, function($cell) use($value, $detailNPS) {
            $cell->setValue($detailNPS->total->SauBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('F' . $rowStart3, function($cell) use($value, $detailNPS) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('G' . $rowStart3, function($cell) use($value, $detailNPS) {
            $cell->setValue($detailNPS->total->Total);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('H' . $rowStart3, function($cell) use($value) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        });
        $rowStart3++;
        //Tạo row NPS
        $detailNPS->groupNPS[2] = (object) $detailNPS->groupNPS[2];
        $detailNPS->groupNPS[0] = (object) $detailNPS->groupNPS[0];
        $sheet->mergeCells('A' . $rowStart3 . ':B' . $rowStart3)->cell('A' . $rowStart3, function($cell) use($value, $detailNPS) {
            $cell->setValue('NPS');
            $this->setTitleMainRow($cell);
        });
        $this->extraFunc->setColumnByFormat('C', 6, $sheet, $rowStart3, 'thin-thin-thin-thin');
        $sheet->mergeCells('C' . $rowStart3 . ':D' . $rowStart3)->cell('C' . $rowStart3, function($cell) use($value, $detailNPS) {
            if ($detailNPS->total->SauTK != 0) {
                $cell->setValue(number_format(round((($detailNPS->groupNPS[2]->SauTK - $detailNPS->groupNPS[0]->SauTK) / $detailNPS->total->SauTK) * 100, 2), 2));
            } else
                $cell->setValue('0 %');
            $this->setTitleMainRow($cell);
        })->mergeCells('E' . $rowStart3 . ':F' . $rowStart3)->cell('E' . $rowStart3, function($cell) use($value, $detailNPS) {
            if ($detailNPS->total->SauBT != 0) {
                $cell->setValue(number_format(round((($detailNPS->groupNPS[2]->SauBT - $detailNPS->groupNPS[0]->SauBT) / $detailNPS->total->SauBT) * 100, 2), 2));
            } else
                $cell->setValue('0 %');
            $this->setTitleMainRow($cell);
        })->mergeCells('G' . $rowStart3 . ':H' . $rowStart3)->cell('G' . $rowStart3, function($cell) use($value, $detailNPS) {
            if ($detailNPS->total->Total != 0) {
                $cell->setValue(number_format(round((($detailNPS->groupNPS[2]->Total - $detailNPS->groupNPS[0]->Total) / $detailNPS->total->Total) * 100, 2), 2));
            } else
                $cell->setValue('0 %');
            $this->setTitleMainRow($cell);
        });
        return $rowStart3;
    }

    //Tạo bảng thống kê comment khách hàng, đã rút gọn
    public function createEvaluateCus($sheet, $customerComment, $rowIndex) {
        $sheet->cell('A' . $rowIndex, function($cell) use($customerComment) {
            $cell->setValue('3.4. '.trans('report.Customer Comments'));
            $this->setTitleTable($cell);
        })->mergeCells('A' . ($rowIndex + 1) . ':A' . ($rowIndex + 2))->setWidth('A', 25)->setWidth('B', 45)->cell('A' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Customer Comments'));
            $this->setTitleHeaderTable($cell);
            $cell->setAlignment('left');
            $cell->setValignment('center');
        })->mergeCells('B' . ($rowIndex + 1) . ':B' . ($rowIndex + 2))->cell('B' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Content'));
            $this->setTitleHeaderTable($cell);
            $cell->setAlignment('left');
            $cell->setValignment('center');
        })->mergeCells('C' . ($rowIndex + 1) . ':D' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        });
            $this->extraFunc->setColumnByFormat('C', 6, $sheet, $rowIndex + 1, 'thin-thin-thin-thin');
          $sheet->mergeCells('E' . ($rowIndex + 1) . ':F' . ($rowIndex + 1))->cell('E' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('G' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('I' . ($rowIndex + 1) . ':J' . ($rowIndex + 1))->cell('I' . ($rowIndex + 1), function($cell) {
            $cell->setValue(wordwrap(trans('report.Total By Group'), 19));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('D' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('I' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('J' . ($rowIndex + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        $temp = $t = '';
        $customerComment= (object) $customerComment;
        $customerComment->total = (array) $customerComment->total;
        foreach ($customerComment->survey as $a) {// tạo mảng chứa tên nhóm answer
            if ($a->answers_group_title != $t) {
                $ansGroup[$a->answers_group_title] = 1;
                $totalByGroup[$a->answers_group_title] = (int) $a->Total;
            } else {
                $ansGroup[$t] ++;
                $totalByGroup[$a->answers_group_title] += $a->Total;
            }
            //% các nhóm answer
//            dump($customerComment->total,$totalByGroup);die;
            $totalByGroupPercent[$a->answers_group_title] = ($customerComment->total['Total'] > 0) ? round(($totalByGroup[$a->answers_group_title] / $customerComment->total['Total']) * 100, 2) : 0;
            $t = $a->answers_group_title;
        }
        //Tạo row  ý kiến
        $rowStart4 = $rowIndex + 3;
        $customerComment->survey = (array) $customerComment->survey;
        $customerComment->totalCusComment = (array) $customerComment->totalCusComment;
        $customerComment->totalConsulted = (array) $customerComment->totalConsulted;
        $customerComment->totalCusNoComment = (array) $customerComment->totalCusNoComment;
//        dump($customerComment);die;
        if (is_array($customerComment->survey)) {
            foreach ($customerComment->survey as $key => $value) {
                if ($temp != $value->answers_group_title) {
                    $sheet->mergeCells('A' . $rowStart4 . ':A' . ($rowStart4 + $ansGroup[$value->answers_group_title] - 1))
                            ->mergeCells('I' . $rowStart4 . ':I' . ($rowStart4 + $ansGroup[$value->answers_group_title] - 1))
                            ->mergeCells('J' . $rowStart4 . ':J' . ($rowStart4 + $ansGroup[$value->answers_group_title] - 1));
                }
                $sheet->cell('A' . $rowStart4, function($cell) use($value, $customerComment) {
                    $cell->setValue(trans('report.'.$value->answers_group_title));
                    $cell->setFontWeight('bold');
                    $this->setTitleBodyTable($cell);
                    $cell->setAlignment('left');
                    $cell->setValignment('center');
                })->cell('B' . $rowStart4, function($cell) use($value, $customerComment) {
                    $cell->setValue(trans('report.'.$value->Content));
                    $this->setTitleBodyTable($cell);
                    $cell->setAlignment('left');
                    $cell->setValignment('center');
                })->cell('C' . $rowStart4, function($cell) use($value) {
                    $cell->setValue($value->SauTK);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('D' . $rowStart4, function($cell) use($value, $customerComment) {
                    if ($customerComment->total['SauTK'] != 0) {
                        $cell->setValue(number_format(round(($value->SauTK / ($customerComment->total['SauTK'])) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('E' . $rowStart4, function($cell) use($value) {
                    $cell->setValue($value->SauBT);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('F' . $rowStart4, function($cell) use($value, $customerComment) {
                    if ($customerComment->total['SauBT'] != 0) {
                        $cell->setValue(number_format(round(($value->SauBT / ($customerComment->total['SauBT'])) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('G' . $rowStart4, function($cell) use($value) {
                    $cell->setValue($value->Total);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('H' . $rowStart4, function($cell) use($value, $customerComment) {
                    if ($customerComment->total['Total'] != 0) {
                        $cell->setValue(number_format(round(($value->Total / ($customerComment->total['Total'])) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('I' . $rowStart4, function($cell) use($value, $totalByGroup) {
                    $cell->setValue($totalByGroup[$value->answers_group_title]);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('J' . $rowStart4, function($cell) use($value, $totalByGroupPercent) {
                    if ($totalByGroupPercent[$value->answers_group_title] != 0) {
                        $cell->setValue(number_format($totalByGroupPercent[$value->answers_group_title], 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
                $rowStart4++;
                $temp = $value->answers_group_title;
            }
            //Tạo row tổng cộng
            $sheet->mergeCells('A' . $rowStart4 . ':B' . $rowStart4)->cell('A' . $rowStart4, function($cell) {
                $cell->setValue(trans('report.Total comment'));
                $this->setTitleBodyTable($cell);
                $cell->setFontWeight('bold');
                $cell->setAlignment('left');
            })->cell('B' . ($rowStart4), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'thin');
            })->cell('C' . $rowStart4, function($cell) use($customerComment) {
                $cell->setValue($customerComment->total['SauTK']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('D' . $rowStart4, function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('E' . $rowStart4, function($cell) use($customerComment) {
                $cell->setValue($customerComment->total['SauBT']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('F' . $rowStart4, function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('G' . $rowStart4, function($cell) use($customerComment) {
                $cell->setValue($customerComment->total['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('H' . $rowStart4, function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('I' . $rowStart4, function($cell) use($customerComment) {
                $cell->setValue($customerComment->total['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('J' . $rowStart4, function($cell) {
                $cell->setValue('100 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
            //Tạo row tổng cộng KH góp ý
            $sheet->mergeCells('A' . ($rowStart4 + 1) . ':B' . ($rowStart4 + 1))->cell('A' . ($rowStart4 + 1), function($cell) {
                $cell->setValue(trans('report.Total customer comment'));
                $this->setTitleBodyTable($cell);
                $cell->setFontWeight('bold');
                $cell->setAlignment('left');
            })->cell('B' . ($rowStart4 + 1), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'thin');
            })->cell('C' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusComment['SauTK']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('D' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cusCommentPercent = ($customerComment->totalConsulted['SauTK'] > 0) ? ($customerComment->totalCusComment['SauTK'] / $customerComment->totalConsulted['SauTK']) * 100 : 0;
                $cusCommentPercent = round($cusCommentPercent, 2);
                $cell->setValue($cusCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('E' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusComment['SauBT']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('F' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cusCommentPercent = ($customerComment->totalConsulted['SauBT'] > 0) ? ($customerComment->totalCusComment['SauBT'] / $customerComment->totalConsulted['SauBT']) * 100 : 0;
                $cusCommentPercent = round($cusCommentPercent, 2);
                $cell->setValue($cusCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('G' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusComment['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('H' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cusCommentPercent = ($customerComment->totalConsulted['Total'] > 0) ? ($customerComment->totalCusComment['Total'] / $customerComment->totalConsulted['Total']) * 100 : 0;
                $cusCommentPercent = round($cusCommentPercent, 2);
                $cell->setValue($cusCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('I' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusComment['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('J' . ($rowStart4 + 1), function($cell) use($customerComment) {
                $cusCommentPercent = ($customerComment->totalConsulted['Total'] > 0) ? ($customerComment->totalCusComment['Total'] / $customerComment->totalConsulted['Total']) * 100 : 0;
                $cusCommentPercent = round($cusCommentPercent, 2);
                $cell->setValue($cusCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
            //Tạo row tổng cộng KH ko góp ý
            $sheet->mergeCells('A' . ($rowStart4 + 2) . ':B' . ($rowStart4 + 2))->cell('A' . ($rowStart4 + 2), function($cell) {
                $cell->setValue(trans('report.Total customer no comment'));
                $this->setTitleBodyTable($cell);
                $cell->setFontWeight('bold');
                $cell->setAlignment('left');
            })->cell('B' . ($rowStart4 + 2), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'thin');
            })->cell('C' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusNoComment['SauTK']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('D' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cusNoCommentPercent = ($customerComment->totalCusNoComment['SauTK'] > 0) ? ($customerComment->totalCusNoComment['SauTK'] / $customerComment->totalConsulted['SauTK']) * 100 : 0;
                $cusNoCommentPercent = round($cusNoCommentPercent, 2);
                $cell->setValue($cusNoCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('E' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusNoComment['SauBT']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('F' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cusNoCommentPercent = ($customerComment->totalCusNoComment['SauBT'] > 0) ? ($customerComment->totalCusNoComment['SauBT'] / $customerComment->totalConsulted['SauBT']) * 100 : 0;
                $cusNoCommentPercent = round($cusNoCommentPercent, 2);
                $cell->setValue($cusNoCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('G' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusNoComment['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('H' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cusNoCommentPercent = ($customerComment->totalConsulted['Total'] > 0) ? ($customerComment->totalCusNoComment['Total'] / $customerComment->totalConsulted['Total']) * 100 : 0;
                $cusNoCommentPercent = round($cusNoCommentPercent, 2);
                $cell->setValue($cusNoCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('I' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalCusNoComment['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('J' . ($rowStart4 + 2), function($cell) use($customerComment) {
                $cusNoCommentPercent = ($customerComment->totalConsulted['Total'] > 0) ? ($customerComment->totalCusNoComment['Total'] / $customerComment->totalConsulted['Total']) * 100 : 0;
                $cusNoCommentPercent = round($cusNoCommentPercent, 2);
                $cell->setValue($cusNoCommentPercent . ' %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
            //Tạo row tổng cộng KH được hỏi ý kiến
            $sheet->mergeCells('A' . ($rowStart4 + 3) . ':B' . ($rowStart4 + 3))->cell('A' . ($rowStart4 + 3), function($cell) {
                $cell->setValue(trans('report.Total consulted'));
                $this->setTitleBodyTable($cell);
                $cell->setFontWeight('bold');
                $cell->setAlignment('left');
            })->cell('B' . ($rowStart4 + 3), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'thin');
            })->cell('C' . ($rowStart4 + 3), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalConsulted['SauTK']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('D' . ($rowStart4 + 3), function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('E' . ($rowStart4 + 3), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalConsulted['SauBT']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('F' . ($rowStart4 + 3), function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('G' . ($rowStart4 + 3), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalConsulted['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('H' . ($rowStart4 + 3), function($cell) {
                $cell->setValue('100.00 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('I' . ($rowStart4 + 3), function($cell) use($customerComment) {
                $cell->setValue($customerComment->totalConsulted['Total']);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('J' . ($rowStart4 + 3), function($cell) {
                $cell->setValue('100 %');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
        } else {
            $sheet->mergeCells('A' . ($rowStart4) . ':I' . ($rowStart4))->cell('A' . ($rowStart4), function($cell) {
                $cell->setValue(trans('report.NotFound'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
        }
        return $rowStart4;
    }

    //Tạo bảng CSAT theo vùng
    public function createCsatRegion($sheet, $survey, $rowStart4, $surveyRegion, $arrayCountry) {
        //Tạo khung CSAT
        $sheet->cell('A' . $rowStart4, function($cell) {
                    $cell->setValue('1. Báo cáo CSAT, NPS theo vùng');
                    $this->setTitleTable($cell);
                })->mergeCells('A' . ($rowStart4 + 1) . ':A' . ($rowStart4 + 3))->cell('A' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('STT');
                    $this->setTitleHeaderTable($cell);
                })->cell('A' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('A' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('B' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('B' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->setWidth('B', 20)->mergeCells('B' . ($rowStart4 + 1) . ':B' . ($rowStart4 + 3))->cell('B' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Vùng');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('C' . ($rowStart4 + 1) . ':J' . ($rowStart4 + 1))->cell('C' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau triển khai DirectSales');
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('D' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('E' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('F' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('G' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('H' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('I' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowStart4 + 2), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('K' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('K' . ($rowStart4 + 1), function($cell) {
                       $cell->setBorder('thin', 'none', 'thin', 'thin');
                })->cell('K' . ($rowStart4 + 2), function($cell) {
                       $cell->setBorder('thin', 'none', 'none', 'thin');
                })->cell('L' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('M' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('N' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('O' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('P' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('P' . ($rowStart4 + 2), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('R' . ($rowStart4 + 2), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('R' . ($rowStart4 + 3), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('Q' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('R' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('S' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('T' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('U' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('V' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('V' . ($rowStart4 + 2), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('W' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('X' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('X' . ($rowStart4 + 3), function($cell) {
                     $cell->setBorder('thin', 'thin', 'thin', 'none');
                })->cell('Y' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('Z' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AA' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AB' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AB' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AD' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AD' . ($rowStart4 + 3), function($cell) {
                     $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AC' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AD' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AE' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AF' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AG' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AH' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AH' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AJ' . ($rowStart4 + 1), function($cell) {
                     $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AJ' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AJ' . ($rowStart4 + 3), function($cell) {
                     $cell->setBorder('thin', 'thin', 'thin', 'none');
                })->cell('AO' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('thin', 'none', 'none', 'thin');
                })->cell('AT' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AV' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('BB' . ($rowStart4 + 2), function($cell) {
                     $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('BB' . ($rowStart4 + 3), function($cell) {
                     $cell->setBorder('thin', 'thin', 'thin', 'none');
                })->cell('AP' . ($rowStart4 + 3), function($cell) {
                     $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AI' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AK' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AL' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AM' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AN' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AN' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('thin', 'thin', 'none', 'none');
                })->cell('AO' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AP' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AQ' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AR' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AS' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AT' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AU' . ($rowStart4 + 1), function($cell) {
                     $this->setTitleHeaderTable($cell);
                })->cell('AV' . ($rowStart4 + 1), function($cell) {
                     $this->setTitleHeaderTable($cell);
                })->cell('AX' . ($rowStart4 + 1), function($cell) {
                      $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AW' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AY' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AZ' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('BA' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('BB' . ($rowStart4 + 1), function($cell) {
                    $this->setTitleHeaderTable($cell);
                })->cell('AS' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('AT' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('AV' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AX' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AZ' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->mergeCells('K' . ($rowStart4 + 1) . ':R' . ($rowStart4 + 1))->cell('K' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau triển khai Telesales');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('S' . ($rowStart4 + 1) . ':X' . ($rowStart4 + 1))->cell('S' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau bảo trì TIN-PNC');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('Y' . ($rowStart4 + 1) . ':AD' . ($rowStart4 + 1))->cell('Y' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau bảo trì INDO');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AE' . ($rowStart4 + 1) . ':AJ' . ($rowStart4 + 1))->cell('AE' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau thu cước tại nhà');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AK' . ($rowStart4 + 1) . ':AN' . ($rowStart4 + 1))->cell('AK' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau GDTQ');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AO' . ($rowStart4 + 1) . ':AV' . ($rowStart4 + 1))->cell('AO' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau triển khi sale tại quầy');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AW' . ($rowStart4 + 1) . ':BB' . ($rowStart4 + 1))->cell('AW' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('Sau Triển khai Swap');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('BC' . ($rowStart4 + 1) . ':BC' . ($rowStart4 + 3))->cell('BC' . ($rowStart4 + 1), function($cell) {
                    $cell->setValue('NPS');
                    $this->setTitleHeaderTable($cell);
                })
                        ->mergeCells('C' . ($rowStart4 + 2) . ':D' . ($rowStart4 + 3))->cell('C' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV kinh doanh');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->cell('D' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('F' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('H' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('J' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('M' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('O' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Q' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('V' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Z' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AC' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AG' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AL' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AN' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AQ' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->mergeCells('E' . ($rowStart4 + 2) . ':F' . ($rowStart4 + 3))->cell('E' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV triển khai');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('G' . ($rowStart4 + 2) . ':J' . ($rowStart4 + 2))->cell('G' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->cell('H' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('J' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->mergeCells('G' . ($rowStart4 + 3) . ':H' . ($rowStart4 + 3))->cell('G' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('I' . ($rowStart4 + 3) . ':J' . ($rowStart4 + 3))->cell('I' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })
               ->mergeCells('K' . ($rowStart4 + 2) . ':L' . ($rowStart4 + 3))->cell('K' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV kinh doanh');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
//                    $cell->setBorder('none', 'thin', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->cell('L' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->mergeCells('M' . ($rowStart4 + 2) . ':N' . ($rowStart4 + 3))->cell('M' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV triển khai');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('O' . ($rowStart4 + 2) . ':R' . ($rowStart4 + 2))->cell('O' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->cell('Q' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })
                ->cell('T' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->mergeCells('O' . ($rowStart4 + 3) . ':P' . ($rowStart4 + 3))->cell('O' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('Q' . ($rowStart4 + 3) . ':R' . ($rowStart4 + 3))->cell('Q' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('S' . ($rowStart4 + 2) . ':T' . ($rowStart4 + 3))->cell('S' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV bảo trì TIN-PNC');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('U' . ($rowStart4 + 2) . ':X' . ($rowStart4 + 2))->cell('U' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->cell('X' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Z' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Z' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AA' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('AE' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AE' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AG' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AG' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AH' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('AL' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AN' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AO' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AQ' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AR' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('P' . ($rowStart4  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('T' . ($rowStart4  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AB' . ($rowStart4  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('N' . ($rowStart4  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->cell('AF' . ($rowStart4  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->mergeCells('U' . ($rowStart4 + 3) . ':V' . ($rowStart4 + 3))->cell('U' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('W' . ($rowStart4 + 3) . ':X' . ($rowStart4 + 3))->cell('W' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('Y' . ($rowStart4 + 2) . ':Z' . ($rowStart4 + 3))->cell('Y' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV bảo trì INDO');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AA' . ($rowStart4 + 2) . ':AD' . ($rowStart4 + 2))->cell('AA' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AA' . ($rowStart4 + 3) . ':AB' . ($rowStart4 + 3))->cell('AA' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AC' . ($rowStart4 + 3) . ':AD' . ($rowStart4 + 3))->cell('AC' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })
                ->mergeCells('AE' . ($rowStart4 + 2) . ':AF' . ($rowStart4 + 3))->cell('AE' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV thu cước');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AG' . ($rowStart4 + 2) . ':AJ' . ($rowStart4 + 2))->cell('AG' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AG' . ($rowStart4 + 3) . ':AH' . ($rowStart4 + 3))->cell('AG' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AI' . ($rowStart4 + 3) . ':AJ' . ($rowStart4 + 3))->cell('AI' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AK' . ($rowStart4 + 2) . ':AL' . ($rowStart4 + 3))->cell('AK' . ($rowStart4 + 2), function($cell) {
            $cell->setValue('Nhân viên giao dịch');
            $this->setTitleHeaderTable($cell);
        })
                ->mergeCells('AM' . ($rowStart4 + 2) . ':AN' . ($rowStart4 + 3))->cell('AM' . ($rowStart4 + 2), function($cell) {
            $cell->setValue('Chất lượng dịch vụ');
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AO' . ($rowStart4 + 2) . ':AP' . ($rowStart4 + 3))->cell('AO' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV kinh doanh');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->cell('M' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })->mergeCells('AQ' . ($rowStart4 + 2) . ':AR' . ($rowStart4 + 3))->cell('AQ' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('NV triển khai');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('AS' . ($rowStart4 + 2) . ':AV' . ($rowStart4 + 2))->cell('AS' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->cell('Q' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })
               ->mergeCells('AS' . ($rowStart4 + 3) . ':AT' . ($rowStart4 + 3))->cell('AS' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('AU' . ($rowStart4 + 3) . ':AV' . ($rowStart4 + 3))->cell('AU' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })
                ->mergeCells('AW' . ($rowStart4 + 2) . ':AX' . ($rowStart4 + 3))->cell('AW' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Nhân viên triển khai Swap');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('AY' . ($rowStart4 + 2) . ':BB' . ($rowStart4 + 2))->cell('AY' . ($rowStart4 + 2), function($cell) {
                    $cell->setValue('Chất lượng dịch vụ');
                    $this->setTitleHeaderTable($cell);
                })->cell('Q' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 2), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart4 + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'thin', 'none');
                })
               ->mergeCells('AY' . ($rowStart4 + 3) . ':AZ' . ($rowStart4 + 3))->cell('AY' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('BA' . ($rowStart4 + 3) . ':BB' . ($rowStart4 + 3))->cell('BA' . ($rowStart4 + 3), function($cell) {
                    $cell->setValue('Truyền hình');
                    $this->setTitleHeaderTable($cell);
                })
               

        ;
        //Tạo row vùng
        $rowStart6 = $rowStart4 + 4;
        $i = 0;
        foreach ($survey as $key => $value) {
            $i++;
            $sheet->cell('A' . $rowStart6, function($cell) use($value, $i) {
                        $cell->setValue($i);
                        $this->setTitleBodyTable($cell);
                    })->cell('B' . $rowStart6, function($cell) use($value) {
                        $cell->setValue($value->Vung);
                        $this->setTitleBodyTable($cell);
                    })->cell('D' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('E' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })
                    ->cell('F' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('G' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('H' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('I' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('J' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('K' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('L' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('M' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('N' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('O' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('P' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('Q' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('R' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('S' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('T' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('U' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('V' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('W' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('X' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('Y' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('Z' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AA' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AB' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AC' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AD' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AE' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AF' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AG' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AH' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AI' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AJ' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AK' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AL' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AS' . ($rowStart6), function($cell) {
                        $cell->setBorder('thin', 'none', 'thin', 'none');
                    })->cell('AM' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AN' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AO' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AP' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AR' . ($rowStart6), function($cell) {
                        $cell->setBorder('thin', 'none', 'thin', 'none');
                    })->cell('AT' . ($rowStart6), function($cell) {
                        $cell->setBorder('thin', 'none', 'thin', 'none');
                    })->cell('AQ' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AV' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AW' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AX' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AY' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AZ' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BA' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BB' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BC' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->mergeCells('C' . $rowStart6 . ':D' . $rowStart6)->cell('C' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongKD != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVKinhDoanhPoint ) / ((int) $value->SoLuongKD), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('E' . $rowStart6 . ':F' . $rowStart6)->cell('E' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongTK != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVTrienKhaiPoint ) / ((int) $value->SoLuongTK), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('G' . $rowStart6 . ':H' . $rowStart6)->cell('G' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDV_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVu_Net_Point ) / ((int) $value->SoLuongDGDV_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('I' . $rowStart6 . ':J' . $rowStart6)->cell('I' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDV_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVu_TV_Point ) / ((int) $value->SoLuongDGDV_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('K' . $rowStart6 . ':L' . $rowStart6)->cell('K' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongKDTS != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVKinhDoanhTSPoint ) / ((int) $value->SoLuongKDTS), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('M' . $rowStart6 . ':N' . $rowStart6)->cell('M' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongTKTS != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVTrienKhaiTSPoint ) / ((int) $value->SoLuongTKTS), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('O' . $rowStart6 . ':P' . $rowStart6)->cell('O' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVTS_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuTS_Net_Point ) / ((int) $value->SoLuongDGDVTS_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('Q' . $rowStart6 . ':R' . $rowStart6)->cell('Q' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVTS_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuTS_TV_Point ) / ((int) $value->SoLuongDGDVTS_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('S' . $rowStart6 . ':T' . $rowStart6)->cell('S' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongNVBaoTriTIN != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVBaoTriTINPoint ) / ((int) $value->SoLuongNVBaoTriTIN), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('U' . $rowStart6 . ':V' . $rowStart6)->cell('U' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDVBaoTriTIN_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DVBaoTriTIN_Net_Point ) / ((int) $value->SoLuongDVBaoTriTIN_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('W' . $rowStart6 . ':X' . $rowStart6)->cell('W' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDVBaoTriTIN_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DVBaoTriTIN_TV_Point ) / ((int) $value->SoLuongDVBaoTriTIN_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('Y' . $rowStart6 . ':Z' . $rowStart6)->cell('Y' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongNVBaoTriINDO != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVBaoTriINDOPoint ) / ((int) $value->SoLuongNVBaoTriINDO), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AA' . $rowStart6 . ':AB' . $rowStart6)->cell('AA' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDVBaoTriINDO_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DVBaoTriINDO_Net_Point ) / ((int) $value->SoLuongDVBaoTriINDO_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AC' . $rowStart6 . ':AD' . $rowStart6)->cell('AC' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDVBaoTriINDO_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DVBaoTriINDO_TV_Point ) / ((int) $value->SoLuongDVBaoTriINDO_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })
                    ->mergeCells('AE' . $rowStart6 . ':AF' . $rowStart6)->cell('AE' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongNVTC != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVTC_Point ) / ((int) $value->SoLuongNVTC), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AG' . $rowStart6 . ':AH' . $rowStart6)->cell('AG' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDV_MobiPay_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVu_MobiPay_Net_Point ) / ((int) $value->SoLuongDGDV_MobiPay_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AI' . $rowStart6 . ':AJ' . $rowStart6)->cell('AI' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDV_MobiPay_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVu_MobiPay_TV_Point ) / ((int) $value->SoLuongDGDV_MobiPay_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AK' . $rowStart6 . ':AL' . $rowStart6)->cell('AK' . $rowStart6, function($cell) use($value) {
                if ((int) $value->SoLuongNVGDTQ != 0) {
                    $cell->setValue(number_format(round(((int) $value->NVGDTQ_Point ) / ((int) $value->SoLuongNVGDTQ), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
            })
                    ->mergeCells('AM' . $rowStart6 . ':AN' . $rowStart6)->cell('AM' . $rowStart6, function($cell) use($value) {
                if ((int) $value->SoLuongGDTQ != 0) {
                    $cell->setValue(number_format(round(((int) $value->DGDichVuGDTQ_Point ) / ((int) $value->SoLuongGDTQ), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
            })->mergeCells('AO' . $rowStart6 . ':AP' . $rowStart6)->cell('AO' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongKDSS != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVKinhDoanhSSPoint ) / ((int) $value->SoLuongKDSS), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AQ' . $rowStart6 . ':AR' . $rowStart6)->cell('AQ' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongTKSS != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVTrienKhaiSSPoint ) / ((int) $value->SoLuongTKSS), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AS' . $rowStart6 . ':AT' . $rowStart6)->cell('AS' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVSS_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuSS_Net_Point ) / ((int) $value->SoLuongDGDVSS_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AU' . $rowStart6 . ':AV' . $rowStart6)->cell('AU' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVSS_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuSS_TV_Point ) / ((int) $value->SoLuongDGDVSS_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AW' . $rowStart6 . ':AX' . $rowStart6)->cell('AW' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongSSW != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVBTSSWPoint ) / ((int) $value->SoLuongSSW), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('AY' . $rowStart6 . ':AZ' . $rowStart6)->cell('AY' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVSSW_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuSSW_Net_Point ) / ((int) $value->SoLuongDGDVSSW_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('BA' . $rowStart6 . ':BB' . $rowStart6)->cell('BA' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDVSSW_TV != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVuSSW_TV_Point ) / ((int) $value->SoLuongDGDVSSW_TV), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
            $rowStart6++;
        }
        //Tạo row tổng cộng        
        $sheet->cell('A' . $rowStart6, function($cell) use ($i) {
                    $this->setTitleBodyTable($cell);
                    $cell->setValue($i + 1);
                    $cell->setFontWeight('bold');
                })->cell('B' . $rowStart6, function($cell) {
                    $this->setTitleBodyTable($cell);
                    $cell->setValue('Toàn quốc');
                    $cell->setFontWeight('bold');
                })->cell('D' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('E' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })
                ->cell('F' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('G' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('H' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('I' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('J' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('K' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('L' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('M' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('N' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('O' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('P' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Q' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('R' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('S' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('T' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('U' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('V' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('W' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('X' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Y' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('Z' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AA' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AB' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AC' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AD' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AE' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AF' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AG' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AH' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AI' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AJ' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AK' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AL' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AM' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AN' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AO' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AP' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AQ' . ($rowStart6), function($cell) {
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AS' . ($rowStart6), function($cell) {
                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AV' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AR' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AT' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AW' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AX' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AY' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('AZ' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BA' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BB' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->cell('BC' . ($rowStart6), function($cell) {
                        $cell->setBorder('none', 'none', 'thin', 'none');
                    })->mergeCells('C' . $rowStart6 . ':D' . $rowStart6)->cell('C' . $rowStart6, function($cell) use( $arrayCountry) {

                    $cell->setValue($arrayCountry['NVKinhDoanh_AVGPoint']);

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('E' . $rowStart6 . ':F' . $rowStart6)->cell('E' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['NVTrienKhai_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('G' . $rowStart6 . ':H' . $rowStart6)->cell('G' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVu_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('I' . $rowStart6 . ':J' . $rowStart6)->cell('I' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVu_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('K' . $rowStart6 . ':L' . $rowStart6)->cell('K' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['NVKinhDoanhTS_AVGPoint']);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('M' . $rowStart6 . ':N' . $rowStart6)->cell('M' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['NVTrienKhaiTS_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('O' . $rowStart6 . ':P' . $rowStart6)->cell('O' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVuTS_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('Q' . $rowStart6 . ':R' . $rowStart6)->cell('Q' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVuTS_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('S' . $rowStart6 . ':T' . $rowStart6)->cell('S' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['NVBaoTriTIN_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('U' . $rowStart6 . ':V' . $rowStart6)->cell('U' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['DVBaoTriTIN_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('W' . $rowStart6 . ':X' . $rowStart6)->cell('W' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DVBaoTriTIN_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('Y' . $rowStart6 . ':Z' . $rowStart6)->cell('Y' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['NVBaoTriINDO_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AA' . $rowStart6 . ':AB' . $rowStart6)->cell('AA' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DVBaoTriINDO_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AC' . $rowStart6 . ':AD' . $rowStart6)->cell('AC' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DVBaoTriINDO_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })
                ->mergeCells('AE' . $rowStart6 . ':AF' . $rowStart6)->cell('AE' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue(empty($arrayCountry['NVTC_AVGPoint']) ? 0:$arrayCountry['NVTC_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AG' . $rowStart6 . ':AH' . $rowStart6)->cell('AG' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVu_MobiPay_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AI' . $rowStart6 . ':AJ' . $rowStart6)->cell('AI' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVu_MobiPay_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })
                 ->mergeCells('AK' . $rowStart6 . ':AL' . $rowStart6)->cell('AK' . $rowStart6, function($cell) use($arrayCountry) {
                  $cell->setValue(empty($arrayCountry['NV_GDTQ_AVGPoint']) ? 0:$arrayCountry['NV_GDTQ_AVGPoint']);
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })
                ->mergeCells('AM' . $rowStart6 . ':AN' . $rowStart6)->cell('AM' . $rowStart6, function($cell) use($arrayCountry) {
            $cell->setValue($arrayCountry['DGDichVu_GDTQ_AVGPoint']);
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->mergeCells('AO' . $rowStart6 . ':AP' . $rowStart6)->cell('AO' . $rowStart6, function($cell) use( $arrayCountry) {
   $cell->setValue(empty($arrayCountry['NVKinhDoanhSS_AVGPoint']) ? 0:$arrayCountry['NVKinhDoanhSS_AVGPoint']);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('AQ' . $rowStart6 . ':AR' . $rowStart6)->cell('AQ' . $rowStart6, function($cell) use($arrayCountry) {
                     $cell->setValue(empty($arrayCountry['NVTrienKhaiSS_AVGPoint']) ? 0:$arrayCountry['NVTrienKhaiSS_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AS' . $rowStart6 . ':AT' . $rowStart6)->cell('AS' . $rowStart6, function($cell) use( $arrayCountry) {
                       $cell->setValue(empty($arrayCountry['DGDichVuSS_Net_AVGPoint']) ? 0:$arrayCountry['DGDichVuSS_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AU' . $rowStart6 . ':AV' . $rowStart6)->cell('AU' . $rowStart6, function($cell) use($arrayCountry) {
                       $cell->setValue(empty($arrayCountry['DGDichVuSS_TV_AVGPoint']) ? 0:$arrayCountry['DGDichVuSS_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AW' . $rowStart6 . ':AX' . $rowStart6)->cell('AW' . $rowStart6, function($cell) use($arrayCountry) {
                       $cell->setValue(empty($arrayCountry['NVBT_SSW_AVGPoint']) ? 0:$arrayCountry['NVBT_SSW_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('AY' . $rowStart6 . ':AZ' . $rowStart6)->cell('AY' . $rowStart6, function($cell) use( $arrayCountry) {
                       $cell->setValue(empty($arrayCountry['DGDichVuSSW_Net_AVGPoint']) ? 0:$arrayCountry['DGDichVuSSW_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('BA' . $rowStart6 . ':BB' . $rowStart6)->cell('BA' . $rowStart6, function($cell) use($arrayCountry) {
                       $cell->setValue(empty($arrayCountry['DGDichVuSSW_TV_AVGPoint']) ? 0:$arrayCountry['DGDichVuSSW_TV_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })

        ;
        //Khung NPS
                $i=7;
            foreach ($surveyRegion['npsRegion'] as $key => $value) {            
            $sheet->cell('M' . $i, function($cell) use($value) {
                        $cell->setValue($value.'%');
                           $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
                    $i++;
            }
              $sheet->cell('M' . $i, function($cell) use($surveyRegion) {
                        $cell->setValue($surveyRegion['npsCountryBranches']['Toàn quốc'].'%');
                           $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
        return  $rowStart6;
    }

    //Tạo bảng CSAT theo chi nhánh, đã rút gọn
     public function createCsatBranch($sheet, $surveyBranches, $arrayCountry, $rowStart6, $resultNpsAll, $npsCountry) {
        //Tạo khung CSAT
        $sheet->cell('A' . $rowStart6 , function($cell) {
                    $cell->setValue('1. '.trans('report.CsatNpsReportByBranches'));
                    $this->setTitleTable($cell);
                })->mergeCells('A' . ($rowStart6  + 1) . ':A' . ($rowStart6  + 3))->cell('A' . ($rowStart6  + 1), function($cell) {
                    $cell->setValue('STT');
                    $this->setTitleHeaderTable($cell);
                })->cell('A' . ($rowStart6  + 2), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('A' . ($rowStart6  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('B' . ($rowStart6  + 2), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->cell('B' . ($rowStart6  + 3), function($cell) {
                    $cell->setBorder('none', 'thin', 'none', 'none');
                })->setWidth('B', 20)->mergeCells('B' . ($rowStart6  + 1) . ':B' . ($rowStart6  + 3))->cell('B' . ($rowStart6  + 1), function($cell) {
                    $cell->setValue(trans('report.Location'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('C' . ($rowStart6  + 1) . ':H' . ($rowStart6  + 1))->cell('C' . ($rowStart6  + 1), function($cell) {
                    $cell->setValue(trans('report.Deployment'));
                    $this->setTitleHeaderTable($cell);
                });
            $this->extraFunc->setColumnTitleHeaderTable('C', 11, $sheet, $rowStart6 + 1);
        $sheet->mergeCells('I' . ($rowStart6  + 1) . ':L' . ($rowStart6  + 1))->cell('I' . ($rowStart6  + 1), function($cell) {
                    $cell->setValue(trans('report.Maintenance'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('M' . ($rowStart6  + 1) . ':M' . ($rowStart6  + 3))->cell('M' . ($rowStart6  + 1), function($cell) {
                    $cell->setValue('NPS');
                    $this->setTitleHeaderTable($cell);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                })->mergeCells('C' . ($rowStart6  + 2) . ':D' . ($rowStart6  + 3))->cell('C' . ($rowStart6  + 2), function($cell) {
                    $cell->setValue(trans('report.Saler'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('none', 'thin', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->mergeCells('E' . ($rowStart6  + 2) . ':F' . ($rowStart6  + 3))->cell('E' . ($rowStart6  + 2), function($cell) {
                    $cell->setValue(trans('report.Deployer'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
                    $cell->setBorder('thin', 'thin', 'none', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('G' . ($rowStart6  + 2) . ':H' . ($rowStart6  + 2))->cell('G' . ($rowStart6  + 2), function($cell) {
                    $cell->setValue(trans('report.Rating Quality Service'));
                    $this->setTitleHeaderTable($cell);
                })->cell('J' . ($rowStart6  + 2), function($cell) {
                      $this->setTitleHeaderTable($cell);
                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->mergeCells('G' . ($rowStart6  + 3) . ':H' . ($rowStart6  + 3))->cell('G' . ($rowStart6  + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                });
                 $this->extraFunc->setColumnByFormat('C', 11, $sheet, $rowStart6 + 3, 'thin-thin-thin-thin');
               $sheet->mergeCells('I' . ($rowStart6  + 2) . ':J' . ($rowStart6  + 3))->cell('I' . ($rowStart6  + 2), function($cell) {
                    $cell->setValue(trans('report.MaintainanceStaff'));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBackground('#8DB4E2');
//                    $cell->setBorder('none', 'thin', 'none', 'none');
                    $cell->setFontWeight('bold');
                })->mergeCells('K' . ($rowStart6  + 2) . ':L' . ($rowStart6  + 2))->cell('K' . ($rowStart6  + 2), function($cell) {
                    $cell->setValue(trans('report.Rating Quality Service'));
                    $this->setTitleHeaderTable($cell);
                })
    ->mergeCells('K' . ($rowStart6  + 3) . ':L' . ($rowStart6  + 3))->cell('K' . ($rowStart6  + 3), function($cell) {
                    $cell->setValue('Internet');
                    $this->setTitleHeaderTable($cell);
                })
        ;
        //Tạo row chi nhánh
        $rowStart6 = $rowStart6 + 4;
        $i = 0;
        foreach ($surveyBranches  as $key => $value) {
            $i++;
            $sheet->cell('A' . $rowStart6, function($cell) use($value,$i) {
                        $cell->setValue($i);
                        $this->setTitleBodyTable($cell);
                    })->cell('B' . $rowStart6, function($cell) use($value) {
                        $this->setTitleBodyTable($cell);
                        $cell->setValue($value->KhuVuc);
                    });
                $this->extraFunc->setColumnByFormat('C', 11, $sheet, $rowStart6, 'thin-thin-thin-thin');
                $sheet->mergeCells('C' . $rowStart6 . ':D' . $rowStart6)->cell('C' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongKD != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVKinhDoanhPoint ) / ((int) $value->SoLuongKD), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('E' . $rowStart6 . ':F' . $rowStart6)->cell('E' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongTK != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVTrienKhaiPoint ) / ((int) $value->SoLuongTK), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('G' . $rowStart6 . ':H' . $rowStart6)->cell('G' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDGDV_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DGDichVu_Net_Point ) / ((int) $value->SoLuongDGDV_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('I' . $rowStart6 . ':J' . $rowStart6)->cell('I' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongNVBaoTri != 0) {
                            $cell->setValue(number_format(round(((int) $value->NVBaoTriPoint ) / ((int) $value->SoLuongNVBaoTri), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    })->mergeCells('K' . $rowStart6 . ':L' . $rowStart6)->cell('K' . $rowStart6, function($cell) use($value) {
                        if ((int) $value->SoLuongDVBaoTri_Net != 0) {
                            $cell->setValue(number_format(round(((int) $value->DVBaoTri_Net_Point ) / ((int) $value->SoLuongDVBaoTri_Net), 2), 2));
                        } else {
                            $cell->setValue(0);
                        }
                        $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
            $rowStart6++;
        }
        //Tạo row tổng cộng        
        $sheet->cell('A' . $rowStart6, function($cell) use ($i) {
                    $this->setTitleBodyTable($cell);
                    $cell->setValue($i + 1);
                    $cell->setFontWeight('bold');
                })->cell('B' . $rowStart6, function($cell) {
                    $this->setTitleBodyTable($cell);
                    $cell->setValue(trans('report.WholeCountry'));
                    $cell->setFontWeight('bold');
                });
            $this->extraFunc->setColumnByFormat('C', 11, $sheet, $rowStart6, 'thin-thin-thin-thin');
            $sheet->mergeCells('C' . $rowStart6 . ':D' . $rowStart6)->cell('C' . $rowStart6, function($cell) use( $arrayCountry) {

                    $cell->setValue($arrayCountry['NVKinhDoanh_AVGPoint']);

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                })->mergeCells('E' . $rowStart6 . ':F' . $rowStart6)->cell('E' . $rowStart6, function($cell) use($arrayCountry) {
                    $cell->setValue($arrayCountry['NVTrienKhai_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('G' . $rowStart6 . ':H' . $rowStart6)->cell('G' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['DGDichVu_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('I' . $rowStart6 . ':J' . $rowStart6)->cell('I' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['NVBaoTri_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })->mergeCells('K' . $rowStart6 . ':L' . $rowStart6)->cell('K' . $rowStart6, function($cell) use( $arrayCountry) {
                    $cell->setValue($arrayCountry['DVBaoTri_Net_AVGPoint']);
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                    $cell->setFontWeight('bold');
                })
        ;
        //Khung NPS
                $i=7;
            foreach ($resultNpsAll as $key => $value) {
            $sheet->cell('M' . $i, function($cell) use($value) {
                        $cell->setValue($value.'%');
                           $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
                    $i++;
            }
              $sheet->cell('M' . $i, function($cell) use($npsCountry) {
                        $cell->setValue(number_format(round($npsCountry['Toàn Quốc'],2), 2)." %");
                           $cell->setAlignment('center');
                        $this->setBorderCell($cell);
                    });
        return $rowStart6;
    }

    //Tạo báo cáo số lượng CSAT, đã rút gọn
    public function createAmountCsat($sheet, $result, $rowStart9) {
        $sheet->cell('A' . $rowStart9, function($cell) {
                    $cell->setValue('1. '.trans('report.QuantitySurveyReportCustomerCareActive'));
                    $this->setTitleTable($cell);
                })->cell('A' . ($rowStart9 + 1), function($cell) {
                    $cell->setValue('1.1. '.trans('report.Survey Quality'));
                    $this->setTitleTable($cell);
                })->mergeCells('A' . ($rowStart9 + 2) . ':B' . ($rowStart9 + 2))->cell('A' . ($rowStart9 + 2), function($cell) {
            $cell->setValue(trans('report.SurveyCareChannel'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('C' . ($rowStart9 + 2) . ':H' . ($rowStart9 + 2))->cell('C' . ($rowStart9 + 2), function($cell) {
            $cell->setValue('Happy Call');
            $this->setTitleHeaderTable($cell);
        });
//them vao
        $this->extraFunc->setColumnByFormat('C', 6, $sheet, $rowStart9 + 2, 'thin-thin-thin-thin');
        $rowStart9 += 1;
        $sheet->mergeCells('A' . ($rowStart9 + 2) . ':B' . ($rowStart9 + 3))->cell('A' . ($rowStart9 + 2), function($cell) {
                    $cell->setValue(trans('report.SurveyQuantityCSAT'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('C' . ($rowStart9 + 2) . ':D' . ($rowStart9 + 2))->cell('C' . ($rowStart9 + 2), function($cell) {
                    $cell->setValue(trans('report.Deployment'));
                    $this->setTitleHeaderTable($cell);
                })->mergeCells('E' . ($rowStart9 + 2) . ':F' . ($rowStart9 + 2))->cell('E' . ($rowStart9 + 2), function($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowStart9 + 2) . ':H' . ($rowStart9 + 2))->cell('G' . ($rowStart9 + 2), function($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowStart9 + 3), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('D' . ($rowStart9 + 3), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowStart9 + 3), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowStart9 + 3), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowStart9 + 3), function($cell) {
                $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowStart9 + 3), function($cell) {
                $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        //Tạo row  ý kiến
        $arrayResult = [0 => 'NoNeedContact', 1 => 'CannotContact', 2 => 'MeetCustomerCustomerDeclinedToTakeSurvey', 3 => 'DidntMeetUser', 4 => 'MeetUser'];
        $rowStart9 +=4;
        $result= (object) $result;
        $result->total = (object) $result->total;
        foreach ($result->survey as $key => $value) {
            $sheet->mergeCells('A' . $rowStart9 . ':B' . $rowStart9)->cell('A' . $rowStart9, function($cell) use($value, $arrayResult, $result) {
                $cell->setValue(trans('report.'.$arrayResult[$value->KQSurvey]));
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart9), function($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('C' . $rowStart9, function($cell) use($value, $result) {
                $cell->setValue($value->SauTK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart9, function($cell) use($value, $result) {
                if ($result->total->SauTK != 0) {
                    $cell->setValue(number_format(round(($value->SauTK / $result->total->SauTK) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart9, function($cell) use($value, $result) {
                $cell->setValue($value->SauBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart9, function($cell) use($value, $result) {
                if ($result->total->SauBT != 0) {
                    $cell->setValue(number_format(round(($value->SauBT / $result->total->SauBT) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart9, function($cell) use($value) {
                $cell->setValue($value->TongCong);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart9, function($cell) use($value, $result) {
                if ($result->total->TongCong != 0) {
                    $cell->setValue(number_format(round(($value->TongCong / ($result->total->TongCong)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });
            $rowStart9++;
        }
        //Tạo row tổng cộng
        $sheet->mergeCells('A' . $rowStart9 . ':B' . $rowStart9)->cell('A' . $rowStart9, function($cell) use( $result) {
            $cell->setValue(trans('report.Total'));
            $cell->setFontWeight('bold');
            $this->setTitleBodyTable($cell);
        })->cell('B' . ($rowStart9), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . $rowStart9, function($cell) use($result) {
            $cell->setValue($result->total->SauTK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('D' . $rowStart9, function($cell) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('E' . $rowStart9, function($cell) use($result) {
            $cell->setValue($result->total->SauBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('F' . $rowStart9, function($cell) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('G' . $rowStart9, function($cell) use($result) {
            $cell->setValue($result->total->TongCong);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('H' . $rowStart9, function($cell) {
            $cell->setValue('100 %');
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        });
        return $rowStart9;
    }

    //Tạo báo cáo số lượng NPS, đã rút gọn
    public function createAmountNps($sheet, $result, $rowStart10) {
        $sheet->cell('A' . $rowStart10, function($cell) use($result) {
            $cell->setValue('1.2. '.trans('report.SurveyNPS Quality'));
            $this->setTitleTable($cell);
        })->mergeCells('A' . ($rowStart10 + 1) . ':B' . ($rowStart10 + 1))->setWidth('A', 68)->cell('A' . ($rowStart10 + 1), function($cell) {
            $cell->setValue(trans('report.SurveyCareChannel'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('C' . ($rowStart10 + 1) . ':H' . ($rowStart10 + 1))->cell('C' . ($rowStart10 + 1), function($cell) {
            $cell->setValue('Happy Call');
            $this->setTitleHeaderTable($cell);
        });
        $this->extraFunc->setColumnByFormat('C', 6, $sheet, $rowStart10 + 1, 'thin-thin-thin-thin');
             $rowStart10+=1;
            $sheet->mergeCells('A' . ($rowStart10 + 1) . ':B' . ($rowStart10 + 2))->setWidth('A', 68)->cell('A' . ($rowStart10 + 1), function($cell) {
            $cell->setValue(trans('report.QuantitySurveyNPS'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('C' . ($rowStart10 + 1) . ':D' . ($rowStart10 + 1))->cell('C' . ($rowStart10 + 1), function($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('E' . ($rowStart10 + 1) . ':F' . ($rowStart10 + 1))->cell('E' . ($rowStart10 + 1), function($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowStart10 + 1) . ':H' . ($rowStart10 + 1))->cell('G' . ($rowStart10 + 1), function($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('D' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('E' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Quantity'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowStart10 + 2), function($cell) {
            $cell->setValue(trans('report.Percent'));
            $this->setTitleHeaderTable($cell);
        });
        $tongSauTK = $tongSauBT = $tongCong = 0;
        $result = (object) $result;
        $tongSauTK+=is_array($result->surveyNPS) ? $result->surveyNPS[0]->SauTK : 0;
        $tongSauBT+=is_array($result->surveyNPS) ? $result->surveyNPS[0]->SauBT : 0;
        $tongCong+=is_array($result->surveyNPS) ? $result->surveyNPS[0]->TongCong : 0;
        $sheet->mergeCells('A' . ($rowStart10 + 3) . ':B' . ($rowStart10 + 3))->cell('A' . ($rowStart10 + 3), function($cell) use($result) {
            $cell->setValue(trans('report.CustomerRated'));
            $this->setTitleBodyTable($cell);
        })->cell('B' . ($rowStart10 + 3), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . ($rowStart10 + 3), function($cell) use( $result) {
            $cell->setValue($result->surveyNPS != [] ? $result->surveyNPS[0]->SauTK : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('D' . ($rowStart10 + 3), function($cell) use($result) {
            if ($result->survey != []) {
                if ($result->survey[0]->SauTK != 0) {
                    $cell->setValue(number_format(round(($result->surveyNPS[0]->SauTK / $result->survey[0]->SauTK) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
            } else
                $cell->setValue("0 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('E' . ($rowStart10 + 3), function($cell) use( $result) {
            $cell->setValue($result->surveyNPS != [] ? $result->surveyNPS[0]->SauBT : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('F' . ($rowStart10 + 3), function($cell) use($result) {
            if ($result->survey != []) {
                if ($result->survey[0]->SauBT != 0) {
                    $cell->setValue(number_format(round(($result->surveyNPS[0]->SauBT / $result->survey[0]->SauBT) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
            } else
                $cell->setValue("0 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('G' . ($rowStart10 + 3), function($cell) use($result) {
            $cell->setValue($result->surveyNPS != [] ? $result->surveyNPS[0]->TongCong : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('H' . ($rowStart10 + 3), function($cell) use($result) {
            if ($result->survey != []) {
                if ($result->survey[0]->TongCong != 0) {
                    $cell->setValue(number_format(round(($result->surveyNPS[0]->TongCong / ($result->survey[0]->TongCong)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
            } else
                $cell->setValue("0 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        });
        //Tạo row  ý kiến
        $rowStart10 += 4;
        if (is_array($result->surveyNPSNoRated)) {
            foreach ($result->surveyNPSNoRated as $key => $value) {
                $tongSauTK+=$value->SauTK;
                $tongSauBT+=$value->SauBT;
                $tongCong+=$value->TongCong;
                $sheet->mergeCells('A' . $rowStart10 . ':B' . $rowStart10)->cell('A' . $rowStart10, function($cell) use($value) {
                    $cell->setValue(trans('report.'.$value->KQSurveyNPS));
                    $this->setTitleBodyTable($cell);
                })->cell('B' . ($rowStart10), function($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('C' . $rowStart10, function($cell) use($value) {
                    $cell->setValue($value->SauTK);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('D' . $rowStart10, function($cell) use($value, $result) {
                    if ($result->survey[0]->SauTK != 0) {
                        $cell->setValue(number_format(round(($value->SauTK / $result->survey[0]->SauTK) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('E' . $rowStart10, function($cell) use($value) {
                    $cell->setValue($value->SauBT);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('F' . $rowStart10, function($cell) use($value, $result) {
                    if ($result->survey[0]->SauBT != 0) {
                        $cell->setValue(number_format(round(($value->SauBT / $result->survey[0]->SauBT) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('G' . $rowStart10, function($cell) use($value) {
                    $cell->setValue($value->TongCong);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('H' . $rowStart10, function($cell) use($value, $result) {
                    if ($result->survey[0]->TongCong != 0) {
                        $cell->setValue(number_format(round(($value->TongCong / ($result->survey[0]->TongCong)) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
                $rowStart10++;
            }
        }
        //Tạo row no_rate

        $sheet->mergeCells('A' . $rowStart10 . ':B' . $rowStart10)->cell('A' . $rowStart10, function($cell) use($result) {
            $cell->setValue(trans('report.'.$result->surveyNPSNoRated_Note[0]->KQSurveyNPS));
            $this->setTitleBodyTable($cell);
        })->cell('B' . ($rowStart10), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . $rowStart10, function($cell) use( $result) {
            $cell->setValue($result->surveyNPSNoRated_Note[0]->SauTK != null ? $result->surveyNPSNoRated_Note[0]->SauTK : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('D' . $rowStart10, function($cell) use($result) {
            if ($result->survey != [] && $result->survey[0]->SauTK != 0) {
                $cell->setValue(number_format(round(($result->surveyNPSNoRated_Note[0]->SauTK / $result->survey[0]->SauTK) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('E' . $rowStart10, function($cell) use( $result) {
            $cell->setValue($result->surveyNPSNoRated_Note[0]->SauBT != null ? $result->surveyNPSNoRated_Note[0]->SauBT : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('F' . $rowStart10, function($cell) use($result) {
            if ($result->survey != [] && $result->survey[0]->SauBT != 0) {
                $cell->setValue(number_format(round(($result->surveyNPSNoRated_Note[0]->SauBT / $result->survey[0]->SauBT) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('G' . $rowStart10, function($cell) use($result) {
            $cell->setValue($result->surveyNPSNoRated_Note[0]->TongCong != null ? $result->surveyNPSNoRated_Note[0]->TongCong : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('H' . $rowStart10, function($cell) use($result) {
            if ($result->survey != [] && $result->survey[0]->TongCong != 0) {
                $cell->setValue(number_format(round(($result->surveyNPSNoRated_Note[0]->TongCong / ($result->survey[0]->TongCong)) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        });
        //Tạo row đã đánh giá, ko hỏi lại
        $rowStart10++;
        $result->totalNPS = (object) $result->totalNPS;
        $sheet->mergeCells('A' . $rowStart10 . ':B' . $rowStart10)->cell('A' . $rowStart10, function($cell) {
            $cell->setValue(trans('report.CustomerRatedNotAskAgain'));
            $this->setTitleBodyTable($cell);
        })->cell('B' . ($rowStart10), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . $rowStart10, function($cell) use($result, $tongSauTK) {
            $cell->setValue( $result->survey != [] ? $result->survey[0]->SauTK - $tongSauTK : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('D' . $rowStart10, function($cell) use($result, $tongSauTK) {
            if ($result->survey != [] && $result->survey[0]->SauTK != 0) {
                $cell->setValue(number_format(round((($result->survey[0]->SauTK - $tongSauTK) / $result->survey[0]->SauTK) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('E' . $rowStart10, function($cell) use($result, $tongSauBT) {
            $cell->setValue(  $result->survey != [] ? $result->survey[0]->SauBT - $tongSauBT : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('F' . $rowStart10, function($cell) use($result, $tongSauBT) {
            if ($result->survey !=[] && $result->survey[0]->SauBT != 0) {
                $cell->setValue(number_format(round((($result->survey[0]->SauBT - $tongSauBT) / $result->survey[0]->SauBT) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('G' . $rowStart10, function($cell) use($result, $tongCong) {
            $cell->setValue(  $result->survey != [] ? $result->survey[0]->TongCong - $tongCong : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('H' . $rowStart10, function($cell) use($result, $tongCong) {
            if ($result->survey !=[] && $result->totalNPS->TongCong != 0) {
                $cell->setValue(number_format(round((($result->survey[0]->TongCong - $tongCong) / ($result->survey[0]->TongCong)) * 100, 2), 2) . " %");
            } else {
                $cell->setValue("0 %");
            }
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        });
        //Tạo row tổng cộng
        $rowStart10++;
        $sheet->mergeCells('A' . $rowStart10 . ':B' . $rowStart10)->cell('A' . $rowStart10, function($cell) use($result) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleBodyTable($cell);
            $cell->setFontWeight('bold');
        })->cell('B' . ($rowStart10), function($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('C' . $rowStart10, function($cell) use( $result) {
            $cell->setValue($result->survey !=[] ? $result->survey[0]->SauTK : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('D' . $rowStart10, function($cell) use($result) {

            $cell->setValue("100 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('E' . $rowStart10, function($cell) use( $result) {
            $cell->setValue($result->survey !=[] ? $result->survey[0]->SauBT : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('F' . $rowStart10, function($cell) use($result) {
            $cell->setValue("100 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('G' . $rowStart10, function($cell) use($result) {
            $cell->setValue($result->survey !=[] ? $result->survey[0]->TongCong : 0);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('H' . $rowStart10, function($cell) use($result) {
            $cell->setValue("100 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        });
        return $rowStart10;
    }

    public function createGroupBranchNps($sheet, $surveyNPSBranch, $rowIndex) {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function($cell) {
            $cell->setValue('3.3 '.trans('report.BranchNPS'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 2))->cell('A' . ($rowIndex + 1), function($cell) {
            $cell->setValue(trans('report.Rating Point'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1), function($cell) {
//
            $cell->setBorder('thin', 'thin', 'thin', 'none');
        })
        ->cell('B' . ($rowIndex + 2), function($cell) {
//
            $cell->setBorder('none', 'none', 'thin', 'none');
        });

        $columnStart='C';
        foreach ($surveyNPSBranch['allLocation'] as $value) {
            $c2=$this->setColumn($columnStart, 1, $sheet, $rowIndex + 1);
//                    $c3=$this->setColumn($columnStart, 2);
            $sheet->mergeCells($columnStart . ($rowIndex + 1) . ':'.($c2) . ($rowIndex + 1))->cell($columnStart . ($rowIndex + 1), function($cell) use($value) {
                $cell->setValue(($value == 'WholeCountry') ?  trans('report.'.$value) : $value);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('thin', 'thin', 'thin', 'none');
            });
            $columnStart=$this->setColumn($columnStart, 2, $sheet, $rowIndex + 1);
        }


        $columnStart='C';
        foreach ($surveyNPSBranch['allLocation'] as $value) {
            $c1 = $this->setColumn($columnStart, 1, $sheet, $rowIndex);
            $sheet->cell($columnStart . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell(($c1) . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            });
            $columnStart = $this->setColumn($columnStart, 2, $sheet, $rowIndex);
        }
        $rowStart = $rowIndex + 3;

        foreach ($surveyNPSBranch['totalNPS'] as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($key, $value) {
                $cell->setValue(trans('report.'.$key));
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart), function($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            });
            $columnStart='C';
            foreach ($surveyNPSBranch['allLocation'] as $value2) {
                $c1=$this->setColumn($columnStart, 1, $sheet, $rowStart);
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

                $columnStart = $this->setColumn($columnStart, 2, $sheet, $rowStart);
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
        foreach ($surveyNPSBranch['allLocation'] as $value2) {
            $c2=$this->setColumn($columnStart, 1, $sheet, $rowStart);
            $sheet->cell($columnStartAveragePoint. ($rowStart), function($cell) {
                $cell->setBorder('none', 'none', 'thin', 'none');
            })->mergeCells($columnStart. ($rowStart) . ':'.$c2 . ($rowStart))->cell($columnStart . ($rowStart), function($cell) use($value, $surveyNPSBranch, $value2) {
                $cell->setBorder('thin', 'none', 'thin', 'none');
                $cell->setValue($surveyNPSBranch['averagePoint']['AVG_'.$value2]);
                $this->setTitleMainRow($cell);
            });
            $columnStart=$this->setColumn($columnStart, 2, $sheet, $rowStart);
            $columnStartAveragePoint=$this->setColumn($columnStartAveragePoint, 1, $sheet, $rowStart);
        };
        return $rowStart;
//            $sheet->cell('B' . ($rowIndex + 1), function($cell) {
//
//            $cell->setBorder('thin', 'none', 'none', 'none');
//        })->cell('B' . ($rowIndex + 2), function($cell) {
//
//            $cell->setBorder('none', 'thin', 'thin', 'none');
//        })->cell('B' . ($rowIndex + 3), function($cell) {
//
//            $cell->setBorder('none', 'thin', 'thin', 'none');
//        })->mergeCells('C' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function($cell) {
//            $cell->setValue(trans('report.Rating Quality Service'));
//            $cell->setAlignment('center');
//            $cell->setValignment('center');
//            $cell->setBackground('#8DB4E2');
//            $cell->setBorder('thin', 'thin', 'thin', 'thin');
//            $cell->setFontWeight('bold');
//        })->cell('D' . ($rowIndex + 1), function($cell) {
//
//            $this->setTitleHeaderTable($cell);
//        })
//            ->cell('E' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })
//            ->cell('F' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('G' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('H' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('I' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('J' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('K' . ($rowIndex + 1), function($cell) {
//
//                $cell->setBorder('thin', 'none', 'thin', 'none');
//            })
//            ->cell('L' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('M' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('N' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('O' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('P' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->cell('Q' . ($rowIndex + 1), function($cell) {
//
//                $this->setTitleHeaderTable($cell);
//            })->mergeCells('I' . ($rowIndex + 1) . ':Q' . ($rowIndex + 1))->cell('I' . ($rowIndex + 1), function($cell) {
//                $cell->setValue(trans('report.Rating Staff'));
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $cell->setBackground('#8DB4E2');
//                $cell->setBorder('thin', 'thin', 'thin', 'thin');
//                $cell->setFontWeight('bold');
//            })
//            ->mergeCells('C' . ($rowIndex + 2) . ':E' . ($rowIndex + 2))->cell('C' . ($rowIndex + 2), function($cell) {
//                $cell->setBorder('none', 'thin', 'thin', 'thin');
//                $cell->setBackground('#8DB4E2');
//            })->cell('C' . ($rowIndex + 2), function($cell) {
//                $cell->setValue(trans('report.InternetService'));
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $cell->setBackground('#8DB4E2');
//                $cell->setFontWeight('bold');
//                $cell->setBorder('none', 'thin', 'none', 'none');
//            })->cell('D' . ($rowIndex + 2), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'none', 'none');
//            })->cell('D' . ($rowIndex + 3), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'thin', 'none');
//            })->cell('H' . ($rowIndex + 2), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'thin', 'none');
//            })->cell('J' . ($rowIndex + 2), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'thin', 'none');
//            })->cell('J' . ($rowIndex + 3), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'none', 'none');
//            })->cell('L' . ($rowIndex + 2), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'none', 'none');
//            })->cell('L' . ($rowIndex + 3), function($cell) {
//
//                $cell->setBorder('none', 'thin', 'none', 'none');
//            })->cell('P' . ($rowIndex + 3), function($cell) {
//
//                $cell->setBorder('thin', 'none', 'none', 'none');
//            })->mergeCells('F' . ($rowIndex + 2) . ':H' . ($rowIndex + 2))->cell('F' . ($rowIndex + 3), function($cell) {
//                $cell->setBackground('#8DB4E2');
//                $cell->setBorder('none', 'none', 'none', 'none');
//            })->cell('F' . ($rowIndex + 2), function($cell) {
//                $cell->setValue(trans('report.Service Quality Statistical'));
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $cell->setBackground('#8DB4E2');
//                $cell->setBorder('none', 'none', 'none', 'none');
//                $cell->setFontWeight('bold');
//            })->mergeCells('I' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('I' . ($rowIndex + 2), function($cell) {
//                $cell->setValue(trans('report.Saler'));
//                $this->setTitleHeaderTable($cell);
//            })->mergeCells('L' . ($rowIndex + 2) . ':N' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function($cell) {
//                $cell->setValue(trans('report.Technical Staff'));
//                $this->setTitleHeaderTable($cell);
//            })
//            ->mergeCells('O' . ($rowIndex + 2) . ':Q' . ($rowIndex + 2))->cell('O' . ($rowIndex + 2), function($cell) {
//                $cell->setValue(trans('report.Staff Statistical'));
//                $this->setTitleHeaderTable($cell);
////            var_dump('1');
//            })
//            ->cell('C' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Quantity'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('D' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('E' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })
//            ->cell('F' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Quantity'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('G' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('H' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })
//            ->cell('I' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Quantity'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('J' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('K' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })
//            ->cell('L' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Quantity'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('M' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('N' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })
//            ->cell('O' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Quantity'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('P' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            })->cell('Q' . ($rowIndex + 3), function($cell) {
//                $cell->setValue(trans('report.Percent'));
//                $this->setTitleHeaderTable($cell);
//            }) ;
//
//        $rowStart = $rowIndex + 4;
////        dump($detailCSAT);die;
////        $detailCSAT['avg'] = (object) $detailCSAT['avg'];
////        $detailCSAT['total'] = (object) $detailCSAT['total'];
////         dump((object)$detailObjectCSAT['totalCSAT'],(object)$detailObjectCSAT['averagePoint'] );die;
//        foreach ($detailObjectCSAT['totalCSAT'] as $key => $value) {
//            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function($cell) use($value) {
//                $cell->setValue(trans('report.'.$value['Csat']));
//                $this->setTitleBodyTable($cell);
//            })->cell('B' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('C' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['Net']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            })->cell('D' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NetPercent']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            });
//
//            if ($key == 3 || $key == 'total') {
//                $sheet->cell('E' . $rowStart, function($cell) use($value) {
//                    $cell->setValue($value['NetPercent']);
//                    $cell->setAlignment('center');
//                    $cell->setValignment('center');
//                    $this->setBorderCell($cell);
//                });
//            } else if ($key == 1 || $key == 4) {
//                if ($key == 1)
//                    $rowCombine = $value['NetPercent'] + $detailObjectCSAT['totalCSAT'][2]['NetPercent'] . '%';
////                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
//                else
//                    $rowCombine = $value['NetPercent'] + $detailObjectCSAT['totalCSAT'][5]['NetPercent'] . '%';
////                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
//                $sheet->cell('E' . $rowStart, function($cell) use($rowCombine) {
//                    $cell->setValue($rowCombine);
//                    $cell->setAlignment('center');
//                    $cell->setBorder('thin', 'thin', 'none', 'thin');
////                $this->setTitleBodyTable($cell);
//                });
//            }
//            $sheet->cell('F' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NetAndTV']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            })->cell('G' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NetAndTVPercent']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            });
//
//            if ($key == 3 || $key == 'total') {
//                $sheet->cell('H' . $rowStart, function($cell) use($value) {
//                    $cell->setValue($value['NetAndTVPercent']);
//                    $cell->setAlignment('center');
//                    $cell->setValignment('center');
//                    $this->setBorderCell($cell);
//                });
//            } else if ($key == 1 || $key == 4) {
//                if ($key == 1)
//                    $rowCombine = $value['NetAndTVPercent'] + $detailObjectCSAT['totalCSAT'][2]['NetAndTVPercent'] . '%';
////                                        echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
//                else
//                    $rowCombine = $value['NetAndTVPercent'] + $detailObjectCSAT['totalCSAT'][5]['NetAndTVPercent'] . '%';
////                                        echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
//                $sheet->cell('H' . $rowStart, function($cell) use($rowCombine) {
//                    $cell->setValue($rowCombine);
//                    $cell->setAlignment('center');
//                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                });
//            }
//
//            $sheet->cell('I' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NVKinhDoanh']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            })->cell('J' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NVKinhDoanhPercent']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            });
//
//            if ($key == 3 || $key == 'total') {
//                $sheet->cell('K' . $rowStart, function($cell) use($value) {
//                    $cell->setValue($value['NVKinhDoanhPercent']);
//                    $cell->setAlignment('center');
//                    $cell->setValignment('center');
//                    $this->setBorderCell($cell);
//                });
//            } else if ($key == 1 || $key == 4) {
//                if ($key == 1)
//                    $rowCombine = $value['NVKinhDoanhPercent'] + $detailObjectCSAT['totalCSAT'][2]['NVKinhDoanhPercent'] . '%';
////                                        echo $res['NVKinhDoanhPercent'] + $totalCSAT[2]['NVKinhDoanhPercent'] . '%';
//                else
//                    $rowCombine = $value['NVKinhDoanhPercent'] + $detailObjectCSAT['totalCSAT'][5]['NVKinhDoanhPercent'] . '%';
////                                        echo $res['NVKinhDoanhPercent'] + $totalCSAT[5]['NVKinhDoanhPercent'] . '%';
//                $sheet->cell('K' . $rowStart, function($cell) use($rowCombine) {
//                    $cell->setValue($rowCombine);
//                    $cell->setAlignment('center');
//                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                });
//            }
//
//            $sheet->cell('L' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NVKT']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            })->cell('M' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['NVKTPercent']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            });
//
//            if ($key == 3 || $key == 'total') {
//                $sheet->cell('N' . $rowStart, function($cell) use($value) {
//                    $cell->setValue($value['NVKTPercent']);
//                    $cell->setAlignment('center');
//                    $cell->setValignment('center');
//                    $this->setBorderCell($cell);
//                });
//            } else if ($key == 1 || $key == 4) {
//                if ($key == 1)
//                    $rowCombine = $value['NVKTPercent'] + $detailObjectCSAT['totalCSAT'][2]['NVKTPercent'] . '%';
////                                        echo $res['NVKTPercent'] + $totalCSAT[2]['NVKTPercent'] . '%';
//                else
//                    $rowCombine = $value['NVKTPercent'] + $detailObjectCSAT['totalCSAT'][5]['NVKTPercent'] . '%';
////                                        echo $res['NVKTPercent'] + $totalCSAT[5]['NVKTPercent'] . '%';
//                $sheet->cell('N' . $rowStart, function($cell) use($rowCombine) {
//                    $cell->setValue($rowCombine);
//                    $cell->setAlignment('center');
//                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                });
//            }
//
//
//
//            $sheet->cell('O' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['TongHopNV']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            })->cell('P' . $rowStart, function($cell) use($value) {
//                $cell->setValue($value['TongHopNVPercent']);
//                $cell->setAlignment('center');
//                $cell->setValignment('center');
//                $this->setBorderCell($cell);
//            });
//
//            if ($key == 3 || $key == 'total') {
//                $sheet->cell('Q' . $rowStart, function($cell) use($value) {
//                    $cell->setValue($value['TongHopNVPercent']);
//                    $cell->setAlignment('center');
//                    $cell->setValignment('center');
//                    $this->setBorderCell($cell);
//                });
//            } else if ($key == 1 || $key == 4) {
//                if ($key == 1)
//                    $rowCombine = $value['TongHopNVPercent'] + $detailObjectCSAT['totalCSAT'][2]['TongHopNVPercent'] . '%';
////                                        echo $res['TongHopNVPercent'] + $totalCSAT[2]['TongHopNVPercent'] . '%';
//                else
//                    $rowCombine = $value['TongHopNVPercent'] + $detailObjectCSAT['totalCSAT'][5]['TongHopNVPercent'] . '%';
////                                        echo $res['TongHopNVPercent'] + $totalCSAT[5]['TongHopNVPercent'] . '%';
//                $sheet->cell('Q' . $rowStart, function($cell) use($rowCombine) {
//                    $cell->setValue($rowCombine);
//                    $cell->setAlignment('center');
//                    $cell->setBorder('thin', 'thin', 'none', 'thin');
//                });
//            }
//            $rowStart++;
//        }
//        //Tạo row điểm trung bình
//        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function($cell) use($value) {
//            $cell->setValue(trans('report.Average Point'));
//            $this->setTitleMainRow($cell);
//        })->cell('B' . ($rowStart), function($cell) {
//
//            $cell->setBorder('none', 'none', 'thin', 'none');
//        })->cell('D' . ($rowStart), function($cell) {
//
//            $cell->setBorder('none', 'none', 'thin', 'none');
//        })
//            ->cell('E' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })
//            ->cell('G' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })
//            ->cell('K' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('M' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('Q' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('F' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('H' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('J' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('L' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('N' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->cell('P' . ($rowStart), function($cell) {
//
//                $cell->setBorder('none', 'none', 'thin', 'none');
//            })->mergeCells('C' . ($rowStart) . ':E' . ($rowStart))->cell('C' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
//                $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NET']);
//                $this->setTitleMainRow($cell);
//            })->mergeCells('F' . ($rowStart) . ':H' . ($rowStart))->cell('F' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
//                $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NetAndTV']);
//                $this->setTitleMainRow($cell);
//            })->mergeCells('I' . ($rowStart) . ':K' . ($rowStart))->cell('I' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
//                $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NVKinhDoanh']);
//                $this->setTitleMainRow($cell);
//            })
//            ->mergeCells('L' . ($rowStart) . ':N' . ($rowStart))->cell('L' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
//                $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_NVKT']);
//                $this->setTitleMainRow($cell);
//            })
//            ->mergeCells('O' . ($rowStart) . ':Q' . ($rowStart))->cell('O' . ($rowStart), function($cell) use($value, $detailObjectCSAT) {
//                $cell->setValue($detailObjectCSAT['averagePoint']['ĐTB_TongHopNV']);
//                $this->setTitleMainRow($cell);
//            })
//        ;
//
//        return $rowStart;
    }

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
            if($rowIndex != 30 && $rowIndex != 31)
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
