<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ContactProfile extends Model {

    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_profile';
    protected $fillable = ['contact_id', 'contact_name', 'contact_phone', 'contact_relationship', 'contact_user_created', 'account_id', 'contact_user_name_create'];

    /*
     * save contact profile
     */

    public function saveContactProfile($infoSave, $accountId, $userId, $userName) {
        try {
            $result = ContactProfile::updateOrCreate(['contact_phone' => $infoSave['phone'], 'account_id' => $accountId], [ 'contact_name' => isset($infoSave['name']) ? $infoSave['name'] : '',
                    'contact_phone' => isset($infoSave['phone']) ? $infoSave['phone'] : '',
                    'contact_relationship' => isset($infoSave['relationship']) ? $infoSave['relationship'] : '',
//                    'contact_last_connected' => date('Y-m-d H:i:s'),
                    'contact_user_created' => isset($userId) ? $userId : '',
                    'account_id' => isset($accountId) ? $accountId : '',
                    'contact_user_name_create' => isset($userName) ? $userName : '',
            ]);

            $res['code'] = 200;
            $res['msg'] = 'Successful';
            $res['data'] = $result;
            return $res;
        } catch (\Exception $ex) {
            $res['code'] = 400;
            $res['msg'] = $ex->getMessage();
            $res['data'] = '';
            return $res;
        }
    }

    /*
     * lấy thông tin người liên hệ dựa vào account id
     */

    public function getContactByID($id, $limit = 0) {
        $result = DB::table($this->table)->where('account_id', '=', $id)
            ->orderBy('updated_at', 'DESC');
        if (!empty($limit) && is_numeric($limit)) {
            $result->limit($limit);
        }
        $result = $result->get();
        if (!empty($result))
            return $result;
        return NULL;
    }

}
