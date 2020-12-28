<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\SurveySections;
//use Illuminate\Support\Facades\DB;
use App\Models\CheckList;
use App\Models\PrecheckList;
use App\Models\Apiisc;
//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Redis;
use DB;

class UpdateChecklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:UpdateChecklist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update checklist data from inside';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {
//                DB::enableQueryLog();
            $listCLID = [];
            $checklist_update_later = Redis::get('checklist_update_later');
            if ($checklist_update_later != null) {
                $arrayDayUpdateData = json_decode($checklist_update_later);
                //Ngày mới
                if (date('y-m-d') != $arrayDayUpdateData->time) {
                    Redis::del('checklist_update_later');
                } else {
                    $listCLID = $arrayDayUpdateData->listCL;
                }
            }
            $listCL = DB::table('checklist')
                ->select('id_checklist_isc')
                ->where(function($query) {
                    $query->whereNotNull('id_checklist_isc');
                    $query->where('id_checklist_isc', '<>', 0);
                })
                ->where(function($query) {
                    $query->whereNotIn('final_status_id', [1, 97, 98, 99, 100, 11, 3]);
                    $query->WhereNotNull('final_status_id');
                })
                ->where(function($query) use ($listCLID) {
                    if (!empty($listCLID))
                        $query->whereNotIn('id_checklist_isc', $listCLID);
                })
                ->orderBy('created_at', 'ASC')
                ->limit(20)
//                    ->offset($offset * 20)
//                    ->tosql();
//            dump($listCL);die;
                ->get();
//                    dump(DB::getQueryLog());die;
            $TotalCl = count($listCL);
            //Có dữ liệu cần cập nhập
            if ($TotalCl > 0) {
                $listCLID = [];
                for ($i = 0; $i <= $TotalCl - 1; $i++) {
                    array_push($listCLID, $listCL[$i]->id_checklist_isc);
                }
                $listCL_update_later = $this->updateCLData($listCLID);
                if (!empty($listCL_update_later)) {
                    $checklist_update_later = Redis::get('checklist_update_later');
                    //Ngày mới hoặc lần đầu chạy
                    if ($checklist_update_later == null) {
                        Redis::set('checklist_update_later', json_encode(['time' => date('y-m-d'), 'listCL' => $listCL_update_later]));
                    }
                    //Ngày cũ
                    else {
                        $DayUpdateData = json_decode($checklist_update_later);
                        $addListCLData = array_merge($DayUpdateData->listCL, $listCL_update_later);
                        Redis::set('checklist_update_later', json_encode(['time' => date('y-m-d'), 'listCL' => $addListCLData]));
                    }
                }
//                if($result)
//                return json_encode(['code' => 200, 'status' => 'Thành công', 'msg' => 'Cập nhập Checklist thành công']);
//                else
            }
            return json_encode(['code' => 200, 'status' => 'Thành công', 'msg' => 'Dữ liệu Checklist đã được cập nhập đầy đủ ']);
        } catch (Exception $e) {
            return json_encode(['code' => 500, 'status' => 'Lỗi', 'msg' => $e->getMessage()]);
        }
    }

    //Gọi qua api ISC để cập nhập dữ liệu checklist
    public function updateCLData($listCLID) {
        try {
            $listCL_update_later = [];
            $listCLIDString = implode(',', $listCLID);
            $listCLIDString = array('ChecklistID' => $listCLIDString
            );
            $uri = 'http://parapi.fpt.vn/api/RadAPI/SupportListGetByCLID/?';
            $uri .= http_build_query($listCLIDString);
            $apiISC = new Apiisc();
            $resultSetClData = json_decode($apiISC->getAPI($uri));

            if ($resultSetClData->statusCode == 200) {

                $checklistUpdate = new CheckList();
                foreach ($resultSetClData->data as $key => $value) {
                    $checklistUpdate = CheckList::where('id_checklist_isc', '=', $value->Id)->get();
//                    if ($checklistUpdate->final_status_id != 1 && $checklistUpdate->final_status_id != 97) {
                    foreach ($checklistUpdate as $key2 => $value2) {
                        //Cần cập nhập
                        if (in_array($value->Final_Status_Id, [1, 97, 98, 99, 100, 11, 3])) {
//                            $updatable = true;
                            $value2->final_status = isset($value->Final_Status) ? $value->Final_Status : NULL;
                            $value2->final_status_id = isset($value->Final_Status_Id) ? $value->Final_Status_Id : NULL;
                            $value2->total_minute = isset($value->TongSoPhut) ? $value->TongSoPhut : NULL;
                            $value2->input_time = isset($value->ThoiGianNhap) ? $value->ThoiGianNhap : NULL;
                            $value2->assign = isset($value->Phancong) ? $value->Phancong : NULL;
                            $value2->store_time = isset($value->ThoiGianTon) ? $value->ThoiGianTon : NULL;
                            $value2->error_position = isset($value->ViTriLoi) ? $value->ViTriLoi : NULL;
                            $value2->error_description = isset($value->MotaLoi) ? $value->MotaLoi : NULL;
                            $value2->reason_description = isset($value->NguyenNhan) ? $value->NguyenNhan : NULL;
                            $value2->way_solving = isset($value->HuongXuLy) ? $value->HuongXuLy : NULL;
                            $value2->checklist_type = isset($value->LoaiCl) ? $value->LoaiCl : NULL;
                            $value2->repeat_checklist = isset($value->CLlap) ? $value->CLlap : NULL;
                            $value2->finish_date = isset($value->FinishDate) ? $value->FinishDate : NULL;
                            $value2->save();
                        } else {
                            if (!in_array($value->Id, $listCL_update_later)) {
                                array_push($listCL_update_later, $value->Id);
                            }
                        }
                    }
                }
                return $listCL_update_later;

//                    }
            }
        } catch (Exception $e) {
            return json_encode(['code' => 500, 'status' => 'Lỗi', 'msg' => $e->getMessage()]);
//            $logger = new Logger('my_logger');
//            $logger->pushHandler(new StreamHandler(storage_path() . '/logs/API_ISC_Update_Checklist_Error.log', Logger::INFO));
//            $logger->addInfo('Log Call API', array('TimeStartCall' => new \DateTime(), 'input' => $uri, 'output' => $resultSetClData, 'error' => $e->getMessage()));
        }
    }
}
