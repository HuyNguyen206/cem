<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class LogMG extends Eloquent {
	protected $collection = 'cem_logs';
	protected $connection = 'mongodb';
	
	protected $fillable = [
		'log_user', 'log_method', 'log_param', 'log_created_at', 'log_request', 'log_action','log_controller'
    ];
	
	public $timestamps = false;
}
