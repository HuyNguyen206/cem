
app.controller('accountController', function ($scope, $state, $filter, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, $stateParams, domain, $location, $window) {
// surveytypes : các khảo sát. sau này có nhiều khảo sát thì phải truy xuất
// database
// $scope.surveytypes = [
// {id:'1', title:'Sau active'},
// {id:'2', title:'Sau checklist'}API_URL
// ];

    /*
     * Kiểm tra xem đã có số hợp đồng chưa
     * Nếu chưa có hiển thị dialog kêu cầu nhập 
     */
// in controller

//    if (angular.isUndefined($scope.sohd)) {
//        $scope.searchStatus = 'Vui lòng nhập số hợp đồng';
//        $('#myModaldialog').modal('show');
//    }

//Chỉ cho phép khởi tạo controller gắn với các state
    if (typeof $stateParams.sohdISC === 'undefined' && typeof $state.current.data === 'undefined') {
        return;
    }
    console.log($state.current.data.state);
    $scope.account = {};
    $scope.contact = {};
    $scope.saveAccount = function (ev) {
        // Appending dialog to document.body to cover sidenav in docs app
        if (angular.isUndefined($scope.account['ContractNum'])) {
//            $('#myModaldialog').modal('show');
            //alert('Vui lòng nhập số hợp đồng.');
//            return;
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Thông báo')
                    .textContent('Vui lòng qua Inside để nhập số hợp đồng.')
                    .ariaLabel('Alert Dialog Demo')
                    .ok('OK')
                    .targetEvent(ev)
                    );
            return;
        }
        console.log($scope.account);
        var confirm = $mdDialog.confirm()
                .title('Thông báo xác nhận?')
                .textContent('Lưu thông tin khách hàng')
                .ariaLabel('lưu thông tin khách hàng')
                .targetEvent(ev)
                .ok('Lưu')
                .cancel('Không');
        $mdDialog.show(confirm).then(function () {
            var url = API_URL + "account/save";
            var gender = $('.gender div .md-checked').attr('value');
            var internet = "";
            var paytv = "";
            if ($('.paytv').hasClass('md-checked'))
                paytv += $('.paytv').attr('title');
            if ($('.isp').hasClass('md-checked'))
            {
                internet += $('.isp').attr('title');
            }
            $scope.account['paytv'] = paytv;
            $scope.account['internet'] = internet;
            $scope.account['gender'] = gender;

            $http({
                method: 'POST',
                url: url,
                data: {datapost: $scope.account, contract: $scope.sohd},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {

                if (angular.isDefined(response.code) && response.code === 200) {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Cập nhập thành công')
                            .textContent('Đã cập nhật vào hệ thống.')
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
                            .targetEvent(ev)
                            );
                } else if (angular.isDefined(response.code) && response.code === 500) {
                    $scope.sohd = undefined;
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Có lỗi xảy ra')
                            .textContent('Vui lòng nhập số hợp đồng.')
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
                            .targetEvent(ev)
                            );
                } else {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Lỗi')
                            .textContent('Có lỗi xảy ra')
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
                            .targetEvent(ev)
                            );
//                    console.log(response);
                }
            }).error(function (response) {
//                console.log(response);
                $mdDialog.show(
                        $mdDialog.alert()
                        .clickOutsideToClose(true)
                        .title('Lỗi')
                        .textContent('Lỗi hệ thống')
                        .ariaLabel('Alert Dialog Demo')
                        .ok('OK')
                        .targetEvent(ev)
                        );
            });
        }, function () {
            ;
        }
        );
    };
    /*
     /Hàm lấy thông tin số hợp đồng
     */
    $scope.getSurveyContent = function ($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, sohdType, type, codes)
    {
        var url = API_URL + "account/search";
        $http({
            method: 'POST',
            url: url,
            data: {sohd: sohdType, codes: codes, type: type},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {

            if (angular.isDefined(response.code)) {
                console.log(response);
//        		if( response.code == 403) {
//        			// chưa login
//            		window.location = 'http://mo.fpt.vn';
//        		}
//Chưa làm khảo sát
                if (response.code == 200) {

                    $scope.bandWidth = response.bandWidthInfo;
                    $scope.contact = response.infoContact;
//                    $scope.bandOn = false;
                    $('#myModaldialog').modal('hide');
                    if (angular.isDefined(response.NPS)) {
                        $scope.NPS = 1;
                    }
//                    $scope.account.ContractNum = response.data_cusinfo[0]['ContractNum'];
                    $scope.account = response.data_cusinfo[0];
                    $scope.section_time_start = response.section_time_start;
//                    console.log(response)
                    if (response.data_cusinfo[0]['UseService'] == 1)
                    {
                        $('.paytv').addClass('md-checked');
                        $scope.PackageSal = true;
                        $scope.ContractTypeName = false;
                    }
                    else if (response.data_cusinfo[0]['UseService'] == 2) {
                        $scope.ContractTypeName = true;
                        $scope.PackageSal = false;
                        $('.isp').addClass('md-checked');
                    }
                    else {
                        $scope.ContractTypeName = true;
                        $scope.PackageSal = true;
                        $('.isp').addClass('md-checked');
                        $('.paytv').addClass('md-checked');
                    }
//                    $scope.account.gender=response.data_cusinfo[0]['Sex'];
                    $scope.exist5 = $scope.exist7 = $scope.exist8 = $scope.exist6 = false;
                    $('.isp').attr('title', response.data_cusinfo[0]['ContractTypeName']);
                    $('.paytv').attr('title', response.data_cusinfo[0]['PackageSal']);
                    if (response.data_cusinfo[0]['CenterList'] == 'TIN/PNC')
                        $scope.account.AccountListTIN = response.data_cusinfo[0]['AccountList'];
                    else
                        $scope.account.AccountListINDO = response.data_cusinfo[0]['AccountList'];
                    if (type == 2) {
                        $('.after-maintance').addClass('md-checked')
                        $('.after-deploy').attr('disabled', 'disabled');
                        $scope.surveyid = 2;
                        //    loadsurvey($scope, 2);

                    }
                    else {
                        $('.after-deploy').addClass('md-checked')
                        $('.after-maintance').attr('disabled', 'disabled');
                        $scope.surveyid = 1;
                        //  loadsurvey($scope, 1);
                    }
                    //Hợp đồng sau triển khai
//                    if ($('.type-survey .md-checked').attr('value') == 1)
//                    {
//                        var res = response.data_cusinfo[0]['KindDeploy'].split("+");
//                        if (res.length == 2) {
//                            $scope.ContractTypeName = true;
//                            $scope.PackageSal = true;
//                        }
//                        else {
//                            if (response.data_cusinfo[0]['KindDeploy'] == 'Internet') {
//                                $scope.ContractTypeName = true;
//                                $scope.PackageSal = false;
//                            }
//                            else {
//                                $scope.ContractTypeName = false;
//                                $scope.PackageSal = true;
//                            }
//                        }
//                    }
//                    //Hợp đồng sau bảo trì
//                    else
//                    {
//                        var res = response.data_cusinfo[0]['KindMain'].split("+");
//                        if (res.length == 2) {
//                            $scope.ContractTypeName = true;
//                            $scope.PackageSal = true;
//                        }
//                        else {
//                            if (response.data_cusinfo[0]['KindMain'] == 'Internet') {
//                                $scope.ContractTypeName = true;
//                                $scope.PackageSal = false;
//                            }
//                            else {
//                                $scope.ContractTypeName = false;
//                                $scope.PackageSal = true;
//                            }
//                        }
//                    }
                    $('.box-select-box').css('display', 'inline-block');
                    $('.box-check-multi').css('display', 'inline-block');
                    $scope.history = response.data_history;
                    $scope.surveyhistory = response.outbound_history;
                    $scope.NPS = response.NPS;
//                    $scope.exist7=false;
                }
                //Quá 5 phút để sửa, chuyển sang xem chi tiết    
                else if (response.code == 650) {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
//                            .targetEvent(ev)
                            )
                            .finally(function () {
                                $state.go('history/detail', {contractNum: sohdType, idSurvey: response.idSur});
                            });
                }
                //Cho phép sửa
                else if (response.code == 600) {
                    $state.go('survey/edit', {idSurvey: response.idSur});
                }
                //Cho phép khảo sát lại
                else if (response.code == 700) {
                    $state.go('survey/retry', {idSurvey: response.idSur});
                }
                //Dữ liệu tương ứng với số HĐ trả về rỗng
                else {
                    $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
                    $('#myModaldialog').modal('show');
                    return;
                }

            } else {
                $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
                $('#myModaldialog').modal('show');
                return;
            }

        }).error(function (response) {
            if (response.code == 800)
            {
                $mdDialog.show(
                        $mdDialog.alert()
                        .clickOutsideToClose(true)
                        .title('Thông báo')
                        .textContent(response.msg)
                        .ariaLabel('Alert Dialog Demo')
                        .ok('OK')
//                            .targetEvent(ev)
                        )
                        .finally(function () {
                            location.reload();
                        });
            }
            console.log(response);

//            $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
//            $('#myModaldialog').modal('show');
//            return;
        });
    }
    /*
     * Tìm kiếm số hợp đồng nhập vào dialog
     */
    $scope.getInfoAccount = function (id) {
// kiểm tra xem có nhập hợp đồng hay không
        if (angular.isUndefined($scope.sohd)) {
            $scope.searchStatus = 'Vui lòng nhập số hợp đồng';
            return;
        }
//        $('#myModaldialog').modal('hide');
        $scope.getSurveyContent($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, $scope.sohd, type = 1);
// $http.get(API_URL + "account/search"+id)
// .success(function(response) {
// $scope.account = response;
// });

    }
