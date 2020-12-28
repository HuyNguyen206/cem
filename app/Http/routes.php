<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */
//Route Test
Route::post('/test/getVoiceData', 'Test\TestController@getVoiceData');
//

Route::get('/summary/getDataSummaryCsat/{fromDay}/{numDays}', 'Summary@getCsatSummary');
Route::get('/summary/getDataSummaryNps/{fromDay}/{numDays}', 'Summary@getNpsSummary');
Route::get('/summary/getDataSummaryOpinion/{fromDay}/{numDays}', 'Summary@getOpinionSummary');

Route::get('/test/updateObijId', 'Test\TestController@updateObijId');
Route::get('/test/testUpdateInvalidCase', 'Test\TestController@testUpdateInvalidCase');
Route::get('/test/getCsat12', 'Test\TestController@getCsat12');


Route::get('/get-hifpt-report/{day}/{dayTo}', 'Report@reportExcelHiFPT');
Route::get('/get-csat-report/{day}/{dayTo}', 'Report@repostCsatCustom');
//Route::get('/clearCache', 'Test\TestController@clearCache');
Route::get('/report-for-minh', 'Records\VoiceRecords@reportForMinh');
//Route::get('/redis', function () {
//    print_r(app()->make('redis'));
//});

Route::group(['prefix' => 'huy'], function () {
    Route::get('buildMailWeek', 'Cron\Cron@buildDataToSendCsatMailWeek');
    Route::get('buildMail', 'Cron\Cron@buildDataToSendCsatMail');
    Route::get('sendMail/{type}', 'Cron\Cron@sendCsatMail');
});


Route::group(['prefix' => 'api/v1'], function () {
    Route::post('get-survey', 'Api\Api@getResultSurveys');
    Route::post('save-survey', 'Api\Api@saveResultSurveys');
    Route::post('get-salary-IBB', 'Api\Api@getInfoSalaryIBB');
    Route::post('get-salary-TinPNC', 'Api\Api@getInfoSalaryTinPNC');
    Route::post('confirm-notification', 'Api\Api@saveReponseAcceptInfo');
    Route::get('resend-notification', 'Api\Api@sendNotificationAgain');
    Route::post('get-contract-info', 'Api\Api@getContractInfo');
    Route::get('fix-missed-surveys', 'Cron\Cron@fixMissedSurveys');

    Route::post('md5', 'Api\Api@supportMD5ForISC');
    //Route::post('generate-link-survey','Api\Api@generateLinkSurvey');

    Route::post('getContractCSAT', 'Api\Api@getInfoSurveyByContractNumber');
});

Route::group(['prefix' => 'api/survey'], function () {
    Route::post('get-info-transaction', 'Api\ApiTransactionController@getInfoContractQGD');
    Route::post('save-info-transaction', 'Api\ApiTransactionController@saveInfoTransaction');
});
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

