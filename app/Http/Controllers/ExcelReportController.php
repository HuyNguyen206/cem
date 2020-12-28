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

class ExcelReportController extends Controller
{

    protected $modelSurveySections;
    protected $extraFunc;
    protected $userGranted;

    public function __construct()
    {
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

    //Tạo bảng thống kê chi tiết năng suất nhân viên
    public function createProductivityReport($sheet, $result, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 20)->setWidth('B', 18)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('1. ' . trans('report.ProductivityReport'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('');
            $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':I' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->cell('C' . ($rowIndex + 1) . ':Q' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('J' . ($rowIndex + 1) . ':Q' . ($rowIndex + 1))->cell('J' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('A', 20)->cell('A' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.UserName'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('B', 20)->cell('B' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.TotalQuantityOfSurveys'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('C', 20)->cell('C' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.MeetUser'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('D', 20)->cell('D' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.DidntMeetUser'));
            $this->setTitleHeaderTable($cell);
        })
            ->setWidth('E', 20)->cell('E' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.MeetCustomerCustomerDeclinedToTakeSurvey'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('F', 20)->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.CannotContact'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('G', 20)->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.NoNeedContact'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('H', 20)->cell('H' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.ContactSuccess'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('I', 20)->cell('I' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.ContactSuccessPercent'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('J', 20)->cell('J' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.TotalQuantityOfSurveys'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('K', 20)->cell('K' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.MeetUser'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('L', 20)->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.DidntMeetUser'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('M', 20)->cell('M' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.MeetCustomerCustomerDeclinedToTakeSurvey'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('N', 20)->cell('N' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.CannotContact'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('O', 20)->cell('O' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.NoNeedContact'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('P', 20)->cell('P' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.ContactSuccess'));
                $this->setTitleHeaderTable($cell);
            })
            ->setWidth('Q', 20)->cell('Q' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.ContactSuccessPercent'));
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 3;
        $totalKS_STK = $GNSD_STK = $KGNSD_STK = $KHTC_STK = $KLLD_STK = $KCLH_STK = $totalKS_SBT = $GNSD_SBT = $KGNSD_SBT = $KHTC_SBT = $KLLD_SBT = $KCLH_SBT = 0;
        foreach ($result as $key => $value) {
            $totalKS_STK += $value->TongKhaoSat_STK;
            $GNSD_STK += $value->GapNguoiSD_STK;
            $KGNSD_STK += $value->KhongGapNguoiSD_STK;
            $KHTC_STK += $value->KHTuChoiCS_STK;
            $KLLD_STK += $value->KhongLienLacDuoc_STK;
            $KCLH_STK += $value->KhongCanLienHe_STK;


            $totalKS_SBT += $value->TongKhaoSat_SBT;
            $GNSD_SBT += $value->GapNguoiSD_SBT;
            $KGNSD_SBT += $value->KhongGapNguoiSD_SBT;
            $KHTC_SBT += $value->KHTuChoiCS_SBT;
            $KLLD_SBT += $value->KhongLienLacDuoc_SBT;
            $KCLH_SBT += $value->KhongCanLienHe_SBT;

            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->section_user_name);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->GapNguoiSD_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongGapNguoiSD_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KHTuChoiCS_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongLienLacDuoc_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongCanLienHe_STK);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_STK - ($value->KhongLienLacDuoc_STK + $value->KhongCanLienHe_STK));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('I' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_STK != 0 ? round((($value->TongKhaoSat_STK - ($value->KhongLienLacDuoc_STK + $value->KhongCanLienHe_STK)) / $value->TongKhaoSat_STK) * 100, 2) . "%" : 0 . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('J' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('K' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->GapNguoiSD_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('L' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongGapNguoiSD_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('M' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KHTuChoiCS_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('N' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongLienLacDuoc_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('O' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->KhongCanLienHe_SBT);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('P' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_SBT - ($value->KhongLienLacDuoc_SBT + $value->KhongCanLienHe_SBT));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('Q' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TongKhaoSat_SBT != 0 ? round((($value->TongKhaoSat_SBT - ($value->KhongLienLacDuoc_SBT + $value->KhongCanLienHe_SBT)) / $value->TongKhaoSat_SBT) * 100, 2) . "%" : 0 . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            });
            $rowStart++;
        }
        //Tạo row tổng cộng,total
        $sheet->cell('A' . ($rowStart), function ($cell) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . $rowStart, function ($cell) use ($totalKS_STK) {
            $cell->setValue($totalKS_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('C' . $rowStart, function ($cell) use ($GNSD_STK) {
            $cell->setValue($GNSD_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('D' . $rowStart, function ($cell) use ($KGNSD_STK) {
            $cell->setValue($KGNSD_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('E' . $rowStart, function ($cell) use ($KHTC_STK) {
            $cell->setValue($KHTC_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('F' . $rowStart, function ($cell) use ($KLLD_STK) {
            $cell->setValue($KLLD_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('G' . $rowStart, function ($cell) use ($KCLH_STK) {
            $cell->setValue($KCLH_STK);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('H' . $rowStart, function ($cell) use ($totalKS_STK, $KLLD_STK, $KCLH_STK) {
            $cell->setValue($totalKS_STK - ($KLLD_STK + $KCLH_STK));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('I' . $rowStart, function ($cell) use ($totalKS_STK, $KLLD_STK, $KCLH_STK) {
            $cell->setValue($totalKS_STK != 0 ? round((($totalKS_STK - ($KLLD_STK + $KCLH_STK)) / $totalKS_STK) * 100, 2) . "%" : 0 . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('J' . $rowStart, function ($cell) use ($totalKS_SBT) {
            $cell->setValue($totalKS_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('K' . $rowStart, function ($cell) use ($GNSD_SBT) {
            $cell->setValue($GNSD_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('L' . $rowStart, function ($cell) use ($KGNSD_SBT) {
            $cell->setValue($KGNSD_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('M' . $rowStart, function ($cell) use ($KHTC_SBT) {
            $cell->setValue($KHTC_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('N' . $rowStart, function ($cell) use ($KLLD_SBT) {
            $cell->setValue($KLLD_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('O' . $rowStart, function ($cell) use ($KCLH_SBT) {
            $cell->setValue($KCLH_SBT);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('P' . $rowStart, function ($cell) use ($totalKS_SBT, $KLLD_SBT, $KCLH_SBT) {
            $cell->setValue($totalKS_SBT - ($KLLD_SBT + $KCLH_SBT));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('Q' . $rowStart, function ($cell) use ($totalKS_SBT, $KLLD_SBT, $KCLH_SBT) {
            $cell->setValue($totalKS_SBT != 0 ? round((($totalKS_SBT - ($KLLD_SBT + $KCLH_SBT)) / $totalKS_SBT) * 100, 2) . "%" : 0 . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        });
        $rowStart++;
        return $rowStart;
    }

    //Tạo bảng thống kê chi tiết CSAT, đã rút gọn
    public function createDetailCsat($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 50)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('1. ' . trans('report.SatisfactionOfCustomerStatistical'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':B' . ($rowIndex + 1))->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.TouchPoint'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('A' . ($rowIndex + 2) . ':B' . ($rowIndex + 4))->cell('A' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Rating Point'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1), function ($cell) {

            $cell->setBorder('thin', 'none', 'thin', 'none');
        })->cell('B' . ($rowIndex + 2), function ($cell) {

            $cell->setBorder('none', 'thin', 'none', 'none');
        })->cell('B' . ($rowIndex + 4), function ($cell) {

            $cell->setBorder('none', 'thin', 'thin', 'none');
        })->mergeCells('C' . ($rowIndex + 1) . ':H' . ($rowIndex + 1))->cell('C' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
        });
        $this->extraFunc->setColumnTitleHeaderTable('C', 10, $sheet, $rowIndex + 1);
        $sheet->mergeCells('I' . ($rowIndex + 1) . ':L' . ($rowIndex + 1))->cell('I' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
        })
            ->mergeCells('C' . ($rowIndex + 2) . ':D' . ($rowIndex + 2))->mergeCells('C' . ($rowIndex + 3) . ':D' . ($rowIndex + 3))->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('C' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Saler'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
            });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowIndex + 2, 'thin-thin-thin-thin');
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowIndex + 3, 'thin-thin-thin-thin');
        $sheet->mergeCells('E' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->mergeCells('E' . ($rowIndex + 3) . ':F' . ($rowIndex + 3))->cell('E' . ($rowIndex + 3), function ($cell) {
            $cell->setBackground('#8DB4E2');
        })->cell('E' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Deployer'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setFontWeight('bold');
        })->mergeCells('G' . ($rowIndex + 2) . ':H' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Rating Quality Service'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowIndex + 3) . ':H' . ($rowIndex + 3))->cell('G' . ($rowIndex + 3), function ($cell) {
            $cell->setValue('Internet');
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('I' . ($rowIndex + 2) . ':J' . ($rowIndex + 2))->mergeCells('I' . ($rowIndex + 3) . ':J' . ($rowIndex + 3))->cell('I' . ($rowIndex + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('I' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.MaintainanceStaff'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
            })->mergeCells('K' . ($rowIndex + 2) . ':L' . ($rowIndex + 2))->cell('K' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Rating Quality Service'));
                $this->setTitleHeaderTable($cell);
            })->mergeCells('K' . ($rowIndex + 3) . ':L' . ($rowIndex + 3))->cell('K' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })
            ->cell('C' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell('D' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell('E' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell('F' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell('G' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell('H' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell('I' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell('J' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->cell('K' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->cell('L' . ($rowIndex + 4), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 5;
//        dump($detailCSAT);die;
        $detailCSAT['avg'] = (object)$detailCSAT['avg'];
        $detailCSAT['total'] = (object)$detailCSAT['total'];
        foreach ($detailCSAT['survey'] as $key => $value) {
            $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue(trans('report.' . $value->DanhGia));
                $this->setTitleBodyTable($cell);
            })->cell('B' . ($rowStart), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKinhDoanh);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                if ($detailCSAT['avg']->NVKinhDoanh != 0) {
                    $cell->setValue(number_format(round(($value->NVKinhDoanh / ($detailCSAT['total']->NVKinhDoanh)) * 100, 2), 2) . " %");
                } else {
                    $cell->setValue("0 %");
                }
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('E' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTrienKhai);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('F' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    if ($detailCSAT['avg']->NVTrienKhai != 0) {
                        $cell->setValue(number_format(round(($value->NVTrienKhai / ($detailCSAT['total']->NVTrienKhai)) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('G' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->DGDichVu_Net);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('H' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    if ($detailCSAT['avg']->DGDichVu_Net != 0) {
                        $cell->setValue(number_format(round(($value->DGDichVu_Net / ($detailCSAT['total']->DGDichVu_Net)) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }

                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('I' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBaoTri);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    if ($detailCSAT['avg']->NVBaoTri != 0) {
                        $cell->setValue(number_format(round(($value->NVBaoTri / ($detailCSAT['total']->NVBaoTri)) * 100, 2), 2) . " %");
                    } else {
                        $cell->setValue("0 %");
                    }

                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('K' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->DVBaoTri_Net);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('L' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    if ($detailCSAT['avg']->DVBaoTri_Net != 0) {
                        $cell->setValue(number_format(round(($value->DVBaoTri_Net / ($detailCSAT['total']->DVBaoTri_Net)) * 100, 2), 2) . " %");
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
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . $rowStart, function ($cell) use ($value) {
            $cell->setValue(trans('report.Total'));
            $this->setTitleBodyTable($cell);
            $cell->setFontWeight('bold');
        })->cell('B' . ($rowStart), function ($cell) {

            $cell->setBorder('none', 'none', 'thin', 'none');
        })->cell('C' . $rowStart, function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['total']->NVKinhDoanh);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->cell('D' . $rowStart, function ($cell) use ($value, $detailCSAT) {
            $cell->setValue("100 %");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })
            ->cell('E' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue($detailCSAT['total']->NVTrienKhai);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('F' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue("100 %");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('G' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue($detailCSAT['total']->DGDichVu_Net);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('H' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue("100 %");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })
            ->cell('I' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue($detailCSAT['total']->NVBaoTri);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue("100 %");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('K' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue($detailCSAT['total']->DVBaoTri_Net);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->cell('L' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                $cell->setValue("100 %");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
        //Tạo row điểm trung bình
        $rowStart++;
        $sheet->mergeCells('A' . $rowStart . ':B' . $rowStart)->cell('A' . ($rowStart), function ($cell) use ($value) {
            $cell->setValue(trans('report.Average Point'));
            $this->setTitleMainRow($cell);
        });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowStart, 'thin-thin-thin-thin');
        $sheet->mergeCells('C' . ($rowStart) . ':D' . ($rowStart))->cell('C' . ($rowStart), function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['avg']->NVKinhDoanh);
            $this->setTitleMainRow($cell);
        })->mergeCells('E' . ($rowStart) . ':F' . ($rowStart))->cell('E' . ($rowStart), function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['avg']->NVTrienKhai);
            $this->setTitleMainRow($cell);
        })->mergeCells('G' . ($rowStart) . ':H' . ($rowStart))->cell('G' . ($rowStart), function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['avg']->DGDichVu_Net);
            $this->setTitleMainRow($cell);
        })->mergeCells('I' . ($rowStart) . ':J' . ($rowStart))->cell('I' . ($rowStart), function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['avg']->NVBaoTri);
            $this->setTitleMainRow($cell);
        })->mergeCells('K' . ($rowStart) . ':L' . ($rowStart))->cell('K' . ($rowStart), function ($cell) use ($value, $detailCSAT) {
            $cell->setValue($detailCSAT['avg']->DVBaoTri_Net);
            $this->setTitleMainRow($cell);
        });
        return $rowStart;
    }

    //Tạo bảng thống kê csat12 nhân viên
    public function createDetailStaffCsat12($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 68)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('2.CSAT Nhân viên');
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':A' . ($rowIndex + 3))->setWidth('A', 40)->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Vùng');
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai DirectSales');
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('L' . ($rowIndex + 1) . ':U' . ($rowIndex + 1))->cell('L' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai TLS');
            $this->setTitleHeaderTable($cell);
        })->cell('V' . ($rowIndex + 1) . ':Z' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('V' . ($rowIndex + 1) . ':Z' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau bảo trì TIN-PNC');
            $this->setTitleHeaderTable($cell);
        })->cell('AA' . ($rowIndex + 1) . ':AE' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AA' . ($rowIndex + 1) . ':AE' . ($rowIndex + 1))->cell('AA' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau bảo trì INDO');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowIndex + 1) . ':AE' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('AF' . ($rowIndex + 1) . ':AJ' . ($rowIndex + 1))->cell('AF' . ($rowIndex + 1), function ($cell) {
                $cell->setValue('Sau thu cước tại nhà');
                $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
            })->mergeCells('AK' . ($rowIndex + 1) . ':AO' . ($rowIndex + 1))->cell('AK' . ($rowIndex + 1), function ($cell) {
                $cell->setValue('Sau GDTQ');
                $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
            })->mergeCells('AP' . ($rowIndex + 1) . ':AY' . ($rowIndex + 1))->cell('AP' . ($rowIndex + 1), function ($cell) {
                $cell->setValue('Sau triển khai sale tại quầy');
                $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
            })->mergeCells('AZ' . ($rowIndex + 1) . ':BD' . ($rowIndex + 1))->cell('AZ' . ($rowIndex + 1), function ($cell) {
                $cell->setValue('Sau triển khai Swap');
                $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
            })
            ->cell('AG' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })
            ->cell('AH' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })
            ->cell('AI' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })
            ->cell('AJ' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'thin', 'thin', 'none');
            })->cell('AL' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AM' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AN' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AO' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AP' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AQ' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AR' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AS' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AT' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AU' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AV' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AW' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AX' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AY' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AZ' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('BA' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('BB' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('BC' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('BD' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })
            ->mergeCells('B' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('G' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('P' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('L' . ($rowIndex + 2) . ':P' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
            })->cell('Q' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('Q' . ($rowIndex + 2) . ':U' . ($rowIndex + 2))->cell('Q' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
            })->cell('Q' . ($rowIndex + 1), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->mergeCells('V' . ($rowIndex + 2) . ':Z' . ($rowIndex + 2))->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV bảo trì TIN-PNC');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AA' . ($rowIndex + 2) . ':AE' . ($rowIndex + 2))->cell('AA' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AA' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AA' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV bảo trì INDO');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AF' . ($rowIndex + 2) . ':AJ' . ($rowIndex + 2))->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV thu cước');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AK' . ($rowIndex + 2) . ':AO' . ($rowIndex + 2))->cell('AK' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AK' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AK' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('Nhân viên giao dịch');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AP' . ($rowIndex + 2) . ':AT' . ($rowIndex + 2))->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AU' . ($rowIndex + 2) . ':AY' . ($rowIndex + 2))->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AZ' . ($rowIndex + 2) . ':BD' . ($rowIndex + 2))->cell('AZ' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AZ' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            })->cell('AZ' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('Nhân viên triển khai Swap');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->setWidth('B', 20)->cell('B' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('C', 20)->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('D', 20)->cell('D' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('E', 20)->cell('E' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('F', 20)->cell('F' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('G', 20)->cell('G' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('H', 20)->cell('H' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('I', 20)->cell('I' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('J', 20)->cell('J' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('K', 20)->cell('K' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('L', 20)->cell('L' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('M', 20)->cell('M' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('N', 20)->cell('N' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('O', 20)->cell('O' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('P', 20)->cell('P' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Q', 20)->cell('Q' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('R', 20)->cell('R' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('S', 20)->cell('S' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('T', 20)->cell('T' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('U', 20)->cell('U' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('V', 20)->cell('V' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('W', 20)->cell('W' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('X', 20)->cell('X' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Y', 20)->cell('Y' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Z', 20)->cell('Z' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AA', 20)->cell('AA' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AB', 20)->cell('AB' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AC', 20)->cell('AC' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AD', 20)->cell('AD' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AE', 20)->cell('AE' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AF', 20)->cell('AF' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AG', 20)->cell('AG' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AH', 20)->cell('AH' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AI', 20)->cell('AI' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AJ', 20)->cell('AJ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AK', 20)->cell('AK' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AL', 20)->cell('AL' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AM', 20)->cell('AM' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AN', 20)->cell('AN' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AO', 20)->cell('AO' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AP', 20)->cell('AP' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AQ', 20)->cell('AQ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AR', 20)->cell('AR' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AS', 20)->cell('AS' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AT', 20)->cell('AT' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AU', 20)->cell('AU' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AV', 20)->cell('AV' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AW', 20)->cell('AW' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AX', 20)->cell('AX' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AY', 20)->cell('AY' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AZ', 20)->cell('AZ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BA', 20)->cell('BA' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BB', 20)->cell('BB' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BC', 20)->cell('BC' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BD', 20)->cell('BD' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 4;
        $NVKD_IBB_TQ_CSAT1 = $NVKD_IBB_TQ_CSAT2 = $NVKD_IBB_TQ_CSAT12 = $NVKD_IBB_TQ_CUS_CSAT = $NVKD_IBB_TQ_CSAT = $NVTK_IBB_TQ_CSAT1 = $NVTK_IBB_TQ_CSAT2 = $NVTK_IBB_TQ_CSAT12 = $NVTK_IBB_TQ_CUS_CSAT = $NVTK_IBB_TQ_CSAT = $NVKD_TS_TQ_CSAT1 = $NVKD_TS_TQ_CSAT2 = $NVKD_TS_TQ_CSAT12 = $NVKD_TS_TQ_CUS_CSAT = $NVKD_TS_TQ_CSAT = $NVTK_TS_TQ_CSAT1 = $NVTK_TS_TQ_CSAT2 = $NVTK_TS_TQ_CSAT12 = $NVTK_TS_TQ_CUS_CSAT = $NVTK_TS_TQ_CSAT = $NVBT_TIN_TQ_CSAT1 = $NVBT_TIN_TQ_CSAT2 = $NVBT_TIN_TQ_CSAT12 = $NVBT_TIN_TQ_CUS_CSAT = $NVBT_TIN_TQ_CSAT = $NVBT_INDO_TQ_CSAT1 = $NVBT_INDO_TQ_CSAT2 = $NVBT_INDO_TQ_CSAT12 = $NVBT_INDO_TQ_CUS_CSAT = $NVBT_INDO_TQ_CSAT = $NVTC_TQ_CSAT1 = $NVTC_TQ_CSAT2 = $NVTC_TQ_CSAT12 = $NVTC_TQ_CUS_CSAT = $NVTC_TQ_CSAT = $NVGDTQ_TQ_CSAT1 = $NVGDTQ_TQ_CSAT2 = $NVGDTQ_TQ_CSAT12 = $NVGDTQ_TQ_CUS_CSAT = $NVGDTQ_TQ_CSAT = $NVKDSS_TQ_CSAT1 = $NVKDSS_TQ_CSAT2 = $NVKDSS_TQ_CSAT12 = $NVKDSS_TQ_CUS_CSAT = $NVKDSS_TQ_CSAT = $NVTKSS_TQ_CSAT1 = $NVTKSS_TQ_CSAT2 = $NVTKSS_TQ_CSAT12 = $NVTKSS_TQ_CUS_CSAT = $NVTKSS_TQ_CSAT = $NVBTSSW_TQ_CSAT1 = $NVBTSSW_TQ_CSAT2 = $NVBTSSW_TQ_CSAT12 = $NVBTSSW_TQ_CUS_CSAT = $NVBTSSW_TQ_CSAT = 0;
        foreach ($detailCSAT['surveyCSAT12'] as $key => $value) {
            $NVKD_IBB_TQ_CSAT1 += $value->NVKD_IBB_CSAT_1;
            $NVKD_IBB_TQ_CSAT2 += $value->NVKD_IBB_CSAT_2;
            $NVKD_IBB_TQ_CSAT12 += $value->NVKD_IBB_CSAT_12;
            $NVKD_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVKD_CUS_CSAT;
            $NVKD_IBB_TQ_CSAT += $value->TOTAL_IBB_NVKD_CSAT;

            $NVTK_IBB_TQ_CSAT1 += $value->NVTK_IBB_CSAT_1;
            $NVTK_IBB_TQ_CSAT2 += $value->NVTK_IBB_CSAT_2;
            $NVTK_IBB_TQ_CSAT12 += $value->NVTK_IBB_CSAT_12;
            $NVTK_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVTK_CUS_CSAT;
            $NVTK_IBB_TQ_CSAT += $value->TOTAL_IBB_NVTK_CSAT;

            $NVKD_TS_TQ_CSAT1 += $value->NVKD_TS_CSAT_1;
            $NVKD_TS_TQ_CSAT2 += $value->NVKD_TS_CSAT_2;
            $NVKD_TS_TQ_CSAT12 += $value->NVKD_TS_CSAT_12;
            $NVKD_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVKD_CUS_CSAT;
            $NVKD_TS_TQ_CSAT += $value->TOTAL_TS_NVKD_CSAT;

            $NVTK_TS_TQ_CSAT1 += $value->NVTK_TS_CSAT_1;
            $NVTK_TS_TQ_CSAT2 += $value->NVTK_TS_CSAT_2;
            $NVTK_TS_TQ_CSAT12 += $value->NVTK_TS_CSAT_12;
            $NVTK_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVTK_CUS_CSAT;
            $NVTK_TS_TQ_CSAT += $value->TOTAL_TS_NVTK_CSAT;

            $NVBT_TIN_TQ_CSAT1 += $value->NVBT_TIN_CSAT_1;
            $NVBT_TIN_TQ_CSAT2 += $value->NVBT_TIN_CSAT_2;
            $NVBT_TIN_TQ_CSAT12 += $value->NVBT_TIN_CSAT_12;
            $NVBT_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_NVBT_CUS_CSAT;
            $NVBT_TIN_TQ_CSAT += $value->TOTAL_TIN_NVBT_CSAT;

            $NVBT_INDO_TQ_CSAT1 += $value->NVBT_INDO_CSAT_1;
            $NVBT_INDO_TQ_CSAT2 += $value->NVBT_INDO_CSAT_2;
            $NVBT_INDO_TQ_CSAT12 += $value->NVBT_INDO_CSAT_12;
            $NVBT_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_NVBT_CUS_CSAT;
            $NVBT_INDO_TQ_CSAT += $value->TOTAL_INDO_NVBT_CSAT;

            $NVTC_TQ_CSAT1 += $value->NVThuCuoc_CSAT_1;
            $NVTC_TQ_CSAT2 += $value->NVThuCuoc_CSAT_2;
            $NVTC_TQ_CSAT12 += $value->NVThuCuoc_CSAT_12;
            $NVTC_TQ_CUS_CSAT += $value->TOTAL_NVThuCuoc_CUS_CSAT;
            $NVTC_TQ_CSAT += $value->TOTAL_NVThuCuoc_CSAT;

            $NVGDTQ_TQ_CSAT1 += $value->NVGDTQ_CSAT_1;
            $NVGDTQ_TQ_CSAT2 += $value->NVGDTQ_CSAT_2;
            $NVGDTQ_TQ_CSAT12 += $value->NVGDTQ_CSAT_12;
            $NVGDTQ_TQ_CUS_CSAT += $value->TOTAL_NVGDTQ_CUS_CSAT;
            $NVGDTQ_TQ_CSAT += $value->TOTAL_NVGDTQ_CSAT;

            $NVKDSS_TQ_CSAT1 += $value->NVKD_SS_CSAT_1;
            $NVKDSS_TQ_CSAT2 += $value->NVKD_SS_CSAT_2;
            $NVKDSS_TQ_CSAT12 += $value->NVKD_SS_CSAT_12;
            $NVKDSS_TQ_CUS_CSAT += $value->TOTAL_SS_NVKD_CUS_CSAT;
            $NVKDSS_TQ_CSAT += $value->TOTAL_SS_NVKD_CSAT;

            $NVTKSS_TQ_CSAT1 += $value->NVTK_SS_CSAT_1;
            $NVTKSS_TQ_CSAT2 += $value->NVTK_SS_CSAT_2;
            $NVTKSS_TQ_CSAT12 += $value->NVTK_SS_CSAT_12;
            $NVTKSS_TQ_CUS_CSAT += $value->TOTAL_SS_NVTK_CUS_CSAT;
            $NVTKSS_TQ_CSAT += $value->TOTAL_SS_NVTK_CSAT;

            $NVBTSSW_TQ_CSAT1 += $value->NVBT_SSW_CSAT_1;
            $NVBTSSW_TQ_CSAT2 += $value->NVBT_SSW_CSAT_2;
            $NVBTSSW_TQ_CSAT12 += $value->NVBT_SSW_CSAT_12;
            $NVBTSSW_TQ_CUS_CSAT += $value->TOTAL_SSW_NVBT_CUS_CSAT;
            $NVBTSSW_TQ_CSAT += $value->TOTAL_SSW_NVBT_CSAT;

            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->section_sub_parent_desc);
                $this->setTitleBodyTable($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_IBB_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_IBB_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_IBB_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                $rateNotSastisfied = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_IBB_CSAT_12 / $value->TOTAL_IBB_NVKD_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVKD_CSAT / $value->TOTAL_IBB_NVKD_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_IBB_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_IBB_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('I' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_IBB_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {


                $rateNotSastisfied = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_IBB_CSAT_12 / $value->TOTAL_IBB_NVTK_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('K' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVTK_CSAT / $value->TOTAL_IBB_NVTK_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('L' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_TS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('M' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_TS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('N' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_TS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('O' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_TS_CSAT_12 / $value->TOTAL_TS_NVKD_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('P' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVKD_CSAT / $value->TOTAL_TS_NVKD_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('Q' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_TS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('R' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_TS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('S' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_TS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('T' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_TS_CSAT_12 / $value->TOTAL_TS_NVTK_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('U' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVTK_CSAT / $value->TOTAL_TS_NVTK_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('V' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_TIN_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('W' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_TIN_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('X' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_TIN_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('Y' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_TIN_CSAT_12 / $value->TOTAL_TIN_NVBT_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('Z' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_NVBT_CSAT / $value->TOTAL_TIN_NVBT_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('AA' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_INDO_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AB' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_INDO_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AC' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_INDO_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AD' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_INDO_CSAT_12 / $value->TOTAL_INDO_NVBT_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AE' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_NVBT_CSAT / $value->TOTAL_INDO_NVBT_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AF' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVThuCuoc_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AG' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVThuCuoc_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AH' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVThuCuoc_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AI' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->NVThuCuoc_CSAT_12 / $value->TOTAL_NVThuCuoc_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AJ' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->TOTAL_NVThuCuoc_CSAT / $value->TOTAL_NVThuCuoc_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AK' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVGDTQ_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AL' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVGDTQ_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AM' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVGDTQ_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AN' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->NVGDTQ_CSAT_12 / $value->TOTAL_NVGDTQ_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AO' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->TOTAL_NVGDTQ_CSAT / $value->TOTAL_NVGDTQ_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AP' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_SS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AQ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_SS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AR' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVKD_SS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AS' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_SS_CSAT_12 / $value->TOTAL_SS_NVKD_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AT' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVKD_CSAT / $value->TOTAL_SS_NVKD_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AU' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_SS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AV' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_SS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AW' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVTK_SS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AX' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_SS_CSAT_12 / $value->TOTAL_SS_NVTK_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AY' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVTK_CSAT / $value->TOTAL_SS_NVTK_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AZ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_SSW_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BA' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_SSW_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BB' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_SSW_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BC' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_SSW_CSAT_12 / $value->TOTAL_SSW_NVBT_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BD' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_NVBT_CSAT / $value->TOTAL_SSW_NVBT_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart++;
        }
        $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
            $cell->setValue('Toàn Quốc');
            $this->setTitleBodyTable($cell);
            $this->setTitleMainRow($cell);
        })->cell('B' . $rowStart, function ($cell) use ($NVKD_IBB_TQ_CSAT1) {
            $cell->setValue($NVKD_IBB_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('C' . $rowStart, function ($cell) use ($NVKD_IBB_TQ_CSAT2) {
            $cell->setValue($NVKD_IBB_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('D' . $rowStart, function ($cell) use ($NVKD_IBB_TQ_CSAT12) {
            $cell->setValue($NVKD_IBB_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('E' . $rowStart, function ($cell) use ($NVKD_IBB_TQ_CUS_CSAT, $NVKD_IBB_TQ_CSAT12) {

            $rateNotSastisfied = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT12 / $NVKD_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('F' . $rowStart, function ($cell) use ($NVKD_IBB_TQ_CUS_CSAT, $NVKD_IBB_TQ_CSAT) {
            $csatAverage = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT / $NVKD_IBB_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('G' . $rowStart, function ($cell) use ($NVTK_IBB_TQ_CSAT1) {
            $cell->setValue($NVTK_IBB_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('H' . $rowStart, function ($cell) use ($NVTK_IBB_TQ_CSAT2) {
            $cell->setValue($NVTK_IBB_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('I' . $rowStart, function ($cell) use ($NVTK_IBB_TQ_CSAT12) {
            $cell->setValue($NVTK_IBB_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('J' . $rowStart, function ($cell) use ($value, $NVTK_IBB_TQ_CUS_CSAT, $NVTK_IBB_TQ_CSAT12) {


            $rateNotSastisfied = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT12 / $NVTK_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('K' . $rowStart, function ($cell) use ($NVTK_IBB_TQ_CUS_CSAT, $NVTK_IBB_TQ_CSAT) {
            $csatAverage = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT / $NVTK_IBB_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })
            ->cell('L' . $rowStart, function ($cell) use ($NVKD_TS_TQ_CSAT1) {
                $cell->setValue($NVKD_TS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('M' . $rowStart, function ($cell) use ($NVKD_TS_TQ_CSAT2) {
                $cell->setValue($NVKD_TS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('N' . $rowStart, function ($cell) use ($NVKD_TS_TQ_CSAT12) {
                $cell->setValue($NVKD_TS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('O' . $rowStart, function ($cell) use ($NVKD_TS_TQ_CUS_CSAT, $NVKD_TS_TQ_CSAT12) {

                $rateNotSastisfied = (($NVKD_TS_TQ_CUS_CSAT) != 0) ? round(($NVKD_TS_TQ_CSAT12 / $NVKD_TS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('P' . $rowStart, function ($cell) use ($NVKD_TS_TQ_CUS_CSAT, $NVKD_TS_TQ_CSAT) {
                $csatAverage = (($NVKD_TS_TQ_CUS_CSAT) != 0) ? round(($NVKD_TS_TQ_CSAT / $NVKD_TS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('Q' . $rowStart, function ($cell) use ($NVTK_TS_TQ_CSAT1) {
                $cell->setValue($NVTK_TS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('R' . $rowStart, function ($cell) use ($NVTK_TS_TQ_CSAT2) {
                $cell->setValue($NVTK_TS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('S' . $rowStart, function ($cell) use ($NVTK_TS_TQ_CSAT12) {
                $cell->setValue($NVTK_TS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('T' . $rowStart, function ($cell) use ($NVTK_TS_TQ_CUS_CSAT, $NVTK_TS_TQ_CSAT12) {

                $rateNotSastisfied = (($NVTK_TS_TQ_CUS_CSAT) != 0) ? round(($NVTK_TS_TQ_CSAT12 / $NVTK_TS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('U' . $rowStart, function ($cell) use ($NVTK_TS_TQ_CUS_CSAT, $NVTK_TS_TQ_CSAT) {
                $csatAverage = (($NVTK_TS_TQ_CUS_CSAT) != 0) ? round(($NVTK_TS_TQ_CSAT / $NVTK_TS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('V' . $rowStart, function ($cell) use ($NVBT_TIN_TQ_CSAT1) {
                $cell->setValue($NVBT_TIN_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('W' . $rowStart, function ($cell) use ($NVBT_TIN_TQ_CSAT2) {
                $cell->setValue($NVBT_TIN_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('X' . $rowStart, function ($cell) use ($NVBT_TIN_TQ_CSAT12) {
                $cell->setValue($NVBT_TIN_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('Y' . $rowStart, function ($cell) use ($NVBT_TIN_TQ_CUS_CSAT, $NVBT_TIN_TQ_CSAT12) {

                $rateNotSastisfied = (($NVBT_TIN_TQ_CUS_CSAT) != 0) ? round(($NVBT_TIN_TQ_CSAT12 / $NVBT_TIN_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('Z' . $rowStart, function ($cell) use ($NVBT_TIN_TQ_CUS_CSAT, $NVBT_TIN_TQ_CSAT) {
                $csatAverage = (($NVBT_TIN_TQ_CUS_CSAT) != 0) ? round(($NVBT_TIN_TQ_CSAT / $NVBT_TIN_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AA' . $rowStart, function ($cell) use ($NVBT_INDO_TQ_CSAT1) {
                $cell->setValue($NVBT_INDO_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AB' . $rowStart, function ($cell) use ($NVBT_INDO_TQ_CSAT2) {
                $cell->setValue($NVBT_INDO_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AC' . $rowStart, function ($cell) use ($NVBT_INDO_TQ_CSAT12) {
                $cell->setValue($NVBT_INDO_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AD' . $rowStart, function ($cell) use ($NVBT_INDO_TQ_CUS_CSAT, $NVBT_INDO_TQ_CSAT12) {

                $rateNotSastisfied = (($NVBT_INDO_TQ_CUS_CSAT) != 0) ? round(($NVBT_INDO_TQ_CSAT12 / $NVBT_INDO_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AE' . $rowStart, function ($cell) use ($NVBT_INDO_TQ_CUS_CSAT, $NVBT_INDO_TQ_CSAT) {
                $csatAverage = (($NVBT_INDO_TQ_CUS_CSAT) != 0) ? round(($NVBT_INDO_TQ_CSAT / $NVBT_INDO_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AF' . $rowStart, function ($cell) use ($NVTC_TQ_CSAT1) {
                $cell->setValue($NVTC_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AG' . $rowStart, function ($cell) use ($NVTC_TQ_CSAT2) {
                $cell->setValue($NVTC_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AH' . $rowStart, function ($cell) use ($NVTC_TQ_CSAT12) {
                $cell->setValue($NVTC_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AI' . $rowStart, function ($cell) use ($NVTC_TQ_CUS_CSAT, $NVTC_TQ_CSAT12) {

                $rateNotSastisfied = (($NVTC_TQ_CUS_CSAT) != 0) ? round(($NVTC_TQ_CSAT12 / $NVTC_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AJ' . $rowStart, function ($cell) use ($NVTC_TQ_CUS_CSAT, $NVTC_TQ_CSAT) {
                $csatAverage = (($NVTC_TQ_CUS_CSAT) != 0) ? round(($NVTC_TQ_CSAT / $NVTC_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AK' . $rowStart, function ($cell) use ($NVGDTQ_TQ_CSAT1) {
                $cell->setValue($NVGDTQ_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AL' . $rowStart, function ($cell) use ($NVGDTQ_TQ_CSAT2) {
                $cell->setValue($NVGDTQ_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AM' . $rowStart, function ($cell) use ($NVGDTQ_TQ_CSAT12) {
                $cell->setValue($NVGDTQ_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AN' . $rowStart, function ($cell) use ($NVGDTQ_TQ_CUS_CSAT, $NVGDTQ_TQ_CSAT12) {

                $rateNotSastisfied = (($NVGDTQ_TQ_CUS_CSAT) != 0) ? round(($NVGDTQ_TQ_CSAT12 / $NVGDTQ_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AO' . $rowStart, function ($cell) use ($NVGDTQ_TQ_CUS_CSAT, $NVGDTQ_TQ_CSAT) {
                $csatAverage = (($NVGDTQ_TQ_CUS_CSAT) != 0) ? round(($NVGDTQ_TQ_CSAT / $NVGDTQ_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AP' . $rowStart, function ($cell) use ($NVKDSS_TQ_CSAT1) {
                $cell->setValue($NVKDSS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AQ' . $rowStart, function ($cell) use ($NVKDSS_TQ_CSAT2) {
                $cell->setValue($NVKDSS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AR' . $rowStart, function ($cell) use ($NVKDSS_TQ_CSAT12) {
                $cell->setValue($NVKDSS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AS' . $rowStart, function ($cell) use ($NVKDSS_TQ_CUS_CSAT, $NVKDSS_TQ_CSAT12) {

                $rateNotSastisfied = (($NVKDSS_TQ_CUS_CSAT) != 0) ? round(($NVKDSS_TQ_CSAT12 / $NVKDSS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AT' . $rowStart, function ($cell) use ($NVKDSS_TQ_CUS_CSAT, $NVKDSS_TQ_CSAT) {
                $csatAverage = (($NVKDSS_TQ_CUS_CSAT) != 0) ? round(($NVKDSS_TQ_CSAT / $NVKDSS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AU' . $rowStart, function ($cell) use ($NVTKSS_TQ_CSAT1) {
                $cell->setValue($NVTKSS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AV' . $rowStart, function ($cell) use ($NVTKSS_TQ_CSAT2) {
                $cell->setValue($NVTKSS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AW' . $rowStart, function ($cell) use ($NVTKSS_TQ_CSAT12) {
                $cell->setValue($NVTKSS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AX' . $rowStart, function ($cell) use ($NVTKSS_TQ_CUS_CSAT, $NVTKSS_TQ_CSAT12) {

                $rateNotSastisfied = (($NVTKSS_TQ_CUS_CSAT) != 0) ? round(($NVTKSS_TQ_CSAT12 / $NVTKSS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AY' . $rowStart, function ($cell) use ($NVTKSS_TQ_CUS_CSAT, $NVTKSS_TQ_CSAT) {
                $csatAverage = (($NVTKSS_TQ_CUS_CSAT) != 0) ? round(($NVTKSS_TQ_CSAT / $NVTKSS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AZ' . $rowStart, function ($cell) use ($NVBTSSW_TQ_CSAT1) {
                $cell->setValue($NVBTSSW_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BA' . $rowStart, function ($cell) use ($NVBTSSW_TQ_CSAT2) {
                $cell->setValue($NVBTSSW_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BB' . $rowStart, function ($cell) use ($NVBTSSW_TQ_CSAT12) {
                $cell->setValue($NVBTSSW_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BC' . $rowStart, function ($cell) use ($NVBTSSW_TQ_CUS_CSAT, $NVBTSSW_TQ_CSAT12) {

                $rateNotSastisfied = (($NVBTSSW_TQ_CUS_CSAT) != 0) ? round(($NVBTSSW_TQ_CSAT12 / $NVBTSSW_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BD' . $rowStart, function ($cell) use ($NVBTSSW_TQ_CUS_CSAT, $NVBTSSW_TQ_CSAT) {
                $csatAverage = (($NVBTSSW_TQ_CUS_CSAT) != 0) ? round(($NVBTSSW_TQ_CSAT / $NVBTSSW_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            });
        return $rowStart;
    }

    //Tạo bảng thống kê csat12 dịch vụ
    public function createDetailServiceCsat12($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 68)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('3.CSAT dịch vụ');
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':A' . ($rowIndex + 3))->setWidth('A', 40)->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Vùng');
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai DirectSales');
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('L' . ($rowIndex + 1) . ':U' . ($rowIndex + 1))->cell('L' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai TLS');
            $this->setTitleHeaderTable($cell);
        })->cell('L' . ($rowIndex + 1) . ':U' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('V' . ($rowIndex + 1) . ':AE' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau bảo trì TIN-PNC');
            $this->setTitleHeaderTable($cell);
        })->cell('V' . ($rowIndex + 1) . ':AE' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AF' . ($rowIndex + 1) . ':AO' . ($rowIndex + 1))->cell('AF' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau bảo trì INDO');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('AF' . ($rowIndex + 1) . ':AO' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AP' . ($rowIndex + 1) . ':AY' . ($rowIndex + 1))->cell('AP' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau thu cước');
            $this->setTitleHeaderTable($cell);
        })->cell('AP' . ($rowIndex + 1) . ':AY' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AZ' . ($rowIndex + 1) . ':BD' . ($rowIndex + 1))->cell('AZ' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau giao dịch tại quầy');
            $this->setTitleHeaderTable($cell);
        })->cell('AZ' . ($rowIndex + 1) . ':BD' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('BE' . ($rowIndex + 1) . ':BN' . ($rowIndex + 1))->cell('BE' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai sale tại quầy');
            $this->setTitleHeaderTable($cell);
        })->cell('BE' . ($rowIndex + 1) . ':BN' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('BO' . ($rowIndex + 1) . ':BX' . ($rowIndex + 1))->cell('BO' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Sau triển khai Swap');
            $this->setTitleHeaderTable($cell);
        })->cell('BO' . ($rowIndex + 1) . ':BX' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('BY' . ($rowIndex + 1) . ':CH' . ($rowIndex + 1))->cell('BY' . ($rowIndex + 1), function ($cell) {
            $cell->setValue('Tổng cộng các trường hơp khách hàng không hài lòng');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowIndex + 1) . ':Y' . ($rowIndex + 1))->cell('V' . ($rowIndex + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('BY' . ($rowIndex + 1) . ':CH' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('B' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('G' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('P' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('L' . ($rowIndex + 2) . ':P' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
            })->cell('Q' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('Q' . ($rowIndex + 2) . ':U' . ($rowIndex + 2))->cell('Q' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
            })->cell('Q' . ($rowIndex + 1), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })
            ->mergeCells('V' . ($rowIndex + 2) . ':Z' . ($rowIndex + 2))->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('V' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('AA' . ($rowIndex + 2) . ':AE' . ($rowIndex + 2))->cell('AA' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AA' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->mergeCells('AF' . ($rowIndex + 2) . ':AJ' . ($rowIndex + 2))->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('AF' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('AK' . ($rowIndex + 2) . ':AO' . ($rowIndex + 2))->cell('AK' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AK' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->mergeCells('AP' . ($rowIndex + 2) . ':AT' . ($rowIndex + 2))->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('AP' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('AU' . ($rowIndex + 2) . ':AY' . ($rowIndex + 2))->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->cell('AU' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('AZ' . ($rowIndex + 2) . ':BD' . ($rowIndex + 2))->cell('AZ' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AZ' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('Chất lượng DV');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->mergeCells('BE' . ($rowIndex + 2) . ':BI' . ($rowIndex + 2))->cell('BE' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('BE' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('BE' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('BJ' . ($rowIndex + 2) . ':BN' . ($rowIndex + 2))->cell('BJ' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('BJ' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('BO' . ($rowIndex + 2) . ':BS' . ($rowIndex + 2))->cell('BO' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('BO' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('BO' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('BT' . ($rowIndex + 2) . ':BX' . ($rowIndex + 2))->cell('BT' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('BT' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('BY' . ($rowIndex + 2) . ':CC' . ($rowIndex + 2))->cell('BY' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('BY' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('BY' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('CD' . ($rowIndex + 2) . ':CH' . ($rowIndex + 2))->cell('CD' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('CD' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CLDV Truyền hình');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->setWidth('B', 20)->cell('B' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('C', 20)->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('D', 20)->cell('D' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('E', 20)->cell('E' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('F', 20)->cell('F' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('G', 20)->cell('G' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('H', 20)->cell('H' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('I', 20)->cell('I' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('J', 20)->cell('J' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('K', 20)->cell('K' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('L', 20)->cell('L' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('M', 20)->cell('M' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('N', 20)->cell('N' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('O', 20)->cell('O' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('P', 20)->cell('P' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Q', 20)->cell('Q' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('R', 20)->cell('R' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('S', 20)->cell('S' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('T', 20)->cell('T' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('U', 20)->cell('U' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('V', 20)->cell('V' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('W', 20)->cell('W' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('X', 20)->cell('X' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Y', 20)->cell('Y' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('Z', 20)->cell('Z' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AA', 20)->cell('AA' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AB', 20)->cell('AB' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AC', 20)->cell('AC' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AD', 20)->cell('AD' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AE', 20)->cell('AE' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AF', 20)->cell('AF' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AG', 20)->cell('AG' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AH', 20)->cell('AH' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AI', 20)->cell('AI' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AJ', 20)->cell('AJ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AK', 20)->cell('AK' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AL', 20)->cell('AL' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AM', 20)->cell('AM' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AN', 20)->cell('AN' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AO', 20)->cell('AO' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AP', 20)->cell('AP' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AQ', 20)->cell('AQ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AR', 20)->cell('AR' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AS', 20)->cell('AS' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AT', 20)->cell('AT' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AU', 20)->cell('AU' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AV', 20)->cell('AV' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AW', 20)->cell('AW' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AX', 20)->cell('AX' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AY', 20)->cell('AY' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('AZ', 20)->cell('AZ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BA', 20)->cell('BA' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BB', 20)->cell('BB' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BC', 20)->cell('BC' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BD', 20)->cell('BD' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BE', 20)->cell('BE' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BF', 20)->cell('BF' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BG', 20)->cell('BG' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BH', 20)->cell('BH' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BI', 20)->cell('BI' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BJ', 20)->cell('BJ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BK', 20)->cell('BK' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BL', 20)->cell('BL' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BM', 20)->cell('BM' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BN', 20)->cell('BN' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BO', 20)->cell('BO' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BP', 20)->cell('BP' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BQ', 20)->cell('BQ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BR', 20)->cell('BR' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BS', 20)->cell('BS' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BT', 20)->cell('BT' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BU', 20)->cell('BU' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BV', 20)->cell('BV' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BW', 20)->cell('BW' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BX', 20)->cell('BX' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BY', 20)->cell('BY' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('BZ', 20)->cell('BZ' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CA', 20)->cell('CA' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CB', 20)->cell('CB' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CC', 20)->cell('CC' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CD', 20)->cell('CD' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CE', 20)->cell('CE' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CF', 20)->cell('CF' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tổng CSAT 1,2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CG', 20)->cell('CG' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('Tỉ lệ không hài lòng(%)');
                $this->setTitleHeaderTable($cell);
            })->setWidth('CH', 20)->cell('CH' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT Trung bình');
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 4;
        $Internet_IBB_TQ_CSAT1 = $Internet_IBB_TQ_CSAT2 = $Internet_IBB_TQ_CSAT12 = $Internet_IBB_TQ_CUS_CSAT = $Internet_IBB_TQ_CSAT = $TV_IBB_TQ_CSAT1 = $TV_IBB_TQ_CSAT2 = $TV_IBB_TQ_CSAT12 = $TV_IBB_TQ_CUS_CSAT = $TV_IBB_TQ_CSAT = $Internet_TS_TQ_CSAT1 = $Internet_TS_TQ_CSAT2 = $Internet_TS_TQ_CSAT12 = $Internet_TS_TQ_CUS_CSAT = $Internet_TS_TQ_CSAT = $TV_TS_TQ_CSAT1 = $TV_TS_TQ_CSAT2 = $TV_TS_TQ_CSAT12 = $TV_TS_TQ_CUS_CSAT = $TV_TS_TQ_CSAT = $Internet_TIN_TQ_CSAT1 = $Internet_TIN_TQ_CSAT2 = $Internet_TIN_TQ_CSAT12 = $Internet_TIN_TQ_CUS_CSAT = $Internet_TIN_TQ_CSAT = $TV_TIN_TQ_CSAT1 = $TV_TIN_TQ_CSAT2 = $TV_TIN_TQ_CSAT12 = $TV_TIN_TQ_CUS_CSAT = $TV_TIN_TQ_CSAT = $Internet_INDO_TQ_CSAT1 = $Internet_INDO_TQ_CSAT2 = $Internet_INDO_TQ_CSAT12 = $Internet_INDO_TQ_CUS_CSAT = $Internet_INDO_TQ_CSAT = $TV_INDO_TQ_CSAT1 = $TV_INDO_TQ_CSAT2 = $TV_INDO_TQ_CSAT12 = $TV_INDO_TQ_CUS_CSAT = $TV_INDO_TQ_CSAT = $Internet_CUS_TQ_CSAT1 = $Internet_CUS_TQ_CSAT2 = $Internet_CUS_TQ_CSAT12 = $Internet_CUS_TQ_CUS_CSAT = $Internet_CUS_TQ_CSAT = $TV_CUS_TQ_CSAT1 = $TV_CUS_TQ_CSAT2 = $TV_CUS_TQ_CSAT12 = $TV_CUS_TQ_CUS_CSAT = $TV_CUS_TQ_CSAT = $Internet_KHL_TQ_CSAT1 = $Internet_KHL_TQ_CSAT2 = $Internet_KHL_TQ_CSAT12 = $Internet_KHL_TQ_CUS_CSAT = $Internet_KHL_TQ_CSAT = $TV_KHL_TQ_CSAT1 = $TV_KHL_TQ_CSAT2 = $TV_KHL_TQ_CSAT12 = $TV_KHL_TQ_CUS_CSAT = $TV_KHL_TQ_CSAT = $GDTQ_TQ_CSAT1 = $GDTQ_TQ_CSAT2 = $GDTQ_TQ_CSAT12 = $GDTQ_TQ_CUS_CSAT = $GDTQ_TQ_CSAT = $Internet_SS_TQ_CSAT1 = $Internet_SS_TQ_CSAT2 = $Internet_SS_TQ_CSAT12 = $Internet_SS_TQ_CUS_CSAT = $Internet_SS_TQ_CSAT = $TV_SS_TQ_CSAT1 = $TV_SS_TQ_CSAT2 = $TV_SS_TQ_CSAT12 = $TV_SS_TQ_CUS_CSAT = $TV_SS_TQ_CSAT = $Internet_SSW_TQ_CSAT1 = $Internet_SSW_TQ_CSAT2 = $Internet_SSW_TQ_CSAT12 = $Internet_SSW_TQ_CUS_CSAT = $Internet_SSW_TQ_CSAT = $TV_SSW_TQ_CSAT1 = $TV_SSW_TQ_CSAT2 = $TV_SSW_TQ_CSAT12 = $TV_SSW_TQ_CUS_CSAT = $TV_SSW_TQ_CSAT = 0;
        foreach ($detailCSAT['surveyCSATService12'] as $key => $value) {
            $Internet_IBB_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1;
            $Internet_IBB_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2;
            $Internet_IBB_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12;
            $Internet_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT;
            $Internet_IBB_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT;

            $TV_IBB_TQ_CSAT1 += $value->TV_IBB_CSAT_1;
            $TV_IBB_TQ_CSAT2 += $value->TV_IBB_CSAT_2;
            $TV_IBB_TQ_CSAT12 += $value->TV_IBB_CSAT_12;
            $TV_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT;
            $TV_IBB_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT;

            $Internet_TS_TQ_CSAT1 += $value->INTERNET_TS_CSAT_1;
            $Internet_TS_TQ_CSAT2 += $value->INTERNET_TS_CSAT_2;
            $Internet_TS_TQ_CSAT12 += $value->INTERNET_TS_CSAT_12;
            $Internet_TS_TQ_CUS_CSAT += $value->TOTAL_TS_INTERNET_CUS_CSAT;
            $Internet_TS_TQ_CSAT += $value->TOTAL_TS_INTERNET_CSAT;

            $TV_TS_TQ_CSAT1 += $value->TV_TS_CSAT_1;
            $TV_TS_TQ_CSAT2 += $value->TV_TS_CSAT_2;
            $TV_TS_TQ_CSAT12 += $value->TV_TS_CSAT_12;
            $TV_TS_TQ_CUS_CSAT += $value->TOTAL_TS_TV_CUS_CSAT;
            $TV_TS_TQ_CSAT += $value->TOTAL_TS_TV_CSAT;

            $Internet_TIN_TQ_CSAT1 += $value->INTERNET_TIN_CSAT_1;
            $Internet_TIN_TQ_CSAT2 += $value->INTERNET_TIN_CSAT_2;
            $Internet_TIN_TQ_CSAT12 += $value->INTERNET_TIN_CSAT_12;
            $Internet_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_INTERNET_CUS_CSAT;
            $Internet_TIN_TQ_CSAT += $value->TOTAL_TIN_INTERNET_CSAT;

            $TV_TIN_TQ_CSAT1 += $value->TV_TIN_CSAT_1;
            $TV_TIN_TQ_CSAT2 += $value->TV_TIN_CSAT_2;
            $TV_TIN_TQ_CSAT12 += $value->TV_TIN_CSAT_12;
            $TV_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_TV_CUS_CSAT;
            $TV_TIN_TQ_CSAT += $value->TOTAL_TIN_TV_CSAT;

            $Internet_INDO_TQ_CSAT1 += $value->INTERNET_INDO_CSAT_1;
            $Internet_INDO_TQ_CSAT2 += $value->INTERNET_INDO_CSAT_2;
            $Internet_INDO_TQ_CSAT12 += $value->INTERNET_INDO_CSAT_12;
            $Internet_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_INTERNET_CUS_CSAT;
            $Internet_INDO_TQ_CSAT += $value->TOTAL_INDO_INTERNET_CSAT;

            $TV_INDO_TQ_CSAT1 += $value->TV_INDO_CSAT_1;
            $TV_INDO_TQ_CSAT2 += $value->TV_INDO_CSAT_2;
            $TV_INDO_TQ_CSAT12 += $value->TV_INDO_CSAT_12;
            $TV_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_TV_CUS_CSAT;
            $TV_INDO_TQ_CSAT += $value->TOTAL_INDO_TV_CSAT;

            $Internet_CUS_TQ_CSAT1 += $value->INTERNET_CUS_CSAT_1;
            $Internet_CUS_TQ_CSAT2 += $value->INTERNET_CUS_CSAT_2;
            $Internet_CUS_TQ_CSAT12 += $value->INTERNET_CUS_CSAT_12;
            $Internet_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_INTERNET_CUS_CSAT;
            $Internet_CUS_TQ_CSAT += $value->TOTAL_CUS_INTERNET_CSAT;

            $TV_CUS_TQ_CSAT1 += $value->TV_CUS_CSAT_1;
            $TV_CUS_TQ_CSAT2 += $value->TV_CUS_CSAT_2;
            $TV_CUS_TQ_CSAT12 += $value->TV_CUS_CSAT_12;
            $TV_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_TV_CUS_CSAT;
            $TV_CUS_TQ_CSAT += $value->TOTAL_CUS_TV_CSAT;

            $GDTQ_TQ_CSAT1 += $value->DGDichVu_Counter_CSAT_1;
            $GDTQ_TQ_CSAT2 += $value->DGDichVu_Counter_CSAT_2;
            $GDTQ_TQ_CSAT12 += $value->DGDichVu_Counter_CSAT_12;
            $GDTQ_TQ_CUS_CSAT += $value->TOTAL_DGDichVu_Counter_CUS_CSAT;
            $GDTQ_TQ_CSAT += $value->TOTAL_DGDichVu_Counter_CSAT;

            $Internet_SS_TQ_CSAT1 += $value->INTERNET_SS_CSAT_1;
            $Internet_SS_TQ_CSAT2 += $value->INTERNET_SS_CSAT_2;
            $Internet_SS_TQ_CSAT12 += $value->INTERNET_SS_CSAT_12;
            $Internet_SS_TQ_CUS_CSAT += $value->TOTAL_SS_INTERNET_CUS_CSAT;
            $Internet_SS_TQ_CSAT += $value->TOTAL_SS_INTERNET_CSAT;

            $TV_SS_TQ_CSAT1 += $value->TV_SS_CSAT_1;
            $TV_SS_TQ_CSAT2 += $value->TV_SS_CSAT_2;
            $TV_SS_TQ_CSAT12 += $value->TV_SS_CSAT_12;
            $TV_SS_TQ_CUS_CSAT += $value->TOTAL_SS_TV_CUS_CSAT;
            $TV_SS_TQ_CSAT += $value->TOTAL_SS_TV_CSAT;

            $Internet_SSW_TQ_CSAT1 += $value->INTERNET_SSW_CSAT_1;
            $Internet_SSW_TQ_CSAT2 += $value->INTERNET_SSW_CSAT_2;
            $Internet_SSW_TQ_CSAT12 += $value->INTERNET_SSW_CSAT_12;
            $Internet_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_INTERNET_CUS_CSAT;
            $Internet_SSW_TQ_CSAT += $value->TOTAL_SSW_INTERNET_CSAT;

            $TV_SSW_TQ_CSAT1 += $value->TV_SSW_CSAT_1;
            $TV_SSW_TQ_CSAT2 += $value->TV_SSW_CSAT_2;
            $TV_SSW_TQ_CSAT12 += $value->TV_SSW_CSAT_12;
            $TV_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_TV_CUS_CSAT;
            $TV_SSW_TQ_CSAT += $value->TOTAL_SSW_TV_CSAT;

            $Internet_KHL_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1 + $value->INTERNET_TS_CSAT_1 + $value->INTERNET_TIN_CSAT_1 + $value->INTERNET_INDO_CSAT_1 + $value->INTERNET_CUS_CSAT_1 + $value->INTERNET_SS_CSAT_1 + $value->INTERNET_SSW_CSAT_1;
            $Internet_KHL_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2 + $value->INTERNET_TS_CSAT_2 + $value->INTERNET_TIN_CSAT_2 + $value->INTERNET_INDO_CSAT_2 + $value->INTERNET_CUS_CSAT_2 + $value->INTERNET_SS_CSAT_2 + $value->INTERNET_SSW_CSAT_2;
            $Internet_KHL_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_CUS_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12;
            $Internet_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_CUS_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
            $Internet_KHL_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_CUS_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT;

            $TV_KHL_TQ_CSAT1 += $value->TV_IBB_CSAT_1 + $value->TV_TS_CSAT_1 + $value->TV_TIN_CSAT_1 + $value->TV_INDO_CSAT_1 + $value->TV_CUS_CSAT_1 + $value->TV_SS_CSAT_1 + $value->TV_SSW_CSAT_1;
            $TV_KHL_TQ_CSAT2 += $value->TV_IBB_CSAT_2 + $value->TV_TS_CSAT_2 + $value->TV_TIN_CSAT_2 + $value->TV_INDO_CSAT_2 + $value->TV_CUS_CSAT_2 + $value->TV_SS_CSAT_2 + $value->TV_SSW_CSAT_2;
            $TV_KHL_TQ_CSAT12 += $value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_CUS_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12;
            $TV_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_CUS_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
            $TV_KHL_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT + $value->TOTAL_CUS_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT;
            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->section_sub_parent_desc);
                $this->setTitleBodyTable($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_IBB_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_IBB_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_IBB_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                $rateNotSastisfied = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_IBB_CSAT_12 / $value->TOTAL_IBB_INTERNET_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT / $value->TOTAL_IBB_INTERNET_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TV_IBB_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TV_IBB_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('I' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->TV_IBB_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {


                $rateNotSastisfied = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TV_IBB_CSAT_12 / $value->TOTAL_IBB_TV_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('K' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_TV_CSAT / $value->TOTAL_IBB_TV_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('L' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('M' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('N' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('O' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TS_CSAT_12 / $value->TOTAL_TS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('P' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_INTERNET_CSAT / $value->TOTAL_TS_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('Q' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('R' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('S' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('T' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TV_TS_CSAT_12 / $value->TOTAL_TS_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('U' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_TV_CSAT / $value->TOTAL_TS_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('V' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TIN_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('W' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TIN_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('X' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_TIN_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('Y' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TIN_CSAT_12 / $value->TOTAL_TIN_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('Z' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_INTERNET_CSAT / $value->TOTAL_TIN_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('AA' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TIN_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AB' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TIN_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AC' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_TIN_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AD' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TV_TIN_CSAT_12 / $value->TOTAL_TIN_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AE' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_TV_CSAT / $value->TOTAL_TIN_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('AF' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_INDO_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AG' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_INDO_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AH' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_INDO_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AI' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_INDO_CSAT_12 / $value->TOTAL_INDO_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AJ' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_INTERNET_CSAT / $value->TOTAL_INDO_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('AK' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_INDO_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AL' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_INDO_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AM' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_INDO_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AN' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TV_INDO_CSAT_12 / $value->TOTAL_INDO_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AO' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_TV_CSAT / $value->TOTAL_INDO_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AP' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CUS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AQ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CUS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AR' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CUS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AS' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_CUS_CSAT_12 / $value->TOTAL_CUS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AT' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_INTERNET_CSAT / $value->TOTAL_CUS_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('AU' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_CUS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AV' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_CUS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AW' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_CUS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AX' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TV_CUS_CSAT_12 / $value->TOTAL_CUS_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AY' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_TV_CSAT / $value->TOTAL_CUS_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('AZ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->DGDichVu_Counter_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BA' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->DGDichVu_Counter_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BB' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->DGDichVu_Counter_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BC' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->DGDichVu_Counter_CSAT_12 / $value->TOTAL_DGDichVu_Counter_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BD' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->TOTAL_DGDichVu_Counter_CSAT / $value->TOTAL_DGDichVu_Counter_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BE' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BF' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BG' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BH' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SS_CSAT_12 / $value->TOTAL_SS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BI' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_INTERNET_CSAT / $value->TOTAL_SS_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BJ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SS_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BK' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SS_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BL' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SS_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BM' . $rowStart, function ($cell) use ($value, $detailCSAT) {


                    $rateNotSastisfied = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TV_SS_CSAT_12 / $value->TOTAL_SS_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BN' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_TV_CSAT / $value->TOTAL_SS_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BO' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SSW_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BP' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SSW_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BQ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SSW_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BR' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SSW_CSAT_12 / $value->TOTAL_SSW_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BS' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_INTERNET_CSAT / $value->TOTAL_SSW_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BT' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SSW_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BU' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SSW_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BV' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_SSW_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BW' . $rowStart, function ($cell) use ($value, $detailCSAT) {


                    $rateNotSastisfied = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TV_SSW_CSAT_12 / $value->TOTAL_SSW_TV_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BX' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_TV_CSAT / $value->TOTAL_SSW_TV_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BY' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_IBB_CSAT_1 + $value->INTERNET_TS_CSAT_1 + $value->INTERNET_TIN_CSAT_1 + $value->INTERNET_INDO_CSAT_1 + $value->INTERNET_SS_CSAT_1 + $value->INTERNET_SSW_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('BZ' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_IBB_CSAT_2 + $value->INTERNET_TS_CSAT_2 + $value->INTERNET_TIN_CSAT_2 + $value->INTERNET_INDO_CSAT_2 + $value->INTERNET_SS_CSAT_2 + $value->INTERNET_SSW_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CA' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CB' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    $sumTotal = $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
                    $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CC' . $rowStart, function ($cell) use ($value) {
                    $sumTotal = $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
                    $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT) / $sumTotal, 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CD' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_IBB_CSAT_1 + $value->TV_TS_CSAT_1 + $value->TV_TIN_CSAT_1 + $value->TV_INDO_CSAT_1 + $value->TV_SS_CSAT_1 + $value->TV_SSW_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CE' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_IBB_CSAT_2 + $value->TV_TS_CSAT_2 + $value->TV_TIN_CSAT_2 + $value->TV_INDO_CSAT_2 + $value->TV_SS_CSAT_2 + $value->TV_SSW_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CF' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CG' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $sumTotal = $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
                    $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('CH' . $rowStart, function ($cell) use ($value) {
                    $sumTotal = $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
                    $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT) / $sumTotal, 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart++;
        }
        $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
            $cell->setValue('Toàn Quốc');
            $this->setTitleBodyTable($cell);
            $this->setTitleMainRow($cell);
        })->cell('B' . $rowStart, function ($cell) use ($Internet_IBB_TQ_CSAT1) {
            $cell->setValue($Internet_IBB_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('C' . $rowStart, function ($cell) use ($Internet_IBB_TQ_CSAT2) {
            $cell->setValue($Internet_IBB_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('D' . $rowStart, function ($cell) use ($Internet_IBB_TQ_CSAT12) {
            $cell->setValue($Internet_IBB_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('E' . $rowStart, function ($cell) use ($Internet_IBB_TQ_CUS_CSAT, $Internet_IBB_TQ_CSAT12) {

            $rateNotSastisfied = (($Internet_IBB_TQ_CUS_CSAT) != 0) ? round(($Internet_IBB_TQ_CSAT12 / $Internet_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('F' . $rowStart, function ($cell) use ($Internet_IBB_TQ_CUS_CSAT, $Internet_IBB_TQ_CSAT) {
            $csatAverage = (($Internet_IBB_TQ_CUS_CSAT) != 0) ? round(($Internet_IBB_TQ_CSAT / $Internet_IBB_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('G' . $rowStart, function ($cell) use ($TV_IBB_TQ_CSAT1) {
            $cell->setValue($TV_IBB_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('H' . $rowStart, function ($cell) use ($TV_IBB_TQ_CSAT2) {
            $cell->setValue($TV_IBB_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('I' . $rowStart, function ($cell) use ($TV_IBB_TQ_CSAT12) {
            $cell->setValue($TV_IBB_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('J' . $rowStart, function ($cell) use ($TV_IBB_TQ_CUS_CSAT, $TV_IBB_TQ_CSAT12) {
            $this->setTitleMainRow($cell);

            $rateNotSastisfied = (($TV_IBB_TQ_CUS_CSAT) != 0) ? round(($TV_IBB_TQ_CSAT12 / $TV_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
        })->cell('K' . $rowStart, function ($cell) use ($TV_IBB_TQ_CUS_CSAT, $TV_IBB_TQ_CSAT) {
            $csatAverage = (($TV_IBB_TQ_CUS_CSAT) != 0) ? round(($TV_IBB_TQ_CSAT / $TV_IBB_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })
            ->cell('L' . $rowStart, function ($cell) use ($Internet_TS_TQ_CSAT1) {
                $cell->setValue($Internet_TS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('M' . $rowStart, function ($cell) use ($Internet_TS_TQ_CSAT2) {
                $cell->setValue($Internet_TS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('N' . $rowStart, function ($cell) use ($Internet_TS_TQ_CSAT12) {
                $cell->setValue($Internet_TS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('O' . $rowStart, function ($cell) use ($Internet_TS_TQ_CUS_CSAT, $Internet_TS_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($Internet_TS_TQ_CUS_CSAT) != 0) ? round(($Internet_TS_TQ_CSAT12 / $Internet_TS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('P' . $rowStart, function ($cell) use ($Internet_TS_TQ_CUS_CSAT, $Internet_TS_TQ_CSAT) {
                $csatAverage = (($Internet_TS_TQ_CUS_CSAT) != 0) ? round(($Internet_TS_TQ_CSAT / $Internet_TS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('Q' . $rowStart, function ($cell) use ($TV_TS_TQ_CSAT1) {
                $cell->setValue($TV_TS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('R' . $rowStart, function ($cell) use ($TV_TS_TQ_CSAT2) {
                $cell->setValue($TV_TS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('S' . $rowStart, function ($cell) use ($TV_TS_TQ_CSAT12) {
                $cell->setValue($TV_TS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('T' . $rowStart, function ($cell) use ($TV_TS_TQ_CUS_CSAT, $TV_TS_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($TV_TS_TQ_CUS_CSAT) != 0) ? round(($TV_TS_TQ_CSAT12 / $TV_TS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('U' . $rowStart, function ($cell) use ($TV_TS_TQ_CUS_CSAT, $TV_TS_TQ_CSAT) {
                $csatAverage = (($TV_TS_TQ_CUS_CSAT) != 0) ? round(($TV_TS_TQ_CSAT / $TV_TS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('V' . $rowStart, function ($cell) use ($Internet_TIN_TQ_CSAT1) {
                $cell->setValue($Internet_TIN_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('W' . $rowStart, function ($cell) use ($Internet_TIN_TQ_CSAT2) {
                $cell->setValue($Internet_TIN_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('X' . $rowStart, function ($cell) use ($Internet_TIN_TQ_CSAT12) {
                $cell->setValue($Internet_TIN_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('Y' . $rowStart, function ($cell) use ($Internet_TIN_TQ_CUS_CSAT, $Internet_TIN_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($Internet_TIN_TQ_CUS_CSAT) != 0) ? round(($Internet_TIN_TQ_CSAT12 / $Internet_TIN_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('Z' . $rowStart, function ($cell) use ($Internet_TIN_TQ_CUS_CSAT, $Internet_TIN_TQ_CSAT) {
                $csatAverage = (($Internet_TIN_TQ_CUS_CSAT) != 0) ? round(($Internet_TIN_TQ_CSAT / $Internet_TIN_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AA' . $rowStart, function ($cell) use ($TV_TIN_TQ_CSAT1) {
                $cell->setValue($TV_TIN_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AB' . $rowStart, function ($cell) use ($TV_TIN_TQ_CSAT2) {
                $cell->setValue($TV_TIN_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AC' . $rowStart, function ($cell) use ($TV_TIN_TQ_CSAT12) {
                $cell->setValue($TV_TIN_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AD' . $rowStart, function ($cell) use ($TV_TIN_TQ_CUS_CSAT, $TV_TIN_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($TV_TIN_TQ_CUS_CSAT) != 0) ? round(($TV_TIN_TQ_CSAT12 / $TV_TIN_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('AE' . $rowStart, function ($cell) use ($TV_TIN_TQ_CUS_CSAT, $TV_TIN_TQ_CSAT) {
                $csatAverage = (($TV_TIN_TQ_CUS_CSAT) != 0) ? round(($TV_TIN_TQ_CSAT / $TV_TIN_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AF' . $rowStart, function ($cell) use ($Internet_INDO_TQ_CSAT1) {
                $cell->setValue($Internet_INDO_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AG' . $rowStart, function ($cell) use ($Internet_INDO_TQ_CSAT2) {
                $cell->setValue($Internet_INDO_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AH' . $rowStart, function ($cell) use ($Internet_INDO_TQ_CSAT12) {
                $cell->setValue($Internet_INDO_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AI' . $rowStart, function ($cell) use ($Internet_INDO_TQ_CUS_CSAT, $Internet_INDO_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($Internet_INDO_TQ_CUS_CSAT) != 0) ? round(($Internet_INDO_TQ_CSAT12 / $Internet_INDO_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('AJ' . $rowStart, function ($cell) use ($Internet_INDO_TQ_CUS_CSAT, $Internet_INDO_TQ_CSAT) {
                $csatAverage = (($Internet_INDO_TQ_CUS_CSAT) != 0) ? round(($Internet_INDO_TQ_CSAT / $Internet_INDO_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AK' . $rowStart, function ($cell) use ($TV_INDO_TQ_CSAT1) {
                $cell->setValue($TV_INDO_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AL' . $rowStart, function ($cell) use ($TV_INDO_TQ_CSAT2) {
                $cell->setValue($TV_INDO_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AM' . $rowStart, function ($cell) use ($TV_INDO_TQ_CSAT12) {
                $cell->setValue($TV_INDO_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AN' . $rowStart, function ($cell) use ($TV_INDO_TQ_CUS_CSAT, $TV_INDO_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($TV_INDO_TQ_CUS_CSAT) != 0) ? round(($TV_INDO_TQ_CSAT12 / $TV_INDO_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('AO' . $rowStart, function ($cell) use ($TV_INDO_TQ_CUS_CSAT, $TV_INDO_TQ_CSAT) {
                $csatAverage = (($TV_INDO_TQ_CUS_CSAT) != 0) ? round(($TV_INDO_TQ_CSAT / $TV_INDO_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AP' . $rowStart, function ($cell) use ($Internet_CUS_TQ_CSAT1) {
                $cell->setValue($Internet_CUS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AQ' . $rowStart, function ($cell) use ($Internet_CUS_TQ_CSAT2) {
                $cell->setValue($Internet_CUS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AR' . $rowStart, function ($cell) use ($Internet_CUS_TQ_CSAT12) {
                $cell->setValue($Internet_CUS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AS' . $rowStart, function ($cell) use ($Internet_CUS_TQ_CUS_CSAT, $Internet_CUS_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($Internet_CUS_TQ_CUS_CSAT) != 0) ? round(($Internet_CUS_TQ_CSAT12 / $Internet_CUS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('AT' . $rowStart, function ($cell) use ($Internet_CUS_TQ_CUS_CSAT, $Internet_CUS_TQ_CSAT) {
                $csatAverage = (($Internet_CUS_TQ_CUS_CSAT) != 0) ? round(($Internet_CUS_TQ_CSAT / $Internet_CUS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('AU' . $rowStart, function ($cell) use ($TV_CUS_TQ_CSAT1) {
                $cell->setValue($TV_CUS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AV' . $rowStart, function ($cell) use ($TV_CUS_TQ_CSAT2) {
                $cell->setValue($TV_CUS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AW' . $rowStart, function ($cell) use ($TV_CUS_TQ_CSAT12) {
                $cell->setValue($TV_CUS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AX' . $rowStart, function ($cell) use ($TV_CUS_TQ_CUS_CSAT, $TV_CUS_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($TV_CUS_TQ_CUS_CSAT) != 0) ? round(($TV_CUS_TQ_CSAT12 / $TV_CUS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('AY' . $rowStart, function ($cell) use ($TV_CUS_TQ_CUS_CSAT, $TV_CUS_TQ_CSAT) {
                $csatAverage = (($TV_CUS_TQ_CUS_CSAT) != 0) ? round(($TV_CUS_TQ_CSAT / $TV_CUS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('AZ' . $rowStart, function ($cell) use ($GDTQ_TQ_CSAT1) {
                $cell->setValue($GDTQ_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BA' . $rowStart, function ($cell) use ($GDTQ_TQ_CSAT2) {
                $cell->setValue($GDTQ_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BB' . $rowStart, function ($cell) use ($GDTQ_TQ_CSAT12) {
                $cell->setValue($GDTQ_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BC' . $rowStart, function ($cell) use ($GDTQ_TQ_CUS_CSAT, $GDTQ_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($GDTQ_TQ_CUS_CSAT) != 0) ? round(($GDTQ_TQ_CSAT12 / $GDTQ_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('BD' . $rowStart, function ($cell) use ($GDTQ_TQ_CUS_CSAT, $GDTQ_TQ_CSAT) {
                $csatAverage = (($GDTQ_TQ_CUS_CSAT) != 0) ? round(($GDTQ_TQ_CSAT / $GDTQ_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BE' . $rowStart, function ($cell) use ($Internet_SS_TQ_CSAT1) {
                $cell->setValue($Internet_SS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BF' . $rowStart, function ($cell) use ($Internet_SS_TQ_CSAT2) {
                $cell->setValue($Internet_SS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BG' . $rowStart, function ($cell) use ($Internet_SS_TQ_CSAT12) {
                $cell->setValue($Internet_SS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BH' . $rowStart, function ($cell) use ($Internet_SS_TQ_CUS_CSAT, $Internet_SS_TQ_CSAT12) {

                $rateNotSastisfied = (($Internet_SS_TQ_CUS_CSAT) != 0) ? round(($Internet_SS_TQ_CSAT12 / $Internet_SS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BI' . $rowStart, function ($cell) use ($Internet_SS_TQ_CUS_CSAT, $Internet_SS_TQ_CSAT) {
                $csatAverage = (($Internet_SS_TQ_CUS_CSAT) != 0) ? round(($Internet_SS_TQ_CSAT / $Internet_SS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BJ' . $rowStart, function ($cell) use ($TV_SS_TQ_CSAT1) {
                $cell->setValue($TV_SS_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BK' . $rowStart, function ($cell) use ($TV_SS_TQ_CSAT2) {
                $cell->setValue($TV_SS_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BL' . $rowStart, function ($cell) use ($TV_SS_TQ_CSAT12) {
                $cell->setValue($TV_SS_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BM' . $rowStart, function ($cell) use ($TV_SS_TQ_CUS_CSAT, $TV_SS_TQ_CSAT12) {
                $this->setTitleMainRow($cell);

                $rateNotSastisfied = (($TV_SS_TQ_CUS_CSAT) != 0) ? round(($TV_SS_TQ_CSAT12 / $TV_SS_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('BN' . $rowStart, function ($cell) use ($TV_SS_TQ_CUS_CSAT, $TV_SS_TQ_CSAT) {
                $csatAverage = (($TV_SS_TQ_CUS_CSAT) != 0) ? round(($TV_SS_TQ_CSAT / $TV_SS_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BO' . $rowStart, function ($cell) use ($Internet_SSW_TQ_CSAT1) {
                $cell->setValue($Internet_SSW_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BP' . $rowStart, function ($cell) use ($Internet_SSW_TQ_CSAT2) {
                $cell->setValue($Internet_SSW_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BQ' . $rowStart, function ($cell) use ($Internet_SSW_TQ_CSAT12) {
                $cell->setValue($Internet_SSW_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BR' . $rowStart, function ($cell) use ($Internet_SSW_TQ_CUS_CSAT, $Internet_SSW_TQ_CSAT12) {

                $rateNotSastisfied = (($Internet_SSW_TQ_CUS_CSAT) != 0) ? round(($Internet_SSW_TQ_CSAT12 / $Internet_SSW_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BS' . $rowStart, function ($cell) use ($Internet_SSW_TQ_CUS_CSAT, $Internet_SSW_TQ_CSAT) {
                $csatAverage = (($Internet_SSW_TQ_CUS_CSAT) != 0) ? round(($Internet_SSW_TQ_CSAT / $Internet_SSW_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BT' . $rowStart, function ($cell) use ($TV_SSW_TQ_CSAT1) {
                $cell->setValue($TV_SSW_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BU' . $rowStart, function ($cell) use ($TV_SSW_TQ_CSAT2) {
                $cell->setValue($TV_SSW_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BV' . $rowStart, function ($cell) use ($TV_SSW_TQ_CSAT12) {
                $cell->setValue($TV_SSW_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BW' . $rowStart, function ($cell) use ($TV_SSW_TQ_CUS_CSAT, $TV_SSW_TQ_CSAT12) {
                $this->setTitleMainRow($cell);

                $rateNotSastisfied = (($TV_SSW_TQ_CUS_CSAT) != 0) ? round(($TV_SSW_TQ_CSAT12 / $TV_SSW_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('BX' . $rowStart, function ($cell) use ($TV_SSW_TQ_CUS_CSAT, $TV_SSW_TQ_CSAT) {
                $csatAverage = (($TV_SSW_TQ_CUS_CSAT) != 0) ? round(($TV_SSW_TQ_CSAT / $TV_SSW_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BY' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT1) {
                $cell->setValue($Internet_KHL_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('BZ' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT2) {
                $cell->setValue($Internet_KHL_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('CA' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT12) {
                $cell->setValue($Internet_KHL_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('CB' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CUS_CSAT, $Internet_KHL_TQ_CSAT12) {
                $rateNotSastisfied = (($Internet_KHL_TQ_CUS_CSAT) != 0) ? round(($Internet_KHL_TQ_CSAT12 / $Internet_KHL_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('CC' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CUS_CSAT, $Internet_KHL_TQ_CSAT) {
                $csatAverage = (($Internet_KHL_TQ_CUS_CSAT) != 0) ? round(($Internet_KHL_TQ_CSAT / $Internet_KHL_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('CD' . $rowStart, function ($cell) use ($TV_KHL_TQ_CSAT1) {
                $cell->setValue($TV_KHL_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('CE' . $rowStart, function ($cell) use ($TV_KHL_TQ_CSAT2) {
                $cell->setValue($TV_KHL_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setTitleMainRow($cell);
                $this->setBorderCell($cell);
            })->cell('CF' . $rowStart, function ($cell) use ($TV_KHL_TQ_CSAT12) {
                $cell->setValue($TV_KHL_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setTitleMainRow($cell);
                $this->setBorderCell($cell);
            })->cell('CG' . $rowStart, function ($cell) use ($TV_KHL_TQ_CUS_CSAT, $TV_KHL_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($TV_KHL_TQ_CUS_CSAT) != 0) ? round(($TV_KHL_TQ_CSAT12 / $TV_KHL_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('CH' . $rowStart, function ($cell) use ($TV_KHL_TQ_CUS_CSAT, $TV_KHL_TQ_CSAT) {
                $csatAverage = (($TV_KHL_TQ_CUS_CSAT) != 0) ? round(($TV_KHL_TQ_CSAT / $TV_KHL_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setTitleMainRow($cell);
                $this->setBorderCell($cell);
            });
        return $rowStart;
    }

    //Tạo bảng thống kê csat12 hành động xử lý, đã rút gọn
    public function createDetailActionCsat12($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 68)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('4. ' . trans('report.StatisticalOfCsatAction12ForStaff'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->mergeCells('A' . ($rowIndex + 1) . ':A' . ($rowIndex + 3))->setWidth('A', 40)->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.ResolvedActionOfStaff'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':C' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1) . ':C' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('D' . ($rowIndex + 1) . ':E' . ($rowIndex + 1))->cell('D' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })
            ->cell('E' . ($rowIndex + 1), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('F' . ($rowIndex + 1) . ':G' . ($rowIndex + 1), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('F' . ($rowIndex + 1) . ':G' . ($rowIndex + 1))->cell('F' . ($rowIndex + 1), function ($cell) {
                $cell->setValue(trans('report.Total'));
                $this->setTitleHeaderTable($cell);
            })->cell('F' . ($rowIndex + 1) . ':G' . ($rowIndex + 1), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })
            ->mergeCells('B' . ($rowIndex + 2) . ':C' . ($rowIndex + 2))->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CSAT 1,2 Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('D' . ($rowIndex + 2) . ':E' . ($rowIndex + 2))->cell('D' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('D' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CSAT 1,2 Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->mergeCells('F' . ($rowIndex + 2) . ':G' . ($rowIndex + 2))->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setValue('CSAT 1,2 Internet');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })
            ->setWidth('B', 20)->cell('B' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('C', 20)->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('D', 20)->cell('D' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('E', 20)->cell('E' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('F', 20)->cell('F' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Quantity'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('G', 20)->cell('G' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.Percent'));
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 4;
        $surveyCSATActionService12 = $detailCSAT['surveyCSATActionService12'];
        foreach ($surveyCSATActionService12 as $key => $value) {
            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue(trans('report.' . $value->action));
                $this->setTitleBodyTable($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($surveyCSATActionService12, $value) {
                $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CSAT_12) != 0) ? round(($value->INTERNET_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CSAT_12) * 100, 2) : 0;
                $cell->setValue($rateAction . '%');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('D' . $rowStart, function ($cell) use ($value) {

                    $cell->setValue($value->INTERNET_SBT_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('E' . $rowStart, function ($cell) use ($surveyCSATActionService12, $value) {
                    $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SBT_CSAT_12) != 0) ? round(($value->INTERNET_SBT_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SBT_CSAT_12) * 100, 2) : 0;
                    $cell->setValue($rateAction . '%');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })
                ->cell('F' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->TOTAL_INTERNET_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('G' . $rowStart, function ($cell) use ($surveyCSATActionService12, $value) {
                    $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_INTERNET_CSAT_12) != 0) ? round(($value->TOTAL_INTERNET_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_INTERNET_CSAT_12) * 100, 2) : 0;
                    $cell->setValue($rateAction . '%');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart++;
        }
        return $rowStart;
    }

    //Tạo bảng CSAT theo khu vực
    public function createCsatLocationReport($sheet, $survey, $rowStart4, $npsCountryRegion, $npsRegion, $arrCountry)
    {
        //Tạo khung CSAT
        $sheet->cell('A' . $rowStart4, function ($cell) {
            $cell->setValue('1. ' . trans('report.StatisticalOfCSATNPSPointofLocation'));
            $this->setTitleTable($cell);
        })->setWidth('A', 20)->mergeCells('A' . ($rowStart4 + 1) . ':A' . ($rowStart4 + 3))->cell('A' . ($rowStart4 + 1), function ($cell) {
            $cell->setValue(trans('report.Location'));
            $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowStart4 + 2), function ($cell) {

            $cell->setBorder('none', 'thin', 'none', 'none');
        })->cell('A' . ($rowStart4 + 3), function ($cell) {

            $cell->setBorder('none', 'thin', 'none', 'none');
        })->mergeCells('B' . ($rowStart4 + 1) . ':G' . ($rowStart4 + 1))->cell('B' . ($rowStart4 + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowStart4 + 1) . ':G' . ($rowStart4 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('H' . ($rowStart4 + 1) . ':K' . ($rowStart4 + 1))->cell('H' . ($rowStart4 + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->cell('H' . ($rowStart4 + 1) . ':K' . ($rowStart4 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('B' . ($rowStart4 + 2) . ':C' . ($rowStart4 + 2))->mergeCells('B' . ($rowStart4 + 3) . ':C' . ($rowStart4 + 3))->cell('B' . ($rowStart4 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('C' . ($rowStart4 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('B' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Saler'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowStart4 + 3, 'thin-thin-thin-thin');
        $sheet->mergeCells('D' . ($rowStart4 + 2) . ':E' . ($rowStart4 + 2))->mergeCells('D' . ($rowStart4 + 3) . ':E' . ($rowStart4 + 3))->cell('D' . ($rowStart4 + 3), function ($cell) {
            $cell->setBorder('none', 'none', 'none', 'thin');
            $cell->setBackground('#8DB4E2');
        })->cell('E' . ($rowStart4 + 3), function ($cell) {
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('none', 'none', 'none', 'none');
        })->cell('D' . ($rowStart4 + 2), function ($cell) {
            $cell->setValue(trans('report.Deployer'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('thin', 'thin', 'none', 'thin');
            $cell->setFontWeight('bold');
        })->mergeCells('F' . ($rowStart4 + 2) . ':G' . ($rowStart4 + 2))->cell('F' . ($rowStart4 + 2), function ($cell) {
            $cell->setValue(trans('report.Rating Quality Service'));
            $this->setTitleHeaderTable($cell);
        })->cell('F' . ($rowStart4 + 2) . ':G' . ($rowStart4 + 2), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('F' . ($rowStart4 + 3) . ':G' . ($rowStart4 + 3))->cell('F' . ($rowStart4 + 3), function ($cell) {
            $cell->setValue('Internet');
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('H' . ($rowStart4 + 2) . ':I' . ($rowStart4 + 2))->mergeCells('H' . ($rowStart4 + 3) . ':I' . ($rowStart4 + 3))->cell('H' . ($rowStart4 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('I' . ($rowStart4 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('H' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.MaintainanceStaff'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('J' . ($rowStart4 + 2) . ':K' . ($rowStart4 + 2))->cell('J' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Rating Quality Service'));
                $this->setTitleHeaderTable($cell);
            })->cell('J' . ($rowStart4 + 2) . ':K' . ($rowStart4 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('J' . ($rowStart4 + 3) . ':K' . ($rowStart4 + 3))->cell('J' . ($rowStart4 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            });
        //Tạo row vùng
        $rowStart6 = $rowStart4 + 4;
        foreach ($survey as $key => $value) {
            $sheet->cell('A' . $rowStart6, function ($cell) use ($value) {
                $this->setTitleBodyTable($cell);
                $cell->setValue($value->KhuVuc);
            });
            $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowStart6, 'thin-thin-thin-thin');
            $sheet->mergeCells('B' . $rowStart6 . ':C' . $rowStart6)->cell('B' . $rowStart6, function ($cell) use ($value) {
                if ((int)$value->SoLuongKD != 0) {
                    $cell->setValue(number_format(round(((int)$value->NVKinhDoanhPoint) / ((int)$value->SoLuongKD), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
            })->mergeCells('D' . $rowStart6 . ':E' . $rowStart6)->cell('D' . $rowStart6, function ($cell) use ($value) {
                if ((int)$value->SoLuongTK != 0) {
                    $cell->setValue(number_format(round(((int)$value->NVTrienKhaiPoint) / ((int)$value->SoLuongTK), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
            })->mergeCells('F' . $rowStart6 . ':G' . $rowStart6)->cell('F' . $rowStart6, function ($cell) use ($value) {
                if ((int)$value->SoLuongDGDV_Net != 0) {
                    $cell->setValue(number_format(round(((int)$value->DGDichVu_Net_Point) / ((int)$value->SoLuongDGDV_Net), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
            })
                ->mergeCells('H' . $rowStart6 . ':I' . $rowStart6)->cell('H' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongNVBaoTri != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVBaoTriPoint) / ((int)$value->SoLuongNVBaoTri), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('J' . $rowStart6 . ':K' . $rowStart6)->cell('J' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDVBaoTri_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DVBaoTri_Net_Point) / ((int)$value->SoLuongDVBaoTri_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart6++;
        }
        //Tạo row tổng cộng
        $sheet->cell('A' . $rowStart6, function ($cell) {
            $this->setTitleBodyTable($cell);
            $cell->setValue(trans('report.WholeCountry'));
            $cell->setFontWeight('bold');
        });
        $this->extraFunc->setColumnByFormat('C', 10, $sheet, $rowStart6, 'thin-thin-thin-thin');
        $sheet->mergeCells('B' . $rowStart6 . ':C' . $rowStart6)->cell('B' . $rowStart6, function ($cell) use ($arrCountry) {
            $sum = 0;
            foreach ($arrCountry as $key => $value) {
                $sum += ($value->SoLuongKD > 0) ? ($value->NVKinhDoanhPoint / $value->SoLuongKD) : 0;
            }
            if (count($arrCountry) > 0) {
                $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
            } else {
                $cell->setValue(0);
            }
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
        })->mergeCells('D' . $rowStart6 . ':E' . $rowStart6)->cell('D' . $rowStart6, function ($cell) use ($arrCountry) {
            $sum = 0;
            foreach ($arrCountry as $key => $value) {
                $sum += ($value->SoLuongTK > 0) ? ($value->NVTrienKhaiPoint / $value->SoLuongTK) : 0;
            }
            if (count($arrCountry) > 0) {
                $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
            } else {
                $cell->setValue(0);
            }
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->mergeCells('F' . $rowStart6 . ':G' . $rowStart6)->cell('F' . $rowStart6, function ($cell) use ($arrCountry) {
            $sum = 0;
            foreach ($arrCountry as $key => $value) {
                $sum += $value->SoLuongDGDV_Net != 0 ? ($value->DGDichVu_Net_Point / $value->SoLuongDGDV_Net) : (0);
            }
            if (count($arrCountry) > 0) {
                $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
            } else {
                $cell->setValue(0);
            }
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->mergeCells('H' . $rowStart6 . ':I' . $rowStart6)->cell('H' . $rowStart6, function ($cell) use ($arrCountry) {
            $sum = 0;
            foreach ($arrCountry as $key => $value) {
                $sum += $value->NVBaoTriPoint != 0 ? ($value->NVBaoTriPoint / $value->SoLuongNVBaoTri) : (0);
            }
            if (count($arrCountry) > 0) {
                $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
            } else {
                $cell->setValue(0);
            }
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        })->mergeCells('J' . $rowStart6 . ':K' . $rowStart6)->cell('J' . $rowStart6, function ($cell) use ($arrCountry) {
            $sum = 0;
            foreach ($arrCountry as $key => $value) {
                $sum += $value->DVBaoTri_Net_Point != 0 ? ($value->DVBaoTri_Net_Point / $value->SoLuongDVBaoTri_Net) : (0);
            }
            if (count($arrCountry) > 0) {
                $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
            } else {
                $cell->setValue(0);
            }
            $cell->setAlignment('center');
            $this->setBorderCell($cell);
            $cell->setFontWeight('bold');
        });
        //Khung NPS
        $rowStart7 = $rowStart4 + 4 + count($survey);

        return [0 => $rowStart7, 1 => $npsCountryRegion['WholeCountry'] . ' %'];
    }

    //Tạo bảng CSAT theo chi nhánh
    public function createCsatBranchReport($sheet, $surveyBranches, $survey, $rowStart6, $surveyNPSBranches, $sumTotal, $arrCountry)
    {
        $indexStart = $rowStart6;
        //Tạo khung CSAT
        $sheet->cell('A' . $rowStart6, function ($cell) {
            $cell->setValue('2.Báo cáo CSAT,NPS NV kinh doanh theo chi nhánh');
            $this->setTitleTable($cell);
        })->setWidth('A', 20)->mergeCells('A' . ($rowStart6 + 1) . ':A' . ($rowStart6 + 3))->cell('A' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Vùng');
            $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowStart6 + 2), function ($cell) {

            $cell->setBorder('none', 'thin', 'none', 'none');
        })->cell('A' . ($rowStart6 + 3), function ($cell) {

            $cell->setBorder('none', 'thin', 'none', 'none');
        })->mergeCells('B' . ($rowStart6 + 1) . ':I' . ($rowStart6 + 1))->cell('B' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau triển khai DirectSales');
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowStart6 + 1) . ':I' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('J' . ($rowStart6 + 1) . ':Q' . ($rowStart6 + 1))->cell('J' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau triển khai TLS');
            $this->setTitleHeaderTable($cell);
        })->cell('J' . ($rowStart6 + 1) . ':Q' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('R' . ($rowStart6 + 1) . ':W' . ($rowStart6 + 1))->cell('R' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau bảo trì TIN-PNC');
            $this->setTitleHeaderTable($cell);
        })->cell('R' . ($rowStart6 + 1) . ':W' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('X' . ($rowStart6 + 1) . ':AC' . ($rowStart6 + 1))->cell('X' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau bảo trì INDO');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowStart6 + 1) . ':Y' . ($rowStart6 + 1))->cell('V' . ($rowStart6 + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('X' . ($rowStart6 + 1) . ':AC' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AD' . ($rowStart6 + 1) . ':AI' . ($rowStart6 + 1))->cell('AD' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau thu cước');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowStart6 + 1) . ':Y' . ($rowStart6 + 1))->cell('V' . ($rowStart6 + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('AD' . ($rowStart6 + 1) . ':AI' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AJ' . ($rowStart6 + 1) . ':AM' . ($rowStart6 + 1))->cell('AJ' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau GDTQ');
            $this->setTitleHeaderTable($cell);
//            })->mergeCells('V' . ($rowStart6 + 1) . ':Y' . ($rowStart6 + 1))->cell('V' . ($rowStart6 + 1), function($cell) {
//                $cell->setValue('Sau thu cước');
//                $this->setTitleHeaderTable($cell);
        })->cell('AJ' . ($rowStart6 + 1) . ':AM' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AN' . ($rowStart6 + 1) . ':AU' . ($rowStart6 + 1))->cell('AN' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau triển khai sale tại quầy');
            $this->setTitleHeaderTable($cell);
        })->cell('AN' . ($rowStart6 + 1) . ':AN' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('AV' . ($rowStart6 + 1) . ':BA' . ($rowStart6 + 1))->cell('AV' . ($rowStart6 + 1), function ($cell) {
            $cell->setValue('Sau triển khai Swap');
            $this->setTitleHeaderTable($cell);
        })->cell('AV' . ($rowStart6 + 1) . ':BA' . ($rowStart6 + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('B' . ($rowStart6 + 2) . ':C' . ($rowStart6 + 2))->mergeCells('B' . ($rowStart6 + 3) . ':C' . ($rowStart6 + 3))->cell('B' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('C' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('B' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })->cell('AO' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })->cell('AP' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })->cell('AQ' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })->cell('AR' . ($rowStart6 + 1), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })
            ->cell('AS' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })->cell('AT' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })->cell('AU' . ($rowStart6 + 1), function ($cell) {

                $this->setTitleHeaderTable($cell);
            })
            ->cell('E' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('G' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('I' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })
            ->cell('K' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('M' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('O' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('Q' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })
            ->cell('S' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('U' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('W' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })
            ->cell('Y' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AA' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AC' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })
            ->cell('AE' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AG' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AI' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })
            ->cell('AK' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })->mergeCells('D' . ($rowStart6 + 2) . ':E' . ($rowStart6 + 2))->mergeCells('D' . ($rowStart6 + 3) . ':E' . ($rowStart6 + 3))->cell('D' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('E' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('D' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'none', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('F' . ($rowStart6 + 2) . ':I' . ($rowStart6 + 2))->cell('F' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('F' . ($rowStart6 + 2) . ':I' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('F' . ($rowStart6 + 3) . ':G' . ($rowStart6 + 3))->cell('F' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('H' . ($rowStart6 + 3) . ':I' . ($rowStart6 + 3))->cell('H' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })
            ->mergeCells('J' . ($rowStart6 + 2) . ':K' . ($rowStart6 + 2))->mergeCells('J' . ($rowStart6 + 3) . ':K' . ($rowStart6 + 3))->cell('J' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('K' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('J' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('L' . ($rowStart6 + 2) . ':M' . ($rowStart6 + 2))->mergeCells('L' . ($rowStart6 + 3) . ':M' . ($rowStart6 + 3))->cell('L' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('M' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('L' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'none', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('N' . ($rowStart6 + 2) . ':Q' . ($rowStart6 + 2))->cell('N' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('N' . ($rowStart6 + 2) . ':Q' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('N' . ($rowStart6 + 3) . ':O' . ($rowStart6 + 3))->cell('N' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('P' . ($rowStart6 + 3) . ':Q' . ($rowStart6 + 3))->cell('P' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })
            ->mergeCells('R' . ($rowStart6 + 2) . ':S' . ($rowStart6 + 2))->mergeCells('R' . ($rowStart6 + 3) . ':S' . ($rowStart6 + 3))->cell('R' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('S' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('R' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV bảo trì TIN-PNC');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('T' . ($rowStart6 + 2) . ':W' . ($rowStart6 + 2))->cell('T' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('T' . ($rowStart6 + 2) . ':W' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('T' . ($rowStart6 + 3) . ':U' . ($rowStart6 + 3))->cell('T' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('V' . ($rowStart6 + 3) . ':W' . ($rowStart6 + 3))->cell('V' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('X' . ($rowStart6 + 2) . ':Y' . ($rowStart6 + 2))->mergeCells('X' . ($rowStart6 + 3) . ':Y' . ($rowStart6 + 3))->cell('X' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('Y' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('X' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV bảo trì INDO');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('Z' . ($rowStart6 + 2) . ':AC' . ($rowStart6 + 2))->cell('Z' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('Z' . ($rowStart6 + 2) . ':AC' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('Z' . ($rowStart6 + 3) . ':AA' . ($rowStart6 + 3))->cell('Z' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AB' . ($rowStart6 + 3) . ':AC' . ($rowStart6 + 3))->cell('AB' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AD' . ($rowStart6 + 2) . ':AE' . ($rowStart6 + 2))->mergeCells('AD' . ($rowStart6 + 3) . ':AE' . ($rowStart6 + 3))->cell('AD' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('AE' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AD' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV thu cước');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('AF' . ($rowStart6 + 2) . ':AI' . ($rowStart6 + 2))->cell('AF' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('AF' . ($rowStart6 + 2) . ':AI' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AF' . ($rowStart6 + 3) . ':AG' . ($rowStart6 + 3))->cell('AF' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AH' . ($rowStart6 + 3) . ':AI' . ($rowStart6 + 3))->cell('AH' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AJ' . ($rowStart6 + 2) . ':AK' . ($rowStart6 + 2))->mergeCells('AJ' . ($rowStart6 + 3) . ':AK' . ($rowStart6 + 3))->cell('AJ' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('AJ' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AJ' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV giao dịch');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('AL' . ($rowStart6 + 2) . ':AM' . ($rowStart6 + 2))->mergeCells('AL' . ($rowStart6 + 3) . ':AM' . ($rowStart6 + 3))->cell('AL' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('AM' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AL' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng DV');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })->cell('AK' . ($rowStart6 + 2), function ($cell) {

                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('AK' . ($rowStart6 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'thin', 'none');
            })->mergeCells('AN' . ($rowStart6 + 2) . ':AO' . ($rowStart6 + 2))->mergeCells('AN' . ($rowStart6 + 3) . ':AO' . ($rowStart6 + 3))->cell('AN' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setBackground('#8DB4E2');
            })->cell('AO' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('AN' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV kinh doanh');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })->mergeCells('AP' . ($rowStart6 + 2) . ':AQ' . ($rowStart6 + 2))->mergeCells('AP' . ($rowStart6 + 3) . ':AQ' . ($rowStart6 + 3))->cell('AP' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AQ' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AP' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('NV triển khai');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'none', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AR' . ($rowStart6 + 2) . ':AU' . ($rowStart6 + 2))->cell('AR' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('AR' . ($rowStart6 + 2) . ':AS' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AR' . ($rowStart6 + 3) . ':AS' . ($rowStart6 + 3))->cell('AR' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AT' . ($rowStart6 + 3) . ':AU' . ($rowStart6 + 3))->cell('AT' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AV' . ($rowStart6 + 2) . ':AW' . ($rowStart6 + 2))->mergeCells('AV' . ($rowStart6 + 3) . ':AW' . ($rowStart6 + 3))->cell('AV' . ($rowStart6 + 3), function ($cell) {
                $cell->setBorder('none', 'none', 'none', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('AW' . ($rowStart6 + 3), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('AV' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Nhân viên triển khai Swap');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'none', 'thin');
                $cell->setFontWeight('bold');
            })->mergeCells('AX' . ($rowStart6 + 2) . ':BA' . ($rowStart6 + 2))->cell('AX' . ($rowStart6 + 2), function ($cell) {
                $cell->setValue('Chất lượng dịch vụ');
                $this->setTitleHeaderTable($cell);
            })->cell('AX' . ($rowStart6 + 2) . ':BA' . ($rowStart6 + 2), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AX' . ($rowStart6 + 3) . ':AY' . ($rowStart6 + 3))->cell('AX' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Internet');
                $this->setTitleHeaderTable($cell);
            })->mergeCells('AZ' . ($rowStart6 + 3) . ':BA' . ($rowStart6 + 3))->cell('AZ' . ($rowStart6 + 3), function ($cell) {
                $cell->setValue('Truyền hình');
                $this->setTitleHeaderTable($cell);
            });
        //Tạo row vùng
        $rowStart6 += 4;
        foreach ($surveyBranches as $key => $value) {
            $sheet->cell('A' . $rowStart6, function ($cell) use ($value) {
                $this->setTitleBodyTable($cell);
                $cell->setValue(substr($value->ChiNhanh, strlen($value->ChiNhanh) - 2) == '-0' ? substr($value->ChiNhanh, 0, strlen($value->ChiNhanh) - 2) : $value->ChiNhanh);
            })
                ->cell('C' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('E' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('G' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('I' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('K' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('M' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('O' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('Q' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('S' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('U' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('W' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('Y' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AA' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AC' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AE' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AG' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AI' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('AK' . ($rowStart6), function ($cell) {

                    $cell->setBorder('none', 'none', 'thin', 'none');
                })->cell('AM' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AO' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AQ' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AS' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AU' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AW' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('AY' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->cell('BA' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })->mergeCells('B' . $rowStart6 . ':C' . $rowStart6)->cell('B' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongKD != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVKinhDoanhPoint) / ((int)$value->SoLuongKD), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('D' . $rowStart6 . ':E' . $rowStart6)->cell('D' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongTK != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVTrienKhaiPoint) / ((int)$value->SoLuongTK), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('F' . $rowStart6 . ':G' . $rowStart6)->cell('F' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDV_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_Net_Point) / ((int)$value->SoLuongDGDV_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('H' . $rowStart6 . ':I' . $rowStart6)->cell('H' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDV_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_TV_Point) / ((int)$value->SoLuongDGDV_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('J' . $rowStart6 . ':K' . $rowStart6)->cell('J' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongKDTS != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVKinhDoanhTSPoint) / ((int)$value->SoLuongKDTS), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('L' . $rowStart6 . ':M' . $rowStart6)->cell('L' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongTKTS != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVTrienKhaiTSPoint) / ((int)$value->SoLuongTKTS), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('N' . $rowStart6 . ':O' . $rowStart6)->cell('N' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVTS_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuTS_Net_Point) / ((int)$value->SoLuongDGDVTS_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('P' . $rowStart6 . ':Q' . $rowStart6)->cell('P' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVTS_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuTS_TV_Point) / ((int)$value->SoLuongDGDVTS_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('R' . $rowStart6 . ':S' . $rowStart6)->cell('R' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongNVBaoTriTIN != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVBaoTriTINPoint) / ((int)$value->SoLuongNVBaoTriTIN), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('T' . $rowStart6 . ':U' . $rowStart6)->cell('T' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDVBaoTriTIN_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DVBaoTriTIN_Net_Point) / ((int)$value->SoLuongDVBaoTriTIN_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('V' . $rowStart6 . ':W' . $rowStart6)->cell('V' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDVBaoTriTIN_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DVBaoTriTIN_TV_Point) / ((int)$value->SoLuongDVBaoTriTIN_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('X' . $rowStart6 . ':Y' . $rowStart6)->cell('X' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongNVBaoTriINDO != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVBaoTriINDOPoint) / ((int)$value->SoLuongNVBaoTriINDO), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('Z' . $rowStart6 . ':AA' . $rowStart6)->cell('Z' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDVBaoTriINDO_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DVBaoTriINDO_Net_Point) / ((int)$value->SoLuongDVBaoTriINDO_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AB' . $rowStart6 . ':AC' . $rowStart6)->cell('AB' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDVBaoTriINDO_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DVBaoTriINDO_TV_Point) / ((int)$value->SoLuongDVBaoTriINDO_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AD' . $rowStart6 . ':AE' . $rowStart6)->cell('AD' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongNVThuCuoc != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVThuCuocPoint) / ((int)$value->SoLuongNVThuCuoc), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AF' . $rowStart6 . ':AG' . $rowStart6)->cell('AF' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDV_MobiPay_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_MobiPay_Net_Point) / ((int)$value->SoLuongDGDV_MobiPay_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AH' . $rowStart6 . ':AI' . $rowStart6)->cell('AH' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDV_MobiPay_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_MobiPay_TV_Point) / ((int)$value->SoLuongDGDV_MobiPay_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AJ' . $rowStart6 . ':AK' . $rowStart6)->cell('AJ' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->NV_Counter_Point != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_Counter_Point) / ((int)$value->SoLuongNV_Counter), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AL' . $rowStart6 . ':AM' . $rowStart6)->cell('AL' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDichVu_Counter != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVu_Counter_Point) / ((int)$value->SoLuongDGDichVu_Counter), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AN' . $rowStart6 . ':AO' . $rowStart6)->cell('AN' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongKDSS != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVKinhDoanhSSPoint) / ((int)$value->SoLuongKDSS), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AP' . $rowStart6 . ':AQ' . $rowStart6)->cell('AP' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongTKSS != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVTrienKhaiSSPoint) / ((int)$value->SoLuongTKSS), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AR' . $rowStart6 . ':AS' . $rowStart6)->cell('AR' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVSS_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuSS_Net_Point) / ((int)$value->SoLuongDGDVSS_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AT' . $rowStart6 . ':AU' . $rowStart6)->cell('AT' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVSS_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuSS_TV_Point) / ((int)$value->SoLuongDGDVSS_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AV' . $rowStart6 . ':AW' . $rowStart6)->cell('AV' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongSSW != 0) {
                        $cell->setValue(number_format(round(((int)$value->NVBT_SSWPoint) / ((int)$value->SoLuongSSW), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AX' . $rowStart6 . ':AY' . $rowStart6)->cell('AX' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVSSW_Net != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuSSW_Net_Point) / ((int)$value->SoLuongDGDVSSW_Net), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                })->mergeCells('AZ' . $rowStart6 . ':BA' . $rowStart6)->cell('AZ' . $rowStart6, function ($cell) use ($value) {
                    if ((int)$value->SoLuongDGDVSSW_TV != 0) {
                        $cell->setValue(number_format(round(((int)$value->DGDichVuSSW_TV_Point) / ((int)$value->SoLuongDGDVSSW_TV), 2), 2));
                    } else {
                        $cell->setValue(0);
                    }
                    $cell->setAlignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart6++;
        }
        //Tạo row tổng cộng
        $sheet->cell('A' . $rowStart6, function ($cell) use ($arrCountry) {
            $this->setTitleBodyTable($cell);
            $cell->setValue('Toàn quốc');
            $cell->setFontWeight('bold');
        })->cell('C' . ($rowStart6), function ($cell) {

            $cell->setBorder('none', 'none', 'thin', 'none');
        })
            ->cell('E' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('G' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('I' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('K' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('M' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('O' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('Q' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('S' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('U' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('W' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('Y' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AA' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AC' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AE' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AG' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AI' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })
            ->cell('AK' . ($rowStart6), function ($cell) {

                $cell->setBorder('none', 'none', 'thin', 'none');
            })->cell('AM' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AO' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AQ' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AS' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AU' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AW' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('AY' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->cell('BA' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })->mergeCells('B' . $rowStart6 . ':C' . $rowStart6)->cell('B' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongKD > 0) ? ($value->NVKinhDoanhPoint / $value->SoLuongKD) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('D' . $rowStart6 . ':E' . $rowStart6)->cell('D' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongTK) ? ($value->NVTrienKhaiPoint / $value->SoLuongTK) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('F' . $rowStart6 . ':G' . $rowStart6)->cell('F' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDV_Net != 0 ? ($value->DGDichVu_Net_Point / $value->SoLuongDGDV_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('H' . $rowStart6 . ':I' . $rowStart6)->cell('H' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDV_TV != 0 ? ($value->DGDichVu_TV_Point / $value->SoLuongDGDV_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('J' . $rowStart6 . ':K' . $rowStart6)->cell('J' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongKDTS > 0) ? ($value->NVKinhDoanhTSPoint / $value->SoLuongKDTS) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('L' . $rowStart6 . ':M' . $rowStart6)->cell('L' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongTKTS) ? ($value->NVTrienKhaiTSPoint / $value->SoLuongTKTS) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('N' . $rowStart6 . ':O' . $rowStart6)->cell('N' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVTS_Net != 0 ? ($value->DGDichVuTS_Net_Point / $value->SoLuongDGDVTS_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('P' . $rowStart6 . ':Q' . $rowStart6)->cell('P' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVTS_TV != 0 ? ($value->DGDichVuTS_TV_Point / $value->SoLuongDGDVTS_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('R' . $rowStart6 . ':S' . $rowStart6)->cell('R' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->NVBaoTriTINPoint != 0 ? ($value->NVBaoTriTINPoint / $value->SoLuongNVBaoTriTIN) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('T' . $rowStart6 . ':U' . $rowStart6)->cell('T' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DVBaoTriTIN_Net_Point != 0 ? ($value->DVBaoTriTIN_Net_Point / $value->SoLuongDVBaoTriTIN_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('V' . $rowStart6 . ':W' . $rowStart6)->cell('V' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DVBaoTriTIN_TV_Point != 0 ? ($value->DVBaoTriTIN_TV_Point / $value->SoLuongDVBaoTriTIN_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('X' . $rowStart6 . ':Y' . $rowStart6)->cell('X' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->NVBaoTriINDOPoint != 0 ? ($value->NVBaoTriINDOPoint / $value->SoLuongNVBaoTriINDO) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('Z' . $rowStart6 . ':AA' . $rowStart6)->cell('Z' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DVBaoTriINDO_Net_Point != 0 ? ($value->DVBaoTriINDO_Net_Point / $value->SoLuongDVBaoTriINDO_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AB' . $rowStart6 . ':AC' . $rowStart6)->cell('AB' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DVBaoTriINDO_TV_Point != 0 ? ($value->DVBaoTriINDO_TV_Point / $value->SoLuongDVBaoTriINDO_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AD' . $rowStart6 . ':AE' . $rowStart6)->cell('AD' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->NVThuCuocPoint != 0 ? ($value->NVThuCuocPoint / $value->SoLuongNVThuCuoc) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AF' . $rowStart6 . ':AG' . $rowStart6)->cell('AF' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DGDichVu_MobiPay_Net_Point != 0 ? ($value->DGDichVu_MobiPay_Net_Point / $value->SoLuongDGDV_MobiPay_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AH' . $rowStart6 . ':AI' . $rowStart6)->cell('AH' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DGDichVu_MobiPay_TV_Point != 0 ? ($value->DGDichVu_MobiPay_TV_Point / $value->SoLuongDGDV_MobiPay_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AJ' . $rowStart6 . ':AK' . $rowStart6)->cell('AJ' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongNV_Counter != 0 ? ($value->NV_Counter_Point / $value->SoLuongNV_Counter) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AL' . $rowStart6 . ':AM' . $rowStart6)->cell('AL' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->DGDichVu_Counter_Point != 0 ? ($value->DGDichVu_Counter_Point / $value->SoLuongDGDichVu_Counter) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AN' . $rowStart6 . ':AO' . $rowStart6)->cell('AN' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongKDSS > 0) ? ($value->NVKinhDoanhSSPoint / $value->SoLuongKDSS) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AP' . $rowStart6 . ':AQ' . $rowStart6)->cell('AP' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongTKSS) ? ($value->NVTrienKhaiSSPoint / $value->SoLuongTKSS) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AR' . $rowStart6 . ':AS' . $rowStart6)->cell('AR' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVSS_Net != 0 ? ($value->DGDichVuSS_Net_Point / $value->SoLuongDGDVSS_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AT' . $rowStart6 . ':AU' . $rowStart6)->cell('AT' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVSS_TV != 0 ? ($value->DGDichVuSS_TV_Point / $value->SoLuongDGDVSS_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AV' . $rowStart6 . ':AW' . $rowStart6)->cell('AV' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += ($value->SoLuongSSW) ? ($value->NVBT_SSWPoint / $value->SoLuongSSW) : 0;
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AX' . $rowStart6 . ':AY' . $rowStart6)->cell('AX' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVSSW_Net != 0 ? ($value->DGDichVuSSW_Net_Point / $value->SoLuongDGDVSSW_Net) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            })->mergeCells('AZ' . $rowStart6 . ':BA' . $rowStart6)->cell('AZ' . $rowStart6, function ($cell) use ($arrCountry) {
                $sum = 0;
                foreach ($arrCountry as $key => $value) {
                    $sum += $value->SoLuongDGDVSSW_TV != 0 ? ($value->DGDichVuSSW_TV_Point / $value->SoLuongDGDVSSW_TV) : (0);
                }
                if (count($arrCountry) > 0) {
                    $cell->setValue(number_format(round($sum / count($arrCountry), 2), 2));
                } else {
                    $cell->setValue(0);
                }
                $cell->setAlignment('center');
                $this->setBorderCell($cell);
                $cell->setFontWeight('bold');
            });
        //Tạo khung điểm NPS
//        $rowStart8 = $indexStart + 4;
//        foreach ($surveyBranches as $key => $value) {
//            $evaluate = $value->ChiNhanh;
//            $arrayBranchName = explode('-', $evaluate);
//            $evaluate = trim($arrayBranchName[0]) . ' - ' . trim($arrayBranchName[1]);
//            $sheet->mergeCells('AL' . $rowStart8 . ':AL' . $rowStart8)->cell('AL' . $rowStart8, function($cell) use($evaluate, $surveyNPSBranches) {
//                $valueEva = isset($surveyNPSBranches[$evaluate]) ? $surveyNPSBranches[$evaluate] : 0;
//                $cell->setValue($valueEva . '  %');
//                $cell->setAlignment('center');
//                $this->setBorderCell($cell);
//            });
//            $rowStart8++;
//        }
//        $sheet->mergeCells('AL' . $rowStart8 . ':AL' . $rowStart8)->cell('AL' . $rowStart8, function($cell) use($sumTotal) {
//            $cell->setValue($sumTotal);
//            $cell->setAlignment('center');
//            $this->setBorderCell($cell);
//            $cell->setFontWeight('bold');
//        });
    }

    //Tạo bảng thống kê giao dịch, email
    public function createTransactionReport($sheet, $data, $rowStart6)
    {
        $indexStart = $rowStart6;
        $sheet->cell('A' . $rowStart6, function ($cell) {
            $cell->setValue('1.Số lượng email, khách hàng, phản hồi, giao dịch tại quầy');
            $this->setTitleTable($cell);
        })->setWidth('A', 50)->setWidth('B', 50)->setWidth('C', 50)
            ->cell('A' . ($rowStart6 + 1), function ($cell) {
                $cell->setValue('Nội dung');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->cell('B' . ($rowStart6 + 1), function ($cell) {
                $cell->setValue('Sau giao dịch tại quầy');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            })->cell('C' . ($rowStart6 + 1), function ($cell) {
                $cell->setValue('Sau thu cước tại nhà');
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
            });
        $rowStart6 = $rowStart6 + 2;
        foreach ($data as $key => $value) {
            $sheet->cell('A' . $rowStart6, function ($cell) use ($key, $value) {
                if ($key == 'Tỉ lệ phản hồi') {
                    $this->setTitleMainRow($cell);
                } else
                    $this->setTitleBodyTable($cell);
                $cell->setValue($key);
            })->
            cell('B' . $rowStart6, function ($cell) use ($key, $value) {
                if ($key == 'Tỉ lệ phản hồi') {
                    $this->setTitleMainRow($cell);
                } else
                    $this->setTitleBodyTable($cell);
                $cell->setValue($value['SLGDTQ']);
            })->
            cell('C' . $rowStart6, function ($cell) use ($key, $value) {
                if ($key == 'Tỉ lệ phản hồi') {
                    $this->setTitleMainRow($cell);
                } else
                    $this->setTitleBodyTable($cell);
                $cell->setValue($value['SLGDTCTN']);
            });
            $rowStart6++;
        }
    }

    //Tạo bảng thống kê csat12 dịch vụ theo khu vực, đã rút gọn
    public function createDetailServiceCsat12Location($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 68)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('4. ' . trans('report.StatisticalOfUnsatisfactionCustomerForService'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->setWidth('A', 40)->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.TouchPoint'));
            $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Service'));
            $this->setTitleHeaderTable($cell);
        })->cell('A' . ($rowIndex + 3), function ($cell) {
            $cell->setValue(trans('report.Location'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':F' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1) . ':F' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('G' . ($rowIndex + 1) . ':K' . ($rowIndex + 1))->cell('G' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        })->cell('G' . ($rowIndex + 1) . ':K' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('L' . ($rowIndex + 1) . ':P' . ($rowIndex + 1))->cell('L' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.TotalOfUnsatisfactionCase'));
            $this->setTitleHeaderTable($cell);
        })->cell('L' . ($rowIndex + 1) . ':P' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })
            ->mergeCells('B' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setBorder('none', 'thin', 'thin', 'thin');
                $cell->setBackground('#8DB4E2');
            })->cell('B' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Net'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('F' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('G' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
            })->cell('G' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Net'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'none', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.Net'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('P' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('L' . ($rowIndex + 2) . ':P' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
            })
            ->setWidth('B', 20)->cell('B' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('C', 20)->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('D', 20)->cell('D' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('E', 20)->cell('E' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('F', 20)->cell('F' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('G', 20)->cell('G' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('H', 20)->cell('H' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('I', 20)->cell('I' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('J', 20)->cell('J' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('K', 20)->cell('K' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('L', 20)->cell('L' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('M', 20)->cell('M' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('N', 20)->cell('N' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('O', 20)->cell('O' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('P', 20)->cell('P' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 4;
        $Internet_TQ_CSAT1 = $Internet_TQ_CSAT2 = $Internet_TQ_CSAT12 = $Internet_TQ_CUS_CSAT = $Internet_TQ_CSAT
            = $Internet_SBT_TQ_CSAT1 = $Internet_SBT_TQ_CSAT2 = $Internet_SBT_TQ_CSAT12 = $Internet_SBT_TQ_CUS_CSAT = $Internet_SBT_TQ_CSAT
            = $Internet_KHL_TQ_CSAT1 = $Internet_KHL_TQ_CSAT2 = $Internet_KHL_TQ_CSAT12 = $Internet_KHL_TQ_CUS_CSAT = $Internet_KHL_TQ_CSAT
            = 0;
        foreach ($detailCSAT['surveyCSATService12'] as $key => $value) {
            $Internet_TQ_CSAT1 += $value->INTERNET_CSAT_1;
            $Internet_TQ_CSAT2 += $value->INTERNET_CSAT_2;
            $Internet_TQ_CSAT12 += $value->INTERNET_CSAT_12;
            $Internet_TQ_CUS_CSAT += $value->TOTAL_INTERNET_CUS_CSAT;
            $Internet_TQ_CSAT += $value->TOTAL_INTERNET_CSAT;

            $Internet_SBT_TQ_CSAT1 += $value->INTERNET_SBT_CSAT_1;
            $Internet_SBT_TQ_CSAT2 += $value->INTERNET_SBT_CSAT_2;
            $Internet_SBT_TQ_CSAT12 += $value->INTERNET_SBT_CSAT_12;
            $Internet_SBT_TQ_CUS_CSAT += $value->TOTAL_SBT_INTERNET_CUS_CSAT;
            $Internet_SBT_TQ_CSAT += $value->TOTAL_SBT_INTERNET_CSAT;

            $Internet_KHL_TQ_CSAT1 += $value->INTERNET_CSAT_1 + $value->INTERNET_SBT_CSAT_1;
            $Internet_KHL_TQ_CSAT2 += $value->INTERNET_CSAT_2 + $value->INTERNET_SBT_CSAT_2;
            $Internet_KHL_TQ_CSAT12 += $value->INTERNET_CSAT_12 + $value->INTERNET_SBT_CSAT_12;
            $Internet_KHL_TQ_CUS_CSAT += $value->TOTAL_INTERNET_CUS_CSAT + $value->TOTAL_SBT_INTERNET_CUS_CSAT;
            $Internet_KHL_TQ_CSAT += $value->TOTAL_INTERNET_CSAT + $value->TOTAL_SBT_INTERNET_CSAT;
            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->section_location);
                $this->setTitleBodyTable($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->INTERNET_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                $rateNotSastisfied = (($value->TOTAL_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_CSAT_12 / $value->TOTAL_INTERNET_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_INTERNET_CSAT / $value->TOTAL_INTERNET_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('G' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SBT_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('H' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SBT_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('I' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_SBT_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_SBT_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SBT_CSAT_12 / $value->TOTAL_SBT_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('K' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_SBT_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SBT_INTERNET_CSAT / $value->TOTAL_SBT_INTERNET_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('L' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CSAT_1 + $value->INTERNET_SBT_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('M' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CSAT_2 + $value->INTERNET_SBT_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('N' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->INTERNET_CSAT_12 + $value->INTERNET_SBT_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('O' . $rowStart, function ($cell) use ($value, $detailCSAT) {
                    $sumTotal = $value->TOTAL_INTERNET_CUS_CSAT + $value->TOTAL_SBT_INTERNET_CUS_CSAT;
                    $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->INTERNET_CSAT_12 + $value->INTERNET_SBT_CSAT_12) / $sumTotal) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('P' . $rowStart, function ($cell) use ($value) {
                    $sumTotal = $value->TOTAL_INTERNET_CUS_CSAT + $value->TOTAL_SBT_INTERNET_CUS_CSAT;
                    $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_INTERNET_CSAT + $value->TOTAL_SBT_INTERNET_CSAT) / $sumTotal, 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart++;
        }
        $sheet->cell('A' . $rowStart, function ($cell) {
            $cell->setValue(trans('report.WholeCountry'));
            $this->setTitleBodyTable($cell);
            $this->setTitleMainRow($cell);
        })->cell('B' . $rowStart, function ($cell) use ($Internet_TQ_CSAT1) {
            $cell->setValue($Internet_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('C' . $rowStart, function ($cell) use ($Internet_TQ_CSAT2) {
            $cell->setValue($Internet_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('D' . $rowStart, function ($cell) use ($Internet_TQ_CSAT12) {
            $cell->setValue($Internet_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('E' . $rowStart, function ($cell) use ($Internet_TQ_CUS_CSAT, $Internet_TQ_CSAT12) {

            $rateNotSastisfied = (($Internet_TQ_CUS_CSAT) != 0) ? round(($Internet_TQ_CSAT12 / $Internet_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('F' . $rowStart, function ($cell) use ($Internet_TQ_CUS_CSAT, $Internet_TQ_CSAT) {
            $csatAverage = (($Internet_TQ_CUS_CSAT) != 0) ? round(($Internet_TQ_CSAT / $Internet_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })
            ->cell('G' . $rowStart, function ($cell) use ($Internet_SBT_TQ_CSAT1) {
                $cell->setValue($Internet_SBT_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('H' . $rowStart, function ($cell) use ($Internet_SBT_TQ_CSAT2) {
                $cell->setValue($Internet_SBT_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('I' . $rowStart, function ($cell) use ($Internet_SBT_TQ_CSAT12) {
                $cell->setValue($Internet_SBT_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('J' . $rowStart, function ($cell) use ($Internet_SBT_TQ_CUS_CSAT, $Internet_SBT_TQ_CSAT12) {
                $this->setTitleMainRow($cell);
                $rateNotSastisfied = (($Internet_SBT_TQ_CUS_CSAT) != 0) ? round(($Internet_SBT_TQ_CSAT12 / $Internet_SBT_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('K' . $rowStart, function ($cell) use ($Internet_SBT_TQ_CUS_CSAT, $Internet_SBT_TQ_CSAT) {
                $csatAverage = (($Internet_SBT_TQ_CUS_CSAT) != 0) ? round(($Internet_SBT_TQ_CSAT / $Internet_SBT_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })
            ->cell('L' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT1) {
                $cell->setValue($Internet_KHL_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('M' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT2) {
                $cell->setValue($Internet_KHL_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('N' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CSAT12) {
                $cell->setValue($Internet_KHL_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('O' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CUS_CSAT, $Internet_KHL_TQ_CSAT12) {
                $rateNotSastisfied = (($Internet_KHL_TQ_CUS_CSAT) != 0) ? round(($Internet_KHL_TQ_CSAT12 / $Internet_KHL_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('P' . $rowStart, function ($cell) use ($Internet_KHL_TQ_CUS_CSAT, $Internet_KHL_TQ_CSAT) {
                $csatAverage = (($Internet_KHL_TQ_CUS_CSAT) != 0) ? round(($Internet_KHL_TQ_CSAT / $Internet_KHL_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            });
        return $rowStart;
    }

    //Tạo bảng thống kê csat12 nhân viên theo khu vực, đã rút gọn
    public function createDetailStaffCsat12Location($sheet, $detailCSAT, $rowIndex)
    {
        $sheet->mergeCells('A' . ($rowIndex) . ':B' . $rowIndex)->setWidth('A', 68)->cell('A' . $rowIndex, function ($cell) {
            $cell->setValue('5. ' . trans('report.StatisticalOfUnsatisfactionCustomerForStaff'));
            $this->setTitleTable($cell);
        })->setOrientation('landscape')->setWidth('A', 40)->cell('A' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.TouchPoint'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('A', 40)->cell('A' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Staff'));
            $this->setTitleHeaderTable($cell);
        })->setWidth('A', 40)->cell('A' . ($rowIndex + 3), function ($cell) {
            $cell->setValue(trans('report.Location'));
            $this->setTitleHeaderTable($cell);
        })->mergeCells('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1))->cell('B' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Deployment'));
            $this->setTitleHeaderTable($cell);
        })->cell('B' . ($rowIndex + 1) . ':K' . ($rowIndex + 1), function ($cell) {
            $this->setTitleHeaderTable($cell);
        })->mergeCells('L' . ($rowIndex + 1) . ':P' . ($rowIndex + 1))->cell('L' . ($rowIndex + 1), function ($cell) {
            $cell->setValue(trans('report.Maintenance'));
            $this->setTitleHeaderTable($cell);
        });
        $this->extraFunc->setColumnByFormat('C', 15, $sheet, $rowIndex + 1, 'thin-thin-thin-thin');


        $sheet->mergeCells('B' . ($rowIndex + 2) . ':F' . ($rowIndex + 2))->cell('B' . ($rowIndex + 2), function ($cell) {
            $cell->setBorder('none', 'thin', 'thin', 'thin');
            $cell->setBackground('#8DB4E2');
        })->cell('B' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Saler'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setFontWeight('bold');
            $cell->setBorder('none', 'thin', 'none', 'thin');
        })->cell('F' . ($rowIndex + 2), function ($cell) {
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setFontWeight('bold');
            $cell->setBorder('none', 'thin', 'none', 'thin');
        })->mergeCells('G' . ($rowIndex + 2) . ':K' . ($rowIndex + 2))->cell('G' . ($rowIndex + 2), function ($cell) {
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('none', 'none', 'none', 'none');
        })->cell('G' . ($rowIndex + 2), function ($cell) {
            $cell->setValue(trans('report.Deployer'));
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setBackground('#8DB4E2');
            $cell->setBorder('none', 'none', 'none', 'none');
            $cell->setFontWeight('bold');
        })
            ->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setValue(trans('report.MaintainanceStaff'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->cell('P' . ($rowIndex + 2), function ($cell) {
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setFontWeight('bold');
                $cell->setBorder('none', 'thin', 'none', 'thin');
            })->mergeCells('L' . ($rowIndex + 2) . ':P' . ($rowIndex + 2))->cell('L' . ($rowIndex + 2), function ($cell) {
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setAlignment('center');
                $cell->setValignment('center');
            })->setWidth('B', 20)->cell('B' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('C', 20)->cell('C' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('D', 20)->cell('D' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('E', 20)->cell('E' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('F', 20)->cell('F' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('G', 20)->cell('G' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('H', 20)->cell('H' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('I', 20)->cell('I' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('J', 20)->cell('J' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('K', 20)->cell('K' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('L', 20)->cell('L' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 1');
                $this->setTitleHeaderTable($cell);
            })->setWidth('M', 20)->cell('M' . ($rowIndex + 3), function ($cell) {
                $cell->setValue('CSAT 2');
                $this->setTitleHeaderTable($cell);
            })->setWidth('N', 20)->cell('N' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.TotalCsat12'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('O', 20)->cell('O' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.RatioOfSatisfaction'));
                $this->setTitleHeaderTable($cell);
            })->setWidth('P', 20)->cell('P' . ($rowIndex + 3), function ($cell) {
                $cell->setValue(trans('report.AverageCsat'));
                $this->setTitleHeaderTable($cell);
            });
        $rowStart = $rowIndex + 4;
        $NVKD_TQ_CSAT1 = $NVKD_TQ_CSAT2 = $NVKD_TQ_CSAT12 = $NVKD_TQ_CUS_CSAT = $NVKD_TQ_CSAT
            = $NVTK_TQ_CSAT1 = $NVTK_TQ_CSAT2 = $NVTK_TQ_CSAT12 = $NVTK_TQ_CUS_CSAT = $NVTK_TQ_CSAT
            = $NVBT_TQ_CSAT1 = $NVBT_TQ_CSAT2 = $NVBT_TQ_CSAT12 = $NVBT_TQ_CUS_CSAT = $NVBT_TQ_CSAT
            = 0;
        foreach ($detailCSAT['surveyCSAT12'] as $key => $value) {
            $NVKD_TQ_CSAT1 += $value->NVKD_CSAT_1;
            $NVKD_TQ_CSAT2 += $value->NVKD_CSAT_2;
            $NVKD_TQ_CSAT12 += $value->NVKD_CSAT_12;
            $NVKD_TQ_CUS_CSAT += $value->TOTAL_NVKD_CUS_CSAT;
            $NVKD_TQ_CSAT += $value->TOTAL_NVKD_CSAT;

            $NVTK_TQ_CSAT1 += $value->NVTK_CSAT_1;
            $NVTK_TQ_CSAT2 += $value->NVTK_CSAT_2;
            $NVTK_TQ_CSAT12 += $value->NVTK_CSAT_12;
            $NVTK_TQ_CUS_CSAT += $value->TOTAL_NVTK_CUS_CSAT;
            $NVTK_TQ_CSAT += $value->TOTAL_NVTK_CSAT;

            $NVBT_TQ_CSAT1 += $value->NVBT_CSAT_1;
            $NVBT_TQ_CSAT2 += $value->NVBT_CSAT_2;
            $NVBT_TQ_CSAT12 += $value->NVBT_CSAT_12;
            $NVBT_TQ_CUS_CSAT += $value->TOTAL_NVBT_CUS_CSAT;
            $NVBT_TQ_CSAT += $value->TOTAL_NVBT_CSAT;

            $sheet->cell('A' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->section_location);
                $this->setTitleBodyTable($cell);
            })->cell('B' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('C' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('D' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVKD_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('E' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                $rateNotSastisfied = (($value->TOTAL_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_CSAT_12 / $value->TOTAL_NVKD_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('F' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_NVKD_CSAT / $value->TOTAL_NVKD_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('G' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_CSAT_1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('H' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_CSAT_2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('I' . $rowStart, function ($cell) use ($value) {
                $cell->setValue($value->NVTK_CSAT_12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('J' . $rowStart, function ($cell) use ($value, $detailCSAT) {


                $rateNotSastisfied = (($value->TOTAL_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_CSAT_12 / $value->TOTAL_NVTK_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })->cell('K' . $rowStart, function ($cell) use ($value) {
                $csatAverage = (($value->TOTAL_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_NVTK_CSAT / $value->TOTAL_NVTK_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
            })
                ->cell('L' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_CSAT_1);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('M' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_CSAT_2);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('N' . $rowStart, function ($cell) use ($value) {
                    $cell->setValue($value->NVBT_CSAT_12);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('O' . $rowStart, function ($cell) use ($value, $detailCSAT) {

                    $rateNotSastisfied = (($value->TOTAL_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_CSAT_12 / $value->TOTAL_NVBT_CUS_CSAT) * 100, 2) : 0;
                    $cell->setValue($rateNotSastisfied . "%");
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                })->cell('P' . $rowStart, function ($cell) use ($value) {
                    $csatAverage = (($value->TOTAL_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_NVBT_CSAT / $value->TOTAL_NVBT_CUS_CSAT), 2) : 0;
                    $cell->setValue($csatAverage);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $this->setBorderCell($cell);
                });
            $rowStart++;
        }
        $sheet->cell('A' . $rowStart, function ($cell) {
            $cell->setValue(trans('report.WholeCountry'));
            $this->setTitleBodyTable($cell);
            $this->setTitleMainRow($cell);
        })->cell('B' . $rowStart, function ($cell) use ($NVKD_TQ_CSAT1) {
            $cell->setValue($NVKD_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('C' . $rowStart, function ($cell) use ($NVKD_TQ_CSAT2) {
            $cell->setValue($NVKD_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('D' . $rowStart, function ($cell) use ($NVKD_TQ_CSAT12) {
            $cell->setValue($NVKD_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('E' . $rowStart, function ($cell) use ($NVKD_TQ_CUS_CSAT, $NVKD_TQ_CSAT12) {

            $rateNotSastisfied = (($NVKD_TQ_CUS_CSAT) != 0) ? round(($NVKD_TQ_CSAT12 / $NVKD_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('F' . $rowStart, function ($cell) use ($NVKD_TQ_CUS_CSAT, $NVKD_TQ_CSAT) {
            $csatAverage = (($NVKD_TQ_CUS_CSAT) != 0) ? round(($NVKD_TQ_CSAT / $NVKD_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('G' . $rowStart, function ($cell) use ($NVTK_TQ_CSAT1) {
            $cell->setValue($NVTK_TQ_CSAT1);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('H' . $rowStart, function ($cell) use ($NVTK_TQ_CSAT2) {
            $cell->setValue($NVTK_TQ_CSAT2);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('I' . $rowStart, function ($cell) use ($NVTK_TQ_CSAT12) {
            $cell->setValue($NVTK_TQ_CSAT12);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('J' . $rowStart, function ($cell) use ($NVTK_TQ_CUS_CSAT, $NVTK_TQ_CSAT12) {
            $rateNotSastisfied = (($NVTK_TQ_CUS_CSAT) != 0) ? round(($NVTK_TQ_CSAT12 / $NVTK_TQ_CUS_CSAT) * 100, 2) : 0;
            $cell->setValue($rateNotSastisfied . "%");
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })->cell('K' . $rowStart, function ($cell) use ($NVTK_TQ_CUS_CSAT, $NVTK_TQ_CSAT) {
            $csatAverage = (($NVTK_TQ_CUS_CSAT) != 0) ? round(($NVTK_TQ_CSAT / $NVTK_TQ_CUS_CSAT), 2) : 0;
            $cell->setValue($csatAverage);
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $this->setBorderCell($cell);
            $this->setTitleMainRow($cell);
        })
            ->cell('L' . $rowStart, function ($cell) use ($NVBT_TQ_CSAT1) {
                $cell->setValue($NVBT_TQ_CSAT1);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('M' . $rowStart, function ($cell) use ($NVBT_TQ_CSAT2) {
                $cell->setValue($NVBT_TQ_CSAT2);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('N' . $rowStart, function ($cell) use ($NVBT_TQ_CSAT12) {
                $cell->setValue($NVBT_TQ_CSAT12);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('O' . $rowStart, function ($cell) use ($NVBT_TQ_CUS_CSAT, $NVBT_TQ_CSAT12) {

                $rateNotSastisfied = (($NVBT_TQ_CUS_CSAT) != 0) ? round(($NVBT_TQ_CSAT12 / $NVBT_TQ_CUS_CSAT) * 100, 2) : 0;
                $cell->setValue($rateNotSastisfied . "%");
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            })->cell('P' . $rowStart, function ($cell) use ($NVBT_TQ_CUS_CSAT, $NVBT_TQ_CSAT) {
                $csatAverage = (($NVBT_TQ_CUS_CSAT) != 0) ? round(($NVBT_TQ_CSAT / $NVBT_TQ_CUS_CSAT), 2) : 0;
                $cell->setValue($csatAverage);
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $this->setBorderCell($cell);
                $this->setTitleMainRow($cell);
            });
        return $rowStart;
    }

    public function createCsatErrorActionReport($sheet, $dataCsat, $rowStart4)
    {
        //Tạo khung CSAT
        $sheet->cell('A' . $rowStart4, function ($cell) {
            $cell->setValue('2. ' . trans('report.CustomerFeedbacksandHandlingsolutionsofCCagentsforCSAT12ofInternetQualityService'));
            $this->setTitleTable($cell);
        })->setWidth('A', 20)->setWidth('B', 30)->setWidth('C', 30)->setWidth('D', 30)->setWidth('E', 30)->setWidth('F', 30)->setWidth('G', 30)->setWidth('H', 30)->setWidth('I', 30)
            ->setWidth('J', 30)->setWidth('K', 30)->setWidth('L', 30)->setWidth('M', 30)->setWidth('N', 30)->setWidth('O', 30)->setWidth('P', 30)->setWidth('Q', 30)
            ->setWidth('R', 30)->setWidth('S', 30)->setWidth('T', 30)->setWidth('U', 30)
            ->mergeCells('A' . ($rowStart4 + 1) . ':A' . ($rowStart4 + 2))->cell('A' . ($rowStart4 + 1), function ($cell) {
                $cell->setValue(trans('report.Location'));
                $this->setTitleHeaderTable($cell);
            })->cell('A' . ($rowStart4 + 2), function ($cell) {

                $cell->setBorder('none', 'thin', 'none', 'none');
            })->cell('A' . ($rowStart4 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'none', 'none');
            })->mergeCells('B' . ($rowStart4 + 1) . ':N' . ($rowStart4 + 1))->cell('B' . ($rowStart4 + 1), function ($cell) {
                $cell->setValue(trans('report.CustomerFeedbacksrecordedbyCCagents'));
                $this->setTitleHeaderTable($cell);
            })->cell('B' . ($rowStart4 + 1) . ':N' . ($rowStart4 + 1), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->mergeCells('O' . ($rowStart4 + 1) . ':U' . ($rowStart4 + 1))->cell('O' . ($rowStart4 + 1), function ($cell) {
                $cell->setValue(trans('report.HandlingsolutionsofCCagents'));
                $this->setTitleHeaderTable($cell);
            })->cell('O' . ($rowStart4 + 1) . ':U' . ($rowStart4 + 1), function ($cell) {
                $this->setTitleHeaderTable($cell);
            })->cell('B' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.InternetIsNotStable'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })->cell('C' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.EquipmentError'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('D' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.VoiceError'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('E' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.WifiWeakNotStable'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('F' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.GameLagging'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('G' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.CannotUsingWifi'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('H' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.LoosingSignal'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('I' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.HaveSignalButCannotAccess'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('J' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.SlowInternet'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('K' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.SignalIsNotStableSignalLoosingIsUnderStandard'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('L' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.IntenationalInternetSlow'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('M' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Other'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('N' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Total'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('O' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.SorryCustomerAndClose'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('P' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.ForwardDepartment'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('Q' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.CreatePrechecklist'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('R' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.CreateChecklist'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('S' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.CreateCLIndo'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('T' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Other'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('U' . ($rowStart4 + 2), function ($cell) {
                $cell->setValue(trans('report.Total'));
                $cell->setAlignment('center');
                $cell->setValignment('center');
                $cell->setBackground('#8DB4E2');
                $cell->setBorder('none', 'thin', 'none', 'none');
                $cell->setFontWeight('bold');
            })
            ->cell('G' . ($rowStart4 + 3), function ($cell) {

                $cell->setBorder('none', 'thin', 'none', 'none');
            });
        //Tạo row vùng
        $rowStart6 = $rowStart4 + 3;
        foreach ($dataCsat as $key => $val) {
            $sheet->cell('A' . $rowStart6, function ($cell) use ($val) {
                if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                    $this->setTitleMainRow($cell);
                } else {
                    $this->setTitleBodyTable($cell);
                }
                $cell->setValue($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)' ? trans('report' . '.' . $val['Location']) : $val['Location']);
            })->cell('C' . ($rowStart6), function ($cell) {

                $cell->setBorder('thin', 'none', 'thin', 'none');
            })
                ->cell('E' . ($rowStart6), function ($cell) {

                    $cell->setBorder('thin', 'none', 'thin', 'none');
                })
                ->cell('B' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }

                    $cell->setValue(isset($val['InternetIsNotStable']) ? $val['InternetIsNotStable'] : 0);
                })->cell('C' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['EquipmentError']) ? $val['EquipmentError'] : 0);
                })
                ->cell('D' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['VoiceError']) ? $val['VoiceError'] : 0);
                })
                ->cell('E' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['WifiWeakNotStable']) ? $val['WifiWeakNotStable'] : 0);
                })
                ->cell('F' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['GameLagging']) ? $val['GameLagging'] : 0);
                })
                ->cell('G' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['CannotUsingWifi']) ? $val['CannotUsingWifi'] : 0);
                })
                ->cell('H' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['LoosingSignal']) ? $val['LoosingSignal'] : 0);
                })
                ->cell('I' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['HaveSignalButCannotAccess']) ? $val['HaveSignalButCannotAccess'] : 0);
                })
                ->cell('J' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['SlowInternet']) ? $val['SlowInternet'] : 0);
                })
                ->cell('K' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['SignalIsNotStableSignalLoosingIsUnderStandard']) ? $val['SignalIsNotStableSignalLoosingIsUnderStandard'] : 0);
                })
                ->cell('L' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['IntenationalInternetSlow']) ? $val['IntenationalInternetSlow'] : 0);
                })
                ->cell('M' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['OtherError']) ? $val['OtherError'] : 0);
                })
                ->cell('N' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['TotalError']) ? $val['TotalError'] : 0);
                })
                ->cell('O' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['SorryCustomerAndClose']) ? $val['SorryCustomerAndClose'] : 0);
                })
                ->cell('P' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['ForwardDepartment']) ? $val['ForwardDepartment'] : 0);
                })
                ->cell('Q' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['CreatePrechecklist']) ? $val['CreatePrechecklist'] : 0);
                })
                ->cell('R' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['CreateChecklist']) ? $val['CreateChecklist'] : 0);
                })
                ->cell('S' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['CreateCLIndo']) ? $val['CreateCLIndo'] : 0);
                })
                ->cell('T' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['OtherAction']) ? $val['OtherAction'] : 0);
                })->cell('U' . $rowStart6, function ($cell) use ($val) {
                    if ($val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)') {
                        $this->setTitleMainRow($cell);
                    } else {
                        $this->setTitleBodyTable($cell);
                    }
                    $cell->setValue(isset($val['TotalAction']) ? $val['TotalAction'] : 0);
                });
            $rowStart6++;
        }
    }

    public function setTitleTable($cell)
    {
        $cell->setFontWeight('bold');
        $cell->setAlignment('left');
        $cell->setFontColor('#ff0000');
    }

    public function setTitleHeaderTable($cell)
    {
        $cell->setAlignment('center');
        $cell->setValignment('center');
        $cell->setBackground('#8DB4E2');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
        $cell->setFontWeight('bold');
    }

    public function setTitleBodyTable($cell)
    {
        $cell->setAlignment('center');
        $cell->setValignment('center');
        $cell->setBackground('#C5D9F1');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    public function setBorderCell($cell)
    {
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    public function setTitleMainRow($cell)
    {
        $cell->setFontWeight('bold');
        $cell->setAlignment('center');
        $cell->setFontColor('#ff0000');
        $cell->setBackground('#ffa500');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

}