// load survey
//	loadsurvey($scope, 1);
    /*
     * Tìm kiếm số hợp đồng theo URL, nếu state là "inputcontract" sẽ gửi request khi khởi tạo controller
     */
    if ($state.current.data.state == 'inputcontract') {
        console.log($stateParams.codedm);
        var sohdISC = $stateParams.sohdISC;
        var type = $stateParams.type;
        var codes = $stateParams.codedm;
//        console.log($stateParams);
        $scope.getSurveyContent($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, $stateParams.sohdISC, $stateParams.type, $stateParams.codedm);
    }
//    if ($state.current.data.state == 'outbound') {
//        $scope.searchStatus = 'Vui lòng nhập số hợp đồng';
//        $('#myModaldialog').modal('show');
//    }

    $scope.choosesurvey = function () {
        var item = $scope.surveyid;
        loadsurvey($scope, item);
    }
    $scope.loadSurveyContent = function () {
        loadsurvey($scope, $('.type-survey .md-checked').attr('value'));
    }

    function loadsurvey($scope, id) {
        if (id == 1) {
            var templateUrl = $sce.getTrustedResourceUrl("assets/outboundapp/templates/survey_sau_active.html?t=" + (new Date()).getTime());
        } else {
            var templateUrl = $sce.getTrustedResourceUrl("assets/outboundapp/templates/survey_sau_checklist.html?t=" + (new Date()).getTime());
        }

        $templateRequest(templateUrl).then(function (template) {
// template is the HTML template as a string

// Let's put it into an HTML element and parse any directives and
// expressions
// in the code. (Note: This is just an example, modifying the DOM
// from within
// a controller is considered bad style.)
            $compile($("#surveycontentID").html(template).contents())($scope);
        }, function () {
// An error has occurred
        });
    }