Route::group(['middleware' => ['web', 'languageSwitch']], function () {
    Route::get('/test/', 'Test\TestController@test');

    Route::get('/getDataSummaryCsat', function () {
        $exitCode = Artisan::call('summaryData:Csat', [
                    'day' => "2017-03-16"
        ]);
    }
    );
    Route::get('/getDataSummaryNps', function () {
        $exitCode = Artisan::call('summaryData:Nps', [
                    'day' => "2017-03-16"
        ]);
    }
    );
    Route::group(['middleware' => 'beforeLogin'], function () {
        Route::auth();
    });

    Route::group(['prefix' => main_prefix], function () {
        Route::post('/csat-service/exportGeneral', 'Csat\CsatServiceController@generalExport');
        Route::post('/csat-staff/exportGeneral', 'Csat\CsatStaffController@generalExport');

        Route::post('/csat-service/exportDetail', 'Csat\CsatServiceController@detailExport');
        Route::post('/csat-staff/exportDetail', 'Csat\CsatStaffController@detailExport');
    });

    Route::group(['middleware' => 'manualLog'], function () {
        Route::get('/Falseinside/{input}', 'Users@falseInside');
        Route::get('/deny', 'Users@denyEmail');
        Route::get('/info-new-member', 'Users@newMemberInfo');
        Route::get('confirm-notification', 'Notification@confirmView');
        Route::post('confirm', 'Notification@confirm');
        Route::get('get-push-notification', 'Api\Api@getPushSurveyId');
        Route::post('get-voice-records-ajax', 'Records\VoiceRecords@getVoiceRecordsAjax');
    });

    Route::group(['middleware' => 'beforeError'], function() {
        Route::get('/error/auth', 'Error@auth');
        Route::delete('error/auth', 'Error@auth');
    });

    Route::resource('/history/export', 'History@exportSurvey');
    Route::get('lang/{locale}', 'DashboardController@setLocale');

    Route::group(['middleware' => ['beforeAction']], function() {
        Route::get('/', 'DashboardController@index');

        Route::group(['prefix' => main_prefix], function () {
            Route::get('/', 'DashboardController@index');          
            Route::get('get-voice-records/{id}', 'Records\VoiceRecords@getVoiceRecords');
            Route::get('exportToExcel', 'DashboardController@exportToExcel');
            Route::post('search-voice-records', 'Records\VoiceRecords@searchVoiceRecords');

            Route::post('/location/getLocationByRegion', 'LocationController@getLocationByRegion');

            Route::resource('roles', 'Roles');
            Route::resource('users', 'Users');
            Route::resource('permissions', 'Permissions');
            Route::post('update-permission', 'Permissions@update');
            Route::post('update-role', 'Roles@update');

            Route::get('/report', 'Report@index');
            Route::post('/report/detail_report', 'Report@detail_report');
            Route::post('/report/getLocationByRegion', 'Report@getLocationByRegion');
            Route::get('/report/exportToExcelReport', 'Report@exportToExcelReport');
            Route::post('/report/deleteExcelFile', 'Report@deleteExcelFile');

            Route::post('history/index', 'History@index');
            Route::get('history/index', 'History@index');
            Route::resource('history', 'History');
            Route::resource('/history/detail_survey', 'History@detail_survey');
            Route::resource('/history/get-time-survey', 'History@getTimeSurvey');
            Route::get('/history/exportfile', 'History@exportSurveyfile');
            Route::resource('/history/detail_violations', 'History@getViolations');
            Route::resource('/history/save-violation', 'History@saveViolations');

            Route::controller('complains', 'Complains\ComplainController');

            Route::get('/csat-service/detail', 'Csat\CsatServiceController@detail');
            Route::post('/csat-service/detail', 'Csat\CsatServiceController@detail');
            Route::get('/csat-service/general', 'Csat\CsatServiceController@general');
            Route::post('/csat-service/general', 'Csat\CsatServiceController@general');

            Route::get('/csat-staff/detail', 'Csat\CsatStaffController@detail');
            Route::post('/csat-staff/detail', 'Csat\CsatStaffController@detail');
            Route::get('/csat-staff/general', 'Csat\CsatStaffController@general');
            Route::post('/csat-staff/general', 'Csat\CsatStaffController@general');
            Route::resource('/csat-service/get-checklist-info', 'Csat\CsatServiceController@getChecklistInfo');

            Route::post('/authens/checkPersonRole', 'Authens@checkPersonRole');
            Route::group(['middleware' => 'createUniqueBreadcrumb'], function () {
                Route::get('/authens', 'Authens@getRolePermission');
                Route::get('/authens/view-role-permission', 'Authens@getRolePermission');
                Route::post('/authens/view-role-permission', 'Authens@saveRolePermission');
                Route::get('/authens/view-role-user', 'Authens@getRoleUser');
                Route::post('/authens/view-role-user', 'Authens@saveRoleUser');
                Route::get('/authens/view-user-permission', 'Authens@getUserPermission');
                Route::post('/authens/view-user-permission', 'Authens@saveUserPermission');
            });
        });
    });
});
