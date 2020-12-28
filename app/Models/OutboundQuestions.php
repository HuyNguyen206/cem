<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class OutboundQuestions extends Model {

    protected $table = 'outbound_questions';
    protected $fillable = [
        'question_id', 'question_alias'
    ];
//*    $type loại khảo sát
//*    $aliasID dạng mảng truyền:

    public function getQuestionIDByType($type,$alias){
        $result = DB::table($this->table)
                ->select('question_id')
                ->whereIn('question_survey_id',$type)
                ->whereIn('question_alias', $alias)
                ->get();
        $questionList=[];
        foreach ($result as $key => $value) {
            array_push($questionList, $value->question_id);
        }
        return $questionList;
    }
    
    public function getQuestionAnswer($type)
    {
                 $ques = [];
        $listQues = DB::table('outbound_questions')
                ->select('question_id', 'question_answer_group_id')
                ->where('question_survey_id', $type)
                ->where('question_active', 1)
                ->get();
        foreach ($listQues as $key => $value) {
            $childQues=[];
            $childQues['ques']['id'] = $value->question_id;
            $listAns = DB::table('outbound_answers')
                    ->select('answer_id', 'answers_title','answers_point')
                    ->where('answer_group',  $value->question_answer_group_id)
                    ->get();
            $childAnsList=[];
            foreach ($listAns as $key2 => $value2) {               
                $childAns=[];
                $childAns['id']=$value2->answer_id;
                $childAns['title']=$value2->answers_title;
                $childAns['point']=$value2->answers_point;
//                $child['ques']['ans']['id']=$value2->answer_id;
//                 $child['ques']['ans']['title']=$value2->answers_title;
//                
                array_push($childAnsList, $childAns);
            }
             $childQues['ans']=$childAnsList;
            array_push($ques, $childQues);
        }
        return $ques;
    }

    public function getQuestionIDByParam($param){
        $sql = DB::table($this->table)
            ->select('question_id');
        if(isset($param['type'])){
            $sql->whereIn('question_survey_id',$param['type']);
        }
        if(isset($param['alias'])){
            $sql->whereIn('question_alias', $param['alias']);
        }
        $result = $sql->get();
        $questionList=[];
        foreach ($result as $key => $value) {
            array_push($questionList, $value->question_id);
        }
        return $questionList;
    }

    public function getQuestionByParam($param){
        $sql = DB::table($this->table);
        if(isset($param['type'])){
            $sql->whereIn('question_survey_id',$param['type']);
        }
        if(isset($param['alias'])){
            $sql->whereIn('question_alias', $param['alias']);
        }
        $result = $sql->get();
        return $result;
    }

    public function getAllQuestion(){
        $sql = DB::table($this->table);
        $result = $sql->get();
        return $result;
    }
}