// end load survey

    $scope.showConfirmcomplete = function (ev, $window) {
        console.log($scope.survey);
//Kiểm tra các câu hỏi khảo sát có được đánh giá đủ hay chưa

//    	if (angular.isUndefined( $scope.survey.answer2)) {
//            alert('chưa chọn câu hỏi 3');
//            return;
//        }
//    	return;
        if (angular.isUndefined($scope.account.ContractNum)) {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent('Chưa nhập số hợp đồng')
                    .ariaLabel('Chưa nhập số hợp đồng')
                    .ok('OK')
                    .targetEvent(ev)
                    );
        }
        else {
            var msgError = '';
            if (angular.isUndefined($scope.survey.connected)) {
                msgError += 'Anh/Chị chưa chọn kết quả liên hệ';
//            } else if ($scope.survey.connected == 4 || ($state.current.data.state == 'edit-survey')) {
            }
            else if (angular.isUndefined($scope.contact.phone) || $scope.contact.phone == '' || $scope.contact.phone == null) {
                msgError += 'Anh/Chị vui lòng nhập số điện thoại liên hệ';
            } else if (isFinite(Number($scope.contact.phone)) === false) {
                msgError += 'Anh/Chị vui lòng nhập đúng định dạng số điện thoại liên hệ';
            }
            else if (angular.isUndefined($scope.contact.name) || $scope.contact.name == '' || $scope.contact.name == null) {
                msgError += 'Anh/Chị vui lòng nhập tên người liên hệ';
            }
            else if ($scope.survey.connected == 4) {
                //Tạo case cho các trường hợp
                var cases;
                //Sau triển khai
                if ($scope.surveyid == 1)
                {
                    if ($('.isp').hasClass('md-checked') && ($('.paytv').hasClass('md-checked') == false) && $scope.NPS == false)
                        //Có NPS, dịch vụ internet
                        cases = 101;
                    else if ($('.paytv').hasClass('md-checked') && ($('.isp').hasClass('md-checked') == false) && $scope.NPS == false)
                        //Có NPS, dịch vụ paytv
                        cases = 102;
                    else if ($('.paytv').hasClass('md-checked') && $('.isp').hasClass('md-checked') && $scope.NPS == false)
                        //Có NPS, dịch vụ paytv, internet
                        cases = 103;
                    else if ($('.isp').hasClass('md-checked') && ($('.paytv').hasClass('md-checked') == false) && $scope.NPS == true)
                        //Không có NPS, dịch vụ  internet
                        cases = 104;
                    else if ($('.paytv').hasClass('md-checked') && ($('.isp').hasClass('md-checked') == false) && $scope.NPS == true)
                        //Không có NPS, dịch vụ paytv
                        cases = 105;
                    else if ($('.isp').hasClass('md-checked') && $('.paytv').hasClass('md-checked') && $scope.NPS == true)
                        //Không có NPS, dịch vụ paytv, internet
                        cases = 106;
                }
                //Sau bảo trì
                else {
                    if ($('.isp').hasClass('md-checked') && ($('.paytv').hasClass('md-checked') == false) && $scope.NPS == false)
                        //Có NPS, dịch vụ internet
                        cases = 201;
                    else if ($('.paytv').hasClass('md-checked') && ($('.isp').hasClass('md-checked') == false) && $scope.NPS == false)
                        //Có NPS, dịch vụ paytv
                        cases = 202;
                    else if ($('.paytv').hasClass('md-checked') && $('.isp').hasClass('md-checked') && $scope.NPS == false)
                        //Có NPS, dịch vụ paytv, internet
                        cases = 203;
                    else if ($('.isp').hasClass('md-checked') && ($('.paytv').hasClass('md-checked') == false) && $scope.NPS == true)
                        //Không có NPS, dịch vụ  internet
                        cases = 204;
                    else if ($('.paytv').hasClass('md-checked') && ($('.isp').hasClass('md-checked') == false) && $scope.NPS == true)
                        //Không có NPS, dịch vụ paytv
                        cases = 205;
                    else if ($('.isp').hasClass('md-checked') && $('.paytv').hasClass('md-checked') && $scope.NPS == true)
                        //Không có NPS, dịch vụ paytv, internet
                        cases = 206;
                }

//                if (angular.isUndefined($scope.surveyid)) {
//                    msgError += 'Anh/Chị chưa chọn loại khảo sát';
//                }
//                if ($scope.surveyid == 1) {
////                    alert('sau trien khai')
//                    if ($('.paytv').hasClass('md-checked')) {
//                        console.log($scope.survey.answer11 + ',' + $scope.survey.subnote11);
//                        if ((angular.isUndefined($scope.survey.answer11) || $scope.survey.answer11 == false || $scope.survey.answer11 == -1) && (angular.isUndefined($scope.survey.subnote11) || $scope.survey.subnote11 == '' || $scope.survey.subnote11 == null))
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 1b';
//                    }
//                    if ($('.isp').hasClass('md-checked')) {
//                        if ((angular.isUndefined($scope.survey.answer10) || $scope.survey.answer10 == false || $scope.survey.answer10 == -1) && (angular.isUndefined($scope.survey.subnote10) || $scope.survey.subnote10 == '' || $scope.survey.subnote10 == null))
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 1a';
//                    }
//                    if ((angular.isUndefined($scope.survey.answer2) || $scope.survey.answer2 == false || $scope.survey.answer2 == -1) && (angular.isUndefined($scope.survey.subnote2) || $scope.survey.subnote2 == '' || $scope.survey.subnote2 == null)) {
//                        msgError += '\n Anh/Chị chưa chọn câu khảo sát số 2';
//                    }
//                    if ((angular.isUndefined($scope.survey.answer1) || $scope.survey.answer1 == false || $scope.survey.answer1 == -1) && (angular.isUndefined($scope.survey.subnote1) || $scope.survey.subnote1 == '' || $scope.survey.subnote1 == null)) {
//                        msgError += '\n Anh/Chị chưa chọn câu khảo sát số 3';
//                    }
//                    //Nếu có câu hỏi NPS
//                    if ($scope.NPS == false) {
//                        if ((angular.isUndefined($scope.survey.answer6) || $scope.survey.answer6 == false || $scope.survey.answer6 == -1) && (angular.isUndefined($scope.survey.subnote6) || $scope.survey.subnote6 == '' || $scope.survey.subnote6 == null) && (angular.isUndefined($scope.survey.extraQuestion6) || $scope.survey.extraQuestion6 == 0 || $scope.survey.extraQuestion6 == null)) {
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 4';
//                        }
//                    }
//
//                } else {
////                    alert('sau bao tri')
//                    if ($('.paytv').hasClass('md-checked')) {
//                        if ((angular.isUndefined($scope.survey.answer13) || $scope.survey.answer13 == false || $scope.survey.answer13 == -1) && (angular.isUndefined($scope.survey.subnote13) || $scope.survey.subnote13 == '' || $scope.survey.subnote13 == null))
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 1b';
//                    }
//                    if ($('.isp').hasClass('md-checked')) {
//                        if ((angular.isUndefined($scope.survey.answer12) || $scope.survey.answer12 == false || $scope.survey.answer12 == -1) && (angular.isUndefined($scope.survey.subnote12) || $scope.survey.subnote12 == '' || $scope.survey.subnote12 == null))
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 1a';
//                    }
//                    if ((angular.isUndefined($scope.survey.answer4) || $scope.survey.answer4 == false || $scope.survey.answer4 == -1) && (angular.isUndefined($scope.survey.subnote4) || $scope.survey.subnote4 == '' || $scope.survey.subnote4 == null)) {
//                        msgError += '\nAnh/Chị chưa chọn câu khảo sát số 2';
//                    }
//                    //Nếu có câu hỏi NPS
//                    if ($scope.NPS == false) {
//                        if ((angular.isUndefined($scope.survey.answer8) || $scope.survey.answer8 == false || $scope.survey.answer8 == -1) && (angular.isUndefined($scope.survey.subnote8) || $scope.survey.subnote8 == '') && (angular.isUndefined($scope.survey.extraQuestion8) || $scope.survey.extraQuestion8 == 0 || $scope.survey.extraQuestion8 == null)) {
//                            msgError += '\n Anh/Chị chưa chọn câu khảo sát số 3';
//                        }
//                    }
//                }
            }
            //Không khảo sát được khách hàng
            else
                cases = 0;
            if (msgError != '') {
                $mdDialog.show(
                        $mdDialog.alert()
                        .clickOutsideToClose(true)
                        .title('Lỗi xảy ra')
                        .textContent(msgError)
                        .ariaLabel('Alert Dialog Demo')
                        .ok('OK')
                        .targetEvent(ev)
                        );
            } else {
                console.log($scope.survey)
// Appending dialog to document.body to cover sidenav in docs app
                var confirm = $mdDialog.confirm()
                        .title('Thông báo xác nhận?')
                        .textContent('Hoàn thành khảo sát')
                        .ariaLabel('Lucky day')
                        .targetEvent(ev)
                        .ok('Vâng, Tôi muốn hoàn thành')
                        .cancel('Không');
                $mdDialog.show(confirm).then(function () {

                    var answer7 = $scope.survey.answer7;
                    var answer5 = $scope.survey.answer5;
//Trường hợp cập nhập khảo sát
//                    $scope.oneclick = true;
                    if ($state.current.data.state == 'edit-survey') {
//Lấy danh sách id các câu hỏi đã trả lời
                        var arrayAnswer = '';
                        if (angular.isUndefined($scope.survey.answer10) == false || angular.isUndefined($scope.survey.subnote10) == false)
                            arrayAnswer += "10 ";
                        if (angular.isUndefined($scope.survey.answer11) == false || angular.isUndefined($scope.survey.subnote11) == false)
                            arrayAnswer += "11 ";
                        if (angular.isUndefined($scope.survey.answer2) == false || angular.isUndefined($scope.survey.subnote2) == false)
                            arrayAnswer += "2 ";
                        if (angular.isUndefined($scope.survey.answer1) == false || angular.isUndefined($scope.survey.subnote1) == false)
                            arrayAnswer += "1 ";
                        if (angular.isUndefined($scope.survey.answer6) == false || angular.isUndefined($scope.survey.subnote6) == false || angular.isUndefined($scope.survey.extraQuestion6) == false)
                            arrayAnswer += "6 ";
                        if (angular.isUndefined($scope.survey.answer7) == false || angular.isUndefined($scope.survey.subnote7) == false)
                            arrayAnswer += "7 ";
                        if (angular.isUndefined($scope.survey.answer12) == false || angular.isUndefined($scope.survey.subnote12) == false)
                            arrayAnswer += "12 ";
                        if (angular.isUndefined($scope.survey.answer13) == false || angular.isUndefined($scope.survey.subnote13) == false)
                            arrayAnswer += "13 ";
                        if (angular.isUndefined($scope.survey.answer4) == false || angular.isUndefined($scope.survey.subnote4) == false)
                            arrayAnswer += "4 ";
                        if (angular.isUndefined($scope.survey.answer8) == false || angular.isUndefined($scope.survey.subnote8) == false || angular.isUndefined($scope.survey.extraQuestion8) == false)
                            arrayAnswer += "8 ";
                        if (angular.isUndefined($scope.survey.answer5) == false || angular.isUndefined($scope.survey.subnote5) == false)
                            arrayAnswer += "5 ";
                        var url = API_URL + "surveys/update";
//                        var cases = '';
                        var idS = $stateParams.idSurvey;
                        //Xem xét câu 10 điểm có trả lời hay không, không trả lời sẽ trả về -1
                        if ($scope.NPS == false) {
                            if ($scope.surveyid == 1) {
//                                ($scope.survey.answer7==-1)
                                if (angular.isObject($scope.survey.answer7)) {
                                    var check = 0;
                                    angular.forEach($scope.survey['answer7'], function (value, key) {
                                        if (value != false && value != '-1')
                                            check++;
                                    });
                                    if (check == 0)
                                        $scope.survey['answer7'] = -1;
                                    console.log($scope.survey['answer7']);
                                }
                            } else
                            {
                                if (angular.isObject($scope.survey.answer5)) {
                                    var check = 0;
                                    angular.forEach($scope.survey['answer5'], function (value, key) {
                                        if (value != false)
                                            check++;
                                    });
                                    if (check == 0)
                                        $scope.survey['answer5'] = -1;
                                    console.log($scope.survey['answer5']);
                                }
                            }
                        }
                    }
////Trường hợp lưu khảo sát
                    else
                    {


                        arrayAnswer = '';
                        var url = API_URL + "surveys/complete";
                        var idS = '';
                        if ($scope.NPS == false) {
                            if ($scope.surveyid == 1) {
                                if (angular.isUndefined($scope.survey.answer7))
                                {
                                    $scope.survey['answer7'] = -1;
                                }
                                else {
                                    var check = 0;
                                    angular.forEach($scope.survey['answer7'], function (value, key) {
                                        if (value != false)
                                            check++;
                                    });
                                    if (check == 0)
                                        $scope.survey['answer7'] = -1;
                                    console.log($scope.survey['answer7']);
                                }
                            } else
                            {
                                if (angular.isUndefined($scope.survey.answer5))
                                {
                                    $scope.survey['answer5'] = -1;
                                }
                                else {
                                    var check = 0;
                                    angular.forEach($scope.survey['answer5'], function (value, key) {
                                        if (value != false)
                                            check++;
                                    });
                                    if (check == 0)
                                        $scope.survey['answer5'] = -1;
                                    console.log($scope.survey['answer5']);
                                }
                            }
                        }
                    }
                    console.log()
//                    console.log($scope.survey);

                    $http({
                        method: 'POST',
                        url: url,
                        data: {datapost: $scope.survey, dataaccount: $scope.account, surveyid: $scope.surveyid, idS: idS, arrayAnswer: arrayAnswer, codes: $stateParams.codedm, time_start: $scope.section_time_start, cases: cases, phoneContact: $scope.contact.phone, dataContact: $scope.contact, contractNum: $scope.account.ContractNum},
                        headers: {'Content-Type': 'application/json'}
                    }).success(function (response) {

                        if (angular.isDefined(response.code) && (response.code == 200 || response.code == 300 || response.code == 400 || response.code == 600 || response.code == 700 || response.code == 800)) {
                            $scope.survey.answer7 = answer7;
                            $scope.survey.answer5 = answer5;
                            if (response.code == 800)
                            {
                                $mdDialog.show(
                                        $mdDialog.alert()
                                        .clickOutsideToClose(true)
                                        .title('Thông báo')
                                        .textContent(response.msg)
                                        .ariaLabel('Alert Dialog Demo')
                                        .ok('OK')
//                            .targetEvent(ev)
                                        )
                                scope.$apply(function () {
                                    $location.path(API_URL);
                                })
                            }
                            if (response.code == 700)
                            {
                                $mdDialog.show(
                                        $mdDialog.alert()
                                        .clickOutsideToClose(true)
                                        .title('Thông báo')
                                        .textContent(response.msg)
                                        .ariaLabel('Alert Dialog Demo')
                                        .ok('OK')
//                            .targetEvent(ev)
                                        )
                            }
                            if ($state.current.data.state == 'edit-survey') {
                                //Cập nhập thành công
                                if (response.code == 200) {
                                    var confirm = $mdDialog.confirm()
                                            .title('Thông báo')
                                            .textContent(response.msg)
                                            .ariaLabel(response.msg)
//                            .targetEvent(ev)
                                            .ok('Tiếp tục khảo sát')
                                            .cancel('Quay lại danh sách khảo sát');
                                    $mdDialog.show(confirm).then(function () {
                                        $state.go('outbound');
                                    }, function () {
                                        $state.go('history');
                                        ;
                                    }
                                    );
                                }
                                //Khảo sát lại, chỉnh sửa khảo sát bị lỗi
                                else if (response.code == 300)
                                {
                                    var confirm = $mdDialog.confirm()
                                            .title('Thông báo')
                                            .textContent(response.msg)
                                            .ariaLabel(response.msg)
//                            .targetEvent(ev)
                                            .ok('Gửi lại dữ liệu khảo sát')
                                            .cancel('Quay lại danh sách khảo sát');
                                    $mdDialog.show(confirm).then(function () {
                                        $scope.showConfirmcomplete();
                                    }, function () {
                                        $state.go('history');
                                        ;
                                    }
                                    );
                                }
                            }
                            //Tạo khảo sát
                            else {
                                // Khảo sát bị trùng lặp dữ liệu
                                if (response.code == 400)
                                {
                                    $mdDialog.show(
                                            $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('Thông báo')
                                            .textContent(response.msg)
                                            .ariaLabel('Alert Dialog Demo')
                                            .ok('OK')
//                                            .targetEvent(ev)
                                            );
                                    $state.go('outbound', {}, {reload: true});
                                }
                                //Tạo khảo sát bị lỗi
                                else if (response.code == 600) {
                                    var confirm = $mdDialog.confirm()
                                            .title('Thông báo')
                                            .textContent(response.msg)
                                            .ariaLabel(response.msg)
//                            .targetEvent(ev)
                                            .ok('Gửi lại dữ liệu khảo sát')
                                            .cancel('Quay lại danh sách khảo sát');
                                    $mdDialog.show(confirm).then(function () {
                                        $scope.showConfirmcomplete();
                                    }, function () {
                                        $state.go('history');
                                        ;
                                    }
                                    );
                                }
                                //Tạo khảo sát thành công
                                else if (response.code == 200)
                                {
                                    $mdDialog.show(
                                            $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('Thông báo')
                                            .textContent(response.msg)
                                            .ariaLabel('Alert Dialog Demo')
                                            .ok('OK')
//                            .targetEvent(ev)
                                            )
                                            .finally(function () {
                                                $state.go('history/detail', {contractNum: response.shd, idSurvey: response.ids});
                                            });
                                }

//                                $state.go('outbound')
//                                $state.reload();

//                                $window.location.reload();
                            }
                        } else {
                            alert("Có lỗi xảy ra");
                            console.log(response);
                        }
                    }).error(function (response) {
                        if (angular.isDefined(response.code) && response.code == 800)
                        {
                            $mdDialog.show(
                                    $mdDialog.alert()
                                    .clickOutsideToClose(true)
                                    .title('Thông báo')
                                    .textContent(response.msg)
                                    .ariaLabel('Alert Dialog Demo')
                                    .ok('OK')
//                            .targetEvent(ev)
                                    )
                                    .finally(function () {
                                        location.reload();
                                    });
                        }
                        else {
                            alert('Lỗi hệ thống');
                        }
                        console.log(response);
                    });
                }, function () {
                    ;
                });
            }
        }
    }
    $scope.pointUserGuide = function (ev) {
        var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
        $mdDialog.show({
            controller: DialogController,
            templateUrl: '/assets/outboundapp/templates/pointUserGuide.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: true,
            fullscreen: useFullScreen,
            scope: $scope, // kế thừa $scope controller parent (accountController)
            preserveScope: true,
        })
                .then(function (answer) {
                    $scope.status = 'You said the information was "' + answer + '".';
                }, function () {
                    $scope.status = 'You cancelled the dialog.';
                });
        $scope.$watch(function () {
            return $mdMedia('xs') || $mdMedia('sm');
        }, function (wantsFullScreen) {
            $scope.customFullscreen = (wantsFullScreen === true);
        });
    };
    function DialogController($scope, $mdDialog) {
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.closeUserGuidePoint = function () {
            $mdDialog.hide();
        };
//        $scope.updateContact = function () {//xử lý thêm thông tin người liên hệ
//            var arrInfo = [];
//            arrInfo.push({contactName: $scope.contactName, contactPhone: $scope.contactPhone, contactRelationship: $scope.contactRelationship, contractNum: $scope.account.ContractNum});
//            var url = API_URL + "surveys/addContact";
//            $http({// xử lý ajax 
//                method: 'POST',
//                url: url,
//                data: {datapost: arrInfo},
//                headers: {'Content-Type': 'application/json'}
//            }).success(function (response) {
//                if (angular.isDefined(response.code) && response.code === 200) {
//                    alert("Đã cập nhật người liên hệ vào hệ thống");
//                } else {
//                    alert("Có lỗi xảy ra");
//                    console.log(response);
//                }
//            }).error(function (response) {
//                console.log(response);
//                alert('Lỗi hệ thống');
//            });
//        };
    }
    /*
     * script xử lý menu 
     */
    $scope.openSideNavPanel = function () {
        $mdSidenav('left').open();
    };
    $scope.closeSideNavPanel = function () {
        $mdSidenav('left').close();
    };
    // end menu

    $scope.getHtml = function (html) {
        return $sce.trustAsHtml(html);
    };
    /**
     * Hàm hiển thị thông tin liên hệ
     */
    $scope.getListContact = function (ev) {
        var error = '';
        if (angular.isUndefined($scope.account.ContractNum)) {
            error += 'Chưa nhập số hợp đồng';
        }
        if (error != '') {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent(error)
                    .ariaLabel('Thông báo')
                    .ok('OK')
                    );
        }
        else {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
            $mdDialog.show({
                controller: DialogController,
                templateUrl: '/assets/outboundapp/templates/add_contact_form.html?t=' + (new Date()).getTime(),
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: useFullScreen,
                scope: $scope, // kế thừa $scope controller parent (accountController)
                preserveScope: true,
            })
                    .then(function () {

                    });
            $scope.$watch(function () {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function (wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        }
    };
    //Lấy thông tin trả về từ server đổ vào template view
    $scope.getSurveyContentByIdSurvey = function ($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, idSurvey, domain) {
        var url = API_URL + "surveys/edit_survey_frontend";
        $http({
            method: 'POST',
            url: url,
            data: {surveyID: idSurvey},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
//            $('.history-detail').html(response);
//            $('#modal-table').modal('show');
            console.log(response);
            if (angular.isDefined(response.code)) {
                console.log(response);
//        		if( response.code == 403) {
//        			// chưa login
//            		window.location = 'http://mo.fpt.vn';
//        		}\
                //Không đủ điều kiện để edit, retry 
                if (response.code == 600) {
                    var confirm = $mdDialog.confirm()
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel(response.msg)
//                            .targetEvent(ev)
                            .ok('Tiếp tục khảo sát')
                            .cancel('Quay lại danh sách khảo sát');
                    $mdDialog.show(confirm).then(function () {
                        $state.go('outbound');
                    }, function () {
                        $state.go('history');
                        ;
                    }
                    );
                }
                //Đủ điều kiện để edit, retry
                else if (response.code == 200) {
                    $scope.contact = response.infoContact;
//                    $scope.contact.phone=parseInt(response.infoContact['phone'])
                    $scope.bandWidth = response.bandWidthInfo;
                    $scope.section_time_start = response.section_time_start;
                    if (response.section_connected == 4) {
                        $('.hide-box3').attr('disabled', 'disabled');
                    }
                    $('#myModaldialog').modal('hide');
                    if (angular.isDefined(response.NPS)) {
                        $scope.NPS = 1;
                    }
//                    $scope.account.ContractNum = response.data_cusinfo[0]['ContractNum'];
                    $scope.account = response.data_cusinfo[0];
                    console.log(response)
                    if (response.data_cusinfo[0]['UseService'] == 1)
                    {

                        $('.paytv').addClass('md-checked');
                        $scope.PackageSal = true;
                        $scope.ContractTypeName = false;
                    }
                    else if (response.data_cusinfo[0]['UseService'] == 2) {
                        $scope.ContractTypeName = true;
                        $scope.PackageSal = false;
                        $('.isp').addClass('md-checked');
                    }
                    else {
                        $scope.ContractTypeName = true;
                        $scope.PackageSal = true;
                        $('.isp').addClass('md-checked');
                        $('.paytv').addClass('md-checked');
                    }
//                    $scope.account.gender=response.data_cusinfo[0]['Sex'];
                    $('.isp').attr('title', response.data_cusinfo[0]['ContractTypeName']);
                    $('.paytv').attr('title', response.data_cusinfo[0]['PackageSal']);
                    if (response.data_cusinfo[0]['CenterList'] == 'TIN/PNC')
                        $scope.account.AccountListTIN = response.data_cusinfo[0]['AccountList'];
                    else
                        $scope.account.AccountListINDO = response.data_cusinfo[0]['AccountList'];
                    if (response.section_survey_id == 2) {
                        $('.after-maintance').addClass('md-checked')
                        $('.after-deploy').attr('disabled', 'disabled');
                        $scope.surveyid = 2;
                        //    loadsurvey($scope, 2);

                    }
                    else {
                        $('.after-deploy').addClass('md-checked')
                        $('.after-maintance').attr('disabled', 'disabled');
                        $scope.surveyid = 1;
                        //  loadsurvey($scope, 1);
                    }
                    //Hợp đồng sau triển khai
//                    if ($('.type-survey .md-checked').attr('value') == 1)
//                    {
//                        var res = response.data_cusinfo[0]['KindDeploy'].split("+");
//                        if (res.length == 2) {
//                            $scope.ContractTypeName = true;
//                            $scope.PackageSal = true;
//                        }
//                        else {
//                            if (response.data_cusinfo[0]['KindDeploy'] == 'Internet') {
//                                $scope.ContractTypeName = true;
//                                $scope.PackageSal = false;
//                            }
//                            else {
//                                $scope.ContractTypeName = false;
//                                $scope.PackageSal = true;
//                            }
//                        }
//                    }
//                    //Hợp đồng sau bảo trì
//                    else
//                    {
//                        var res = response.data_cusinfo[0]['KindMain'].split("+");
//                        if (res.length == 2) {
//                            $scope.ContractTypeName = true;
//                            $scope.PackageSal = true;
//                        }
//                        else {
//                            if (response.data_cusinfo[0]['KindMain'] == 'Internet') {
//                                $scope.ContractTypeName = true;
//                                $scope.PackageSal = false;
//                            }
//                            else {
//                                $scope.ContractTypeName = false;
//                                $scope.PackageSal = true;
//                            }
//                        }
//                    }
                    $('.box-select-box').css('display', 'inline-block');
                    $('.box-check-multi').css('display', 'inline-block');
                    $scope.history = response.data_history;
                    $scope.surveyhistory = response.outbound_history;
                    $scope.NPS = response.NPS;
                    console.log(response);
//                    alert(response.section_survey_id);
                    //Nếu khảo sát gặp người sử dụng
                    if (angular.isDefined(response.ques_ans['answer4']) || angular.isDefined(response.ques_ans['answer2'])) {
                        loadsurvey($scope, response.section_survey_id);
//                        if (angular.isUndefined(response.ques_ans['answer5']))
//                            $scope.exist5 = true;
//                        else
//                            $scope.exist5 = false;
//                        if (angular.isUndefined(response.ques_ans['answer8']))
//                            $scope.exist8 = true;
//                        else {
//                            $scope.exist8 = false;
////                            alert(response.NPS + "" + $scope.exist8)
//                        }
//                        if (angular.isUndefined(response.ques_ans['answer7'])) {
//                            $scope.exist7 = true;
//                        }
//                        else
//                            $scope.exist7 = false;
//                        if (angular.isUndefined(response.ques_ans['answer6']))
//                            $scope.exist6 = true;
//                        else
//                        {
//                            $scope.exist6 = false;
//                        }
//                        $scope.survey = response.ques_ans;
                    }
                    $scope.survey = response.ques_ans;
//                    console.log(response.ques_ans);
//                    console.log(response.section_survey_id);
                    console.log($scope.survey);
//                    alert('huy')
                } else {
//                    $mdDialog.show(
//                            $mdDialog.alert()
//                            .clickOutsideToClose(true)
//                            .title('Lỗi xảy ra')
//                            .textContent('Không tim thấy thông tin của hợp đồng')
//                            .ariaLabel('Không tim thấy thông tin của hợp đồng')
//                            .ok('OK')
//                            .targetEvent(ev)
//                            );
//                       $('#myModaldialog').modal('hide');
//                    $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
//                    $('#myModaldialog').modal('show');
//                    return;
                }

            } else {
//                $mdDialog.show(
//                        $mdDialog.alert()
//                        .clickOutsideToClose(true)
//                        .title('Lỗi xảy ra')
//                        .textContent('Không tim thấy thông tin của hợp đồng')
//                        .ariaLabel('Không tim thấy thông tin của hợp đồng')
//                        .ok('OK')
//                        .targetEvent(ev)
//                        );
////                $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
////                $('#myModaldialog').modal('show');
//                return;
            }
        }).error(function (response) {

        });
    }

    $scope.resetSurvey = function () {
        $state.go('outbound', {}, {reload: true});
//    $state.reload();
    }
    /*
     * Lấy lịch sử chi tiết của khảo sát khi state là history-detail
     */
    if ($state.current.data.state == 'edit-survey') {
        var idSurvey = $stateParams.idSurvey;
        $scope.getSurveyContentByIdSurvey($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, idSurvey, domain);
    }
    //Lấy ngày hiện tại
    $scope.getCurrentDate = function () {
        var fullDate = new Date();
        var twoDigitMonth = fullDate.getMonth() + 1 + "";

        if (twoDigitMonth.length == 1)
            twoDigitMonth = "0" + twoDigitMonth;
        var twoDigitDate = fullDate.getDate() + "";
        if (twoDigitDate.length == 1)
            twoDigitDate = "0" + twoDigitDate;
        var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
        return currentDate;
    }
    /**
     * Hàm tạo checklist, gửi qua server
     */
    $scope.createCL = function (ev) {
        if (angular.isUndefined($scope.account.ContractNum) == false) {
            //Kiểm tra checklist có đang chờ xử lý hay ko
            var url = API_URL + "surveys/getCheckList";
            $http({// xử lý ajax 
                method: 'POST',
                url: url,
                data: {ObjID: $scope.account['ObjID']},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {
                $scope.scode = response.code;
                //Nếu có dữ liệu checklist trả về
                if (angular.isDefined(response.data))
                {
                    $scope.sdata = response.data;
                }
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
                $mdDialog.show({
                    controller: SCLController,
                    templateUrl: '/assets/outboundapp/templates/show_checklist_data_form.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    fullscreen: useFullScreen,
                    scope: $scope, // kế thừa $scope controller parent (accountController)
                    preserveScope: true,
                    windowClass: 'app-modal-window'
                })
                        .then(function () {

                        });
                $scope.$watch(function () {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function (wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
            }).error(function (response) {
                if (angular.isDefined(response.code) && response.code == 800)
                {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
//                            .targetEvent(ev)
                            )
                            .finally(function () {
                                location.reload();
                            });
                }
                else {
                    alert('Lỗi hệ thống');
                }
                console.log(response);
            });
        }
        else {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent('Chưa nhập số hợp đồng')
                    .ariaLabel('Chưa nhập số hợp đồng')
                    .ok('OK')
                    .targetEvent(ev)
                    );
        }
    };

    var SCLController = function ($scope, $mdDialog) {
        //Tạo checkList mới
        if ($scope.scode == 200 || $scope.scode == 400)
        {
            $scope.checked = false;
            //Có dữ liệu
            if ($scope.scode == 400) {
                $scope.showed = true;
                $scope.SCL = $scope.sdata;
            }
            else {
                $scope.showed = false;
            }
        }
        //Không tạo checklist
        else {
            $scope.checked = true;
            $scope.showed = true;
            $scope.SCL = $scope.sdata;
        }
        //Sự kiện load form tạo CL
        $scope.triggerCL = function () {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
            $mdDialog.show({
                controller: CLController,
                templateUrl: '/assets/outboundapp/templates/add_checklist_form.html?t=' + (new Date()).getTime(),
                parent: angular.element(document.body),
//                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: useFullScreen,
                scope: $scope, // kế thừa $scope controller parent (accountController)
                preserveScope: true,
            })
                    .then(function () {

                    });
            $scope.$watch(function () {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function (wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
    }
    function CLController($scope, $mdDialog) {
        //        console.log($scope.account['ObjID']);
        $scope.AP = '';
        $scope.client = false;
        $scope.AD = {};
        $scope.AD['AppointmentDate'] = $scope.getCurrentDate();
        var url = API_URL + "surveys/getNameUser";
        $http({// xử lý ajax 
            method: 'POST',
            url: url,
            data: {ObjID: $scope.account['ObjID'], extra: 1, OwnerType: 1},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            console.log(response);
            $scope.CL['sCreateBy'] = response.name;
            $scope.CL['Supporter'] = response.responseSubID[0]['ResultDepID'];
            $scope.CL['SubSupporter'] = response.responseSubID[0]['ResultSubID'];
            $scope.CL['DeptID'] = response.responseSubID[0]['ResultCode'];
            //            $scope.CL['Supporter'] = response.responseSubID[0]['ResultDepID'];
            //            $scope.CL['SubSupporter'] = response.responseSubID[0]['ResultSubID'];
            //            $scope.CL['DeptID'] = response.responseSubID[0]['ResultCode'];

            $scope.CL['iObjId'] = $scope.account['ObjID'];
            $scope.CL['iType'] = 2;
            $scope.CL['RequestFrom'] = 15;
            $scope.CL['OwnerType'] = 1;

        }).error(function (response) {
            if (angular.isDefined(response.code) && response.code == 800)
            {
                $mdDialog.show(
                        $mdDialog.alert()
                        .clickOutsideToClose(true)
                        .title('Thông báo')
                        .textContent(response.msg)
                        .ariaLabel('Alert Dialog Demo')
                        .ok('OK')
//                            .targetEvent(ev)
                        )
                        .finally(function () {
                            location.reload();
                        });
            }
            else {
                alert('Lỗi hệ thống');
            }
            console.log(response);
        });

        //Reset lại các mốc thời gian khi đổi ngày hẹn
        $scope.resetAP = function () {
            $scope.client = false;
            $scope.AP = '';
        }
        //Lấy dữ liệu các mốc hẹn thời gian trong ngày
        $scope.getDateInfo = function () {
//          console.log($scope.client);
            //
            if ($scope.client == true) {
                var currentDate = $scope.getCurrentDate();
//          console.log(currentDate);
                var timeInfo = {Supporter: $scope.CL.Supporter, SubID: $scope.CL.SubSupporter, AppointmentDate: $scope.AD.AppointmentDate, Date: currentDate};
//  console.log(timeInfo);
//          var cars = [$scope.CL.Supporter, $scope.CL.SubSupporter, $scope.CL.AppointmentDate, $scope.CL];
                var url = API_URL + "surveys/getDateInfo";
                $http({// xử lý ajax 
                    method: 'POST',
                    url: url,
                    data: {datapost: timeInfo},
                    headers: {'Content-Type': 'application/json'}
                }).success(function (response) {
                    console.log(response);
                    if (angular.isDefined(response.code) && response.code === 200) {
                        $scope.AP = response.reponseDateInfo;
                    } else {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent('Có lỗi xảy ra')
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    }
                }).error(function (response) {

                    if (angular.isDefined(response.code) && response.code == 800)
                    {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Alert Dialog Demo')
                                .ok('OK')
//                            .targetEvent(ev)
                                )
                                .finally(function () {
                                    location.reload();
                                });
                    }
                    else {
                        alert('Lỗi hệ thống');
                    }
                    console.log(response);
                });
            }
            else {
                $scope.AP = '';
                console.log($scope.CL);
            }
        }
        $scope.sendCL = function () {//Gửi dữ liệu checklist qua server
            console.log($scope.CL)
            var msg = '';
            if (angular.isUndefined($scope.CL.iInit_Status))
            {
                msg += '\n Vui lòng chọn loại sự cố';
                $scope.incidentflag = 1;
            }
            if (angular.isUndefined($scope.CL.sDescription) || $scope.CL.sDescription == '')
            {
                msg += ' \n Vui lòng ghi chú';
                $scope.desflag = 1;
            }
            if (angular.isUndefined($scope.CL.iModemType))
            {
                msg += ' \n Vui lòng chọn loại Modem';
                $scope.modemflag = 1;
            }
//            if (angular.isUndefined($scope.AD.Department))
//                msg += ' \n Vui lòng chọn giá trị tick hẹn';
            if (angular.isUndefined($scope.AD.Timezone) || $scope.AD.Timezone == false)
            {
                msg += ' \n Vui lòng chọn múi thời gian';
                $scope.assignflag = 1;
            }
//            if (angular.isUndefined($scope.AD.isChange))
//                msg += ' \n Vui lòng chọn cập nhập thời gian';
//            if (angular.isUndefined($scope.AD.AppointmentConfirm))
//                msg += ' \n Vui lòng chọn xác nhận buổi hẹn';
            if (msg == '')
            {
//                $mdDialog.show(
//                        $mdDialog.alert()
//                        .clickOutsideToClose(true)
//                        .title('Lỗi xảy ra')
//                        .textContent(msg)
//                        .ariaLabel('Lỗi xảy ra')
//                        .ok('OK')
//                        //                    .targetEvent(ev)
//                        );
                var url = API_URL + "surveys/createCL";
                $scope.AD['LogonUser'] = $scope.CL.sCreateBy;
                $scope.AD['Supporter'] = $scope.CL.Supporter;
                $scope.AD['SubID'] = $scope.CL.SubSupporter;
                console.log($scope.AD);
                $http({// xử lý ajax 
                    method: 'POST',
                    url: url,
                    data: {datapost: $scope.CL, type: 1, datadate: $scope.AD},
                    headers: {'Content-Type': 'application/json'}
                }).success(function (response) {
                    console.log(response);
                    if (angular.isDefined(response.code) && response.code === 200) {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    } else {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    }
                }).error(function (response) {
                    if (angular.isDefined(response.code) && response.code == 800)
                    {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Alert Dialog Demo')
                                .ok('OK')
//                            .targetEvent(ev)
                                )
                                .finally(function () {
                                    location.reload();
                                });
                    }
                    else {
                        alert('Lỗi hệ thống');
                    }
                    console.log(response);
                });
            }
//            else {
//              
//            }
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.closeUserGuidePoint = function () {
            $mdDialog.hide();
        };
    }

    /**
     * Hàm tạo checklistIndo, gửi qua server
     */
    $scope.createCLID = function (ev) {
        if (angular.isUndefined($scope.account.ContractNum) == false) {
            //Kiểm tra checklist có đang chờ xử lý hay ko
            var url = API_URL + "surveys/getCheckList";
            $http({// xử lý ajax 
                method: 'POST',
                url: url,
                data: {ObjID: $scope.account['ObjID']},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {
                $scope.scode = response.code;
                //Nếu có dữ liệu checklist trả về
                if (angular.isDefined(response.data))
                {
                    $scope.sdata = response.data;
                }
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
                $mdDialog.show({
                    controller: SCLIDController,
                    templateUrl: '/assets/outboundapp/templates/show_checklistIndo_data_form.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    fullscreen: useFullScreen,
                    scope: $scope, // kế thừa $scope controller parent (accountController)
                    preserveScope: true,
                    windowClass: 'app-modal-window'
                })
                        .then(function () {

                        });
                $scope.$watch(function () {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function (wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
            }).error(function (response) {
                if (angular.isDefined(response.code) && response.code == 800)
                {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
//                            .targetEvent(ev)
                            )
                            .finally(function () {
                                location.reload();
                            });
                }
                else {
                    alert('Lỗi hệ thống');
                }
                console.log(response);
            });
        }
        else {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent('Chưa nhập số hợp đồng')
                    .ariaLabel('Chưa nhập số hợp đồng')
                    .ok('OK')
                    .targetEvent(ev)
                    );
        }
    };
    var SCLIDController = function ($scope, $mdDialog) {
        //Tạo checkList mới
        if ($scope.scode == 200 || $scope.scode == 400)
        {
            $scope.checked = false;
            //Có dữ liệu
            if ($scope.scode == 400) {
                $scope.showed = true;
                $scope.SCL = $scope.sdata;
            }
            else {
                $scope.showed = false;
            }
        }
        //Không tạo checklist
        else {
            $scope.checked = true;
            $scope.showed = true;
            $scope.SCL = $scope.sdata;
        }
        //Sự kiện load form tạo CL
        $scope.triggerCL = function () {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
            $mdDialog.show({
                controller: CLIDController,
                templateUrl: '/assets/outboundapp/templates/add_checklistIndo_form.html?t=' + (new Date()).getTime(),
                parent: angular.element(document.body),
//                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: useFullScreen,
                scope: $scope, // kế thừa $scope controller parent (accountController)
                preserveScope: true,
            })
                    .then(function () {

                    });
            $scope.$watch(function () {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function (wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
    }
    function CLIDController($scope, $mdDialog) {
        //        console.log($scope.account['ObjID']);
//        $scope.AP = '';
        $scope.client = false;
//        $scope.AD = {};
//        $scope.AD['AppointmentDate'] = $scope.getCurrentDate();
        var url = API_URL + "surveys/getNameUser";
        $http({// xử lý ajax 
            method: 'POST',
            url: url,
            data: {ObjID: $scope.account['ObjID'], extra: 1, OwnerType: 2},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            console.log(response);
            $scope.CL['sCreateBy'] = response.name;
            $scope.CL['Supporter'] = response.responseSubID[0]['ResultDepID'];
//            $scope.CL['SubSupporter'] = response.responseSubID[0]['ResultSubID'];
            $scope.CL['SubSupporter'] = 0;
            $scope.CL['DeptID'] = response.responseSubID[0]['ResultCode'];
            //            $scope.CL['Supporter'] = response.responseSubID[0]['ResultDepID'];
            //            $scope.CL['SubSupporter'] = response.responseSubID[0]['ResultSubID'];
            //            $scope.CL['DeptID'] = response.responseSubID[0]['ResultCode'];

            $scope.CL['iObjId'] = $scope.account['ObjID'];
            $scope.CL['iType'] = 2;
            $scope.CL['RequestFrom'] = 15;
            $scope.CL['OwnerType'] = 2;

        }).error(function (response) {
            if (angular.isDefined(response.code) && response.code == 800)
            {
                $mdDialog.show(
                        $mdDialog.alert()
                        .clickOutsideToClose(true)
                        .title('Thông báo')
                        .textContent(response.msg)
                        .ariaLabel('Alert Dialog Demo')
                        .ok('OK')
//                            .targetEvent(ev)
                        )
                        .finally(function () {
                            location.reload();
                        });
            }
            else {
                alert('Lỗi hệ thống');
            }
            console.log(response);
        });

        //Reset lại các mốc thời gian khi đổi ngày hẹn
//        $scope.resetAP = function () {
//            $scope.client = false;
//            $scope.AP = '';
//        }
        //Lấy dữ liệu các mốc hẹn thời gian trong ngày
//        $scope.getDateInfo = function () {
////          console.log($scope.client);
//            //
//            if ($scope.client == true) {
//                var currentDate = $scope.getCurrentDate();
////          console.log(currentDate);
//                var timeInfo = {Supporter: $scope.CL.Supporter, SubID: $scope.CL.SubSupporter, AppointmentDate: $scope.AD.AppointmentDate, Date: currentDate};
////  console.log(timeInfo);
////          var cars = [$scope.CL.Supporter, $scope.CL.SubSupporter, $scope.CL.AppointmentDate, $scope.CL];
//                var url = API_URL + "surveys/getDateInfo";
//                $http({// xử lý ajax 
//                    method: 'POST',
//                    url: url,
//                    data: {datapost: timeInfo},
//                    headers: {'Content-Type': 'application/json'}
//                }).success(function (response) {
//                    console.log(response);
//                    if (angular.isDefined(response.code) && response.code === 200) {
//                        $scope.AP = response.reponseDateInfo;
//                    } else {
//                        $mdDialog.show(
//                                $mdDialog.alert()
//                                .clickOutsideToClose(true)
//                                .title('Thông báo')
//                                .textContent('Có lỗi xảy ra')
//                                .ariaLabel('Thông báo')
//                                .ok('OK')
//                                );
//                    }
//                }).error(function (response) {
//                    alert('Lỗi hệ thống');
//                });
//            }
//            else {
//                $scope.AP = '';
//                console.log($scope.CL);
//            }
//        }
        $scope.sendCL = function () {//Gửi dữ liệu checklist qua server
            console.log($scope.CL)
            var msg = '';
            if (angular.isUndefined($scope.CL.iInit_Status))
            {
                msg += '\n Vui lòng chọn loại sự cố';
                $scope.incidentflag = 1;
            }
            if (angular.isUndefined($scope.CL.sDescription) || $scope.CL.sDescription == '')
            {
                msg += ' \n Vui lòng ghi chú';
                $scope.desflag = 1;
            }
            if (angular.isUndefined($scope.CL.iModemType))
            {
                msg += ' \n Vui lòng chọn loại Modem';
                $scope.modemflag = 1;
            }
//            if (angular.isUndefined($scope.AD.Department))
//                msg += ' \n Vui lòng chọn giá trị tick hẹn';
//            if (angular.isUndefined($scope.AD.Timezone) || $scope.AD.Timezone == false)
//            {
//                msg += ' \n Vui lòng chọn múi thời gian';
//                $scope.assignflag = 1;
//            }
//            if (angular.isUndefined($scope.AD.isChange))
//                msg += ' \n Vui lòng chọn cập nhập thời gian';
//            if (angular.isUndefined($scope.AD.AppointmentConfirm))
//                msg += ' \n Vui lòng chọn xác nhận buổi hẹn';
            if (msg == '')
            {
//                $mdDialog.show(
//                        $mdDialog.alert()
//                        .clickOutsideToClose(true)
//                        .title('Lỗi xảy ra')
//                        .textContent(msg)
//                        .ariaLabel('Lỗi xảy ra')
//                        .ok('OK')
//                        //                    .targetEvent(ev)
//                        );
                var url = API_URL + "surveys/createCL";
//                $scope.AD['LogonUser'] = $scope.CL.sCreateBy;
//                $scope.AD['Supporter'] = $scope.CL.Supporter;
//                $scope.AD['SubID'] = $scope.CL.SubSupporter;
                console.log($scope.AD);
                $http({// xử lý ajax 
                    method: 'POST',
                    url: url,
                    data: {datapost: $scope.CL, type: 1},
                    headers: {'Content-Type': 'application/json'}
                }).success(function (response) {
                    console.log(response);
                    if (angular.isDefined(response.code) && response.code === 200) {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    } else {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    }
                }).error(function (response) {
                    if (angular.isDefined(response.code) && response.code == 800)
                    {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Alert Dialog Demo')
                                .ok('OK')
//                            .targetEvent(ev)
                                )
                                .finally(function () {
                                    location.reload();
                                });
                    }
                    else {
                        alert('Lỗi hệ thống');
                    }
                    console.log(response);
                });
            }
//            else {
//               
//            }
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.closeUserGuidePoint = function () {
            $mdDialog.hide();
        };

    }

    /**
     * Hàm tạo Prechecklist, gửi qua server      */
    $scope.createPCL = function (ev) {
        if (angular.isUndefined($scope.account.ContractNum) == false) {
            //Kiểm tra Prechecklist có đang chờ xử lý hay ko
            var url = API_URL + "surveys/getPreCheckList";
            $http({// xử lý ajax 
                method: 'POST',
                url: url,
                data: {ObjID: $scope.account['ObjID'], Contract: $stateParams.sohdISC},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {
                $scope.scode = response.code;
                //Nếu có dữ liệu Prechecklist trả về
                if (angular.isDefined(response.data))
                {
                    $scope.sdata = response.data;
                }
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
                $mdDialog.show({
                    controller: SPCLController,
                    templateUrl: '/assets/outboundapp/templates/show_Prechecklist_data_form.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    fullscreen: useFullScreen,
                    scope: $scope, // kế thừa $scope controller parent (accountController)
                    preserveScope: true,
                    windowClass: 'app-modal-window'
                })
                        .then(function () {

                        });
                $scope.$watch(function () {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function (wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
            }).error(function (response) {
                if (angular.isDefined(response.code) && response.code == 800)
                {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel('Alert Dialog Demo')
                            .ok('OK')
//                            .targetEvent(ev)
                            )
                            .finally(function () {
                                location.reload();
                            });
                }
                else {
                    alert('Lỗi hệ thống');
                }
                console.log(response);
            });
        }
        else {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent('Chưa nhập số hợp đồng')
                    .ariaLabel('Chưa nhập số hợp đồng')
                    .ok('OK')
                    .targetEvent(ev)
                    );
        }
    };
    var SPCLController = function ($scope, $mdDialog) {
        //Tạo PrecheckList mới
        if ($scope.scode == 200 || $scope.scode == 400)
        {
            $scope.checked = false;
            //Có dữ liệu
            if ($scope.scode == 400) {
                $scope.showed = true;
                $scope.SPCL = $scope.sdata;
            }
            else {
                $scope.showed = false;
            }
        }
        //Không tạo Prechecklist
        else {
            $scope.checked = true;
            $scope.showed = true;
            $scope.SPCL = $scope.sdata;
        }
        //Sự kiện load form tạo PCL
        $scope.triggerCL = function () {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
            $mdDialog.show({
                controller: PCLController,
                templateUrl: '/assets/outboundapp/templates/add_prechecklist_form.html?t=' + (new Date()).getTime(),
                parent: angular.element(document.body),
//                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: useFullScreen,
                scope: $scope, // kế thừa $scope controller parent (accountController)
                preserveScope: true,
            })
                    .then(function () {

                    });
            $scope.$watch(function () {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function (wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
    }
    function PCLController($scope, $mdDialog) {
        //        console.log($scope.account['ObjID']);
        var url = API_URL + "surveys/getNameUser";
        $scope.CL = {};
        $http({// xử lý ajax 
            method: 'POST',
            url: url,
            data: {extra: 0},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            console.log(response);
            $scope.CL['CreateBy'] = response.name;
            $scope.CL['ObjID'] = $scope.account['ObjID'];
        }).error(function (response) {
            console.log(response);
            alert('Lỗi hệ thống');
        });
        $scope.sendCL = function () {//Gửi dữ liệu checklist qua server
            console.log($scope.CL)
            var msg = '';
            if (angular.isUndefined($scope.CL.Location_Name) || $scope.CL.Location_Name == '')
            {
                msg += '\n Vui lòng nhập người liên hệ';
                $scope.contactflag = 1;
            }
            if (angular.isUndefined($scope.CL.Location_Phone) || $scope.CL.Location_Phone == '')
            {
                msg += ' \n Vui lòng nhập số điện thoại người liên hệ';
                $scope.phoneflag = 1;
            }
            if (angular.isUndefined($scope.CL.FirstStatus))
            {
                msg += ' \n Vui lòng chọn sự cố ban đầu';
                $scope.incidentflag = 1;
            }
            if (angular.isUndefined($scope.CL.DivisionID))
            {
                msg += ' \n Vui lòng chọn phòng ban tạo PreCL';
                $scope.departmentflag = 1;
            }
            if (angular.isUndefined($scope.CL.Description))
            {
                msg += ' \n Vui lòng nhập ghi chú';
                $scope.noteflag = 1;
            }
            if (msg == '')
            {
//                $mdDialog.show(
//                        $mdDialog.alert()
//                        .clickOutsideToClose(true)
//                        .title('Lỗi xảy ra')
//                        .textContent(msg)
//                        .ariaLabel('Lỗi xảy ra')
//                        .ok('OK')
//                        //                    .targetEvent(ev)
//                        );
                var url = API_URL + "surveys/createCL";
                $http({// xử lý ajax 
                    method: 'POST',
                    url: url,
                    data: {datapost: $scope.CL, type: 2},
                    headers: {'Content-Type': 'application/json'}
                }).success(function (response) {
                    console.log(response);
                    if (angular.isDefined(response.code) && response.code === 200) {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    } else {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    }
                }).error(function (response) {
                    if (angular.isDefined(response.code) && response.code == 800)
                    {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Alert Dialog Demo')
                                .ok('OK')
//                            .targetEvent(ev)
                                )
                                .finally(function () {
                                    location.reload();
                                });
                    }
                    else {
                        alert('Lỗi hệ thống');
                    }
                    console.log(response);
                });
            }
//            else {
//               
//            }
        }
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.closeUserGuidePoint = function () {
            $mdDialog.hide();
        };
    }
    /**
     * Hàm Chuyển phòng ban khác
     */
    $scope.forwardDepartment = function (ev) {

        if (angular.isUndefined($scope.account.ContractNum) == false) {
//            $scope.department = '';
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs')) && $scope.customFullscreen;
            $mdDialog.show({
                controller: DepartmentController,
                //Tránh load lại cache cũ
                templateUrl: '/assets/outboundapp/templates/forward_department_form.html?t=' + (new Date()).getTime(),
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: useFullScreen,
                scope: $scope, // kế thừa $scope controller parent (accountController)
                preserveScope: true,
            })
                    .then(function () {

                    });
            $scope.$watch(function () {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function (wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        }
        else {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent('Chưa nhập số hợp đồng')
                    .ariaLabel('Chưa nhập số hợp đồng')
                    .ok('OK')
                    .targetEvent(ev)
                    );
        }
    };
    function DepartmentController($scope, $mdDialog) {
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.forward = function () {//xử lý thêm thông tin người liên hệ
            var departFlag = true;
            console.log($scope.department);
            if (angular.isUndefined($scope.department))
            {
                departFlag = false;
                $scope.departmentTranfer = true;
            }
            //Không tick vào phòng ban nào cả
            else if ($scope.ibb.check == '0' && $scope.tin.check == '0' && $scope.tls.check == '0' && $scope.cus.check == '0' && $scope.cscn.check == '0' && $scope.csho.check == '0' && $scope.kdda.check == '0' && $scope.nvtc.check == '0')
            {
                $scope.reasonIbb = $scope.descriptionIbb = $scope.reasonTin = $scope.descriptionTin = $scope.reasonTls = $scope.descriptionTls = $scope.reasonCus = $scope.descriptionCus = $scope.reasonCscn = $scope.descriptionCscn = $scope.reasonCsho = $scope.descriptionCsho = $scope.descriptionKdda = $scope.descriptionNvtc = false;
                $scope.departmentTranfer = false;
                $scope.validateWhole = true;
                departFlag = false;
            }
            else
            {

                $scope.departmentTranfer = false;
                $scope.validateWhole = false;
                //Mảng chứa dữ liệu phòng ban hợp lệ
                var arrayValidDepart = [];
                //Validate IBB nếu tick
                if ($scope.ibb.check == '1')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.ibb.reason) && angular.isDefined($scope.ibb.description) && $scope.ibb.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.ibb);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.ibb.reason))
                        {
                            $scope.reasonIbb = true;
                            departFlag = false;
                        }
                        else
                            $scope.reasonIbb = false;
                        if (angular.isUndefined($scope.ibb.description) || $scope.ibb.description == '')
                        {
                            $scope.descriptionIbb = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionIbb = false;
                    }
                }
                else {
                    $scope.reasonIbb = false;
                    $scope.descriptionIbb = false;
                }
                //Validate TIN nếu tick
                if ($scope.tin.check == '2')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.tin.reason) && angular.isDefined($scope.tin.description) && $scope.tin.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.tin);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.tin.reason))
                        {
                            $scope.reasonTin = true;
                            departFlag = false;
                        }
                        else
                            $scope.reasonTin = false;
                        if (angular.isUndefined($scope.tin.description) || $scope.tin.description == '')
                        {
                            $scope.descriptionTin = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionTin = false;
                    }
                }
                else {
                    $scope.reasonTin = false;
                    $scope.descriptionTin = false;
                }
                //Validate tls nếu tick
                if ($scope.tls.check == '3')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.tls.reason) && angular.isDefined($scope.tls.description) && $scope.tls.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.tls);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.tls.reason))
                        {
                            $scope.reasonTls = true;
                            departFlag = false;
                        }
                        else
                            $scope.reasonTls = false;
                        if (angular.isUndefined($scope.tls.description) || $scope.tls.description == '')
                        {
                            $scope.descriptionTls = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionTls = false;
                    }
                }
                else {
                    $scope.reasonTls = false;
                    $scope.descriptionTls = false;
                }
                //Validate cus nếu tick
                if ($scope.cus.check == '4')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.cus.reason) && angular.isDefined($scope.cus.description) && $scope.cus.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.cus);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.cus.reason))
                        {
                            $scope.reasonCus = true;
                            departFlag = false;
                        }
                        else
                            $scope.reasonCus = false;
                        if (angular.isUndefined($scope.cus.description) || $scope.cus.description == '')
                        {
                            $scope.descriptionCus = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionCus = false;
                    }
                }
                else {
                    $scope.reasonCus = false;
                    $scope.descriptionCus = false;
                }
                //Validate cscn nếu tick
                if ($scope.cscn.check == '5')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.cscn.reason) && angular.isDefined($scope.cscn.description) && $scope.cscn.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.cscn);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.cscn.reason))
                        {
                            $scope.reasonCscn = true;
                            departFlag = false;
                        } else
                            $scope.reasonCscn = false;
                        if (angular.isUndefined($scope.cscn.description) || $scope.cscn.description == '')
                        {
                            $scope.descriptionCscn = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionCscn = false;
                    }
                }
                else {
                    $scope.reasonCscn = false;
                    $scope.descriptionCscn = false;
                }


                //Validate nvtc nếu tick
                if ($scope.nvtc.check == '8')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.nvtc.reason) && angular.isDefined($scope.nvtc.description) && $scope.nvtc.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.nvtc);
                    }
                    //Không hợp lệ
                    else {
                        if (angular.isUndefined($scope.nvtc.reason))
                        {
                            $scope.reasonNvtc = true;
                            departFlag = false;
                        } else
                            $scope.reasonNvtc = false;
                        if (angular.isUndefined($scope.nvtc.description) || $scope.nvtc.description == '')
                        {
                            $scope.descriptionNvtc = true;
                            departFlag = false;
                        }
                        else
                            $scope.descriptionNvtc = false;
                    }
                }
                else {
                    $scope.reasonNvtc = false;
                    $scope.descriptionNvtc = false;
                }
                //Validate csho nếu tick
                if ($scope.csho.check == '6')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.csho.description) && $scope.csho.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.csho);
                        $scope.descriptionCsho = false;
                    }
                    //Không hợp lệ
                    else {
//                        if (angular.isUndefined($scope.kdda.description))
//                        {
                        $scope.descriptionCsho = true;
                        departFlag = false;
//                        }
                    }

                }
                else {
                    $scope.descriptionCsho = false;
                }
                //Validate kdda nếu tick
                if ($scope.kdda.check == '7')
                {
                    //Hợp lệ thì thêm vào mảng trên
                    if (angular.isDefined($scope.kdda.description) && $scope.kdda.description != '')
                    {
//                        arrayValidDepart.push($scope.ibb.splice(0, 2));
                        arrayValidDepart.push($scope.kdda);
                        $scope.descriptionKdda = false;
                    }
                    //Không hợp lệ
                    else {
//                        if (angular.isUndefined($scope.kdda.description))
//                        {
                        $scope.descriptionKdda = true;
                        departFlag = false;
//                        }
                    }

                }
                else {
                    $scope.descriptionKdda = false;
                }
            }
            //Validate đúng
            if (departFlag == true)
            {
                console.log(arrayValidDepart);
                var arrDepartment = [];
                arrDepartment.push({ObjID: $scope.account.ObjID, TableID: 1, arrayValidDepart: arrayValidDepart, Department: $scope.department});
                var url = API_URL + "surveys/forwardDepartment";
                $http({// xử lý ajax 
                    method: 'POST',
                    url: url,
                    data: {datapost: arrDepartment},
                    headers: {'Content-Type': 'application/json'}
                }).success(function (response) {
                    if (angular.isDefined(response.code) && response.code == 200)
                    {
//                        console.log(3,"\\n\\n".replace(/\\n/g,"\n"))
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .htmlContent(response.msg.replace(/\\n/g, "\n"))
                                .ariaLabel('Thông báo')
                                .ok('OK')
                                );
                    }
                }).error(function (response) {
                    if (angular.isDefined(response.code) && response.code == 800)
                    {
                        $mdDialog.show(
                                $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Thông báo')
                                .textContent(response.msg)
                                .ariaLabel('Alert Dialog Demo')
                                .ok('OK')
//                            .targetEvent(ev)
                                )
                                .finally(function () {
                                    location.reload();
                                });
                    }
                    else {
                        alert('Lỗi hệ thống');
                    }
                    console.log(response);
                });
            }
        };
    }
    //Hàm lưu số điện thoại liên hệ
    $scope.saveContact = function () {
        console.log($scope.contact.phone);
        var error = '';
        if (angular.isUndefined($scope.account.ContractNum)) {
            error += 'Chưa nhập số hợp đồng';
        }
        else if (angular.isUndefined($scope.contact.phone) || $scope.contact.phone == '' || $scope.contact.phone == null) {
            error += 'Anh/Chị vui lòng nhập số điện thoại liên hệ';
        } else if (isFinite(Number($scope.contact.phone)) === false) {
            error += 'Anh/Chị vui lòng nhập đúng định dạng số điện thoại liên hệ';
        }
        else if (angular.isUndefined($scope.contact.name) || $scope.contact.name == '' || $scope.contact.name == null) {
            error += 'Anh/Chị vui lòng nhập tên người liên hệ';
        }
        if (error != '')
        {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Lỗi xảy ra')
                    .textContent(error)
                    .ariaLabel(error)
                    .ok('OK')
//                    .targetEvent(ev)
                    );
        }
        else {
            var url = API_URL + "surveys/addContact";
            $http({// xử lý ajax 
                method: 'POST',
                url: url,
                data: {dataContact: $scope.contact, contractNum: $scope.account.ContractNum},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {
                console.log(response);
                if (angular.isDefined(response.code) && response.code === 200) {
                    $mdDialog.show(
                            $mdDialog.alert()
                            .clickOutsideToClose(true)
                            .title('Thông báo')
                            .textContent(response.msg)
                            .ariaLabel('Thông báo')
                            .ok('OK')
                            );
                }
                else {
                    alert('Lỗi hệ thống');
                }
            }).error(function (response) {
                alert('Lỗi hệ thống');
            });
        }

    }
    $scope.getContactInfoByPhone = function () {
//         console.log($scope.contact.phone);
        if (angular.isDefined($scope.contact.phone) && $scope.contact.phone != '' && angular.isDefined($scope.account.ContractNum)) {
            var url = API_URL + "surveys/getContactByPhone";
            $http({// xử lý ajax 
                method: 'POST',
                url: url,
                data: {phone: $scope.contact.phone, contractNum: $scope.account.ContractNum},
                headers: {'Content-Type': 'application/json'}
            }).success(function (response) {
                console.log(response);
                if (angular.isDefined(response.code) && response.code === 200) {
                    $scope.contact.name = response.contactInfo['name']
                    $scope.contact.relationship = response.contactInfo['relationship']
//                    $scope.contact = response.contactInfo;
                }
                else {
                    alert('Lỗi hệ thống');
                }
            }).error(function (response) {
//                alert('Lỗi hệ thống');
            });
        }
    }
});
app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});

