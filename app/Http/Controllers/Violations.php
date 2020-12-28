<?php

namespace App\Http\Controllers;

use App\Models\SurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use Illuminate\Support\Facades\Auth;

use App\Models\OutboundQuestions;
use App\Models\RecordChannel;
use App\Models\OutboundAnswers;

class Violations extends Controller {
    
    var $sales;
    var $deployer;
    var $maintenance;
    var $modelSurveySections;
    var $selNPSImprovement;
    var $modelRecordChannel;

    public function __construct()
    {
        $this->sales = 'sales';
        $this->deployer = 'deployer';
        $this->maintenance = 'maintenance';
        $this->modelSurveySections = new SurveySections();
        $this->modelRecordChannel = new RecordChannel();
        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9,10,11,12,13,14,15,16,17,18,19]);
    }

  
}