app.controller('contactCtrl', function ($scope, $http, API_URL) {

    var url = API_URL + "surveys/getContact";
    $http({// xử lý ajax 
        method: 'POST',
        url: url,
        data: {contract: $scope.account.ContractNum}, //shd
        headers: {'Content-Type': 'application/json'}
    }).success(function (response) {
        if (response.data == null)
        {
            $scope.displayContact = true;
        }
        else {
            $scope.displayContact = false;
            $scope.data = response.data;
        }
    }).error(function (response) {
        if (angular.isDefined(response.code) && response.code == 800)
        {
            $mdDialog.show(
                    $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Thông báo')
                    .textContent(response.msg)
                    .ariaLabel('Alert Dialog Demo')
                    .ok('OK')
//                            .targetEvent(ev)
                    )
                    .finally(function () {
                        location.reload();
                    });
        }
        else {
            alert('Lỗi hệ thống');
        }
        console.log(response);
    });
    $scope.loadInfoContact = function (event) {
//        children().first();
        var idRelation;
        $scope.contact.name = $($(event.target).parent().children()[0]).html();
        $scope.contact.phone = $($(event.target).parent().children()[1]).html();
        switch ($($(event.target).parent().children()[2]).html()) {
            case 'Ba mẹ':
                idRelation = 1;
                break;
            case 'Anh chị em':
                idRelation = 2;
                break;
            case 'Bạn bè':
                idRelation = 3;
                break;
            case 'Chủ hợp đồng':
                idRelation = 4;
                break;
            case 'Khác':
                idRelation = 5;
                break;
        }
        $scope.contact.relationship = idRelation;
    }
//    }
});
