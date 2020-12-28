app.controller('historyController', function ($scope, $state, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL, domain, $stateParams) {//

//    var filter;
// $scope.filter.startDate={};
// $scope.filter.endDate={};
//    $scope.filters = [];
    $scope.users = []; //declare an empty array
    $scope.pageno = 1; // initialize page no to 1
    $scope.total_count = 0;
    $scope.itemsPerPage = 50; //this could be a dynamic value from a drop down
    $scope.pagenumber = 0;
    $scope.trigger = true;
    $scope.contactResultf = [];
    $scope.typeSurvey = [];
    $scope.region = [];
    $scope.action = [];
    $scope.pointNps = [];
    $scope.pointCsatEmp = [];
    $scope.pointCsatDep = [];
    $scope.pointCsatInt = [];
    $scope.pointCsatTv = [];
//    $scope.filter.startDate=[];
//    $scope.filter.endDate=[];
//Định nghĩa kết quả liên hệ
    $scope.text = {buttonDefaultText: 'Chọn kết quả liên hệ', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.data = [
        {id: 4, label: "Gặp người SD"},
        {id: 3, label: "Không gặp người SD"},
        {id: 2, label: "Gặp KH,KH từ chối CS"},
        {id: 1, label: "Không liên lạc được"},
        {id: 0, label: "Không cần liên hệ"}];
    //Định nghĩa loại khảo sát
    $scope.textType = {buttonDefaultText: 'Chọn loại khảo sát', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.dataType = [
        {id: 1, label: "Sau triển khai"},
        {id: 2, label: "Sau bảo trì"}];
//Định nghĩa vùng
    $scope.textRegion = {buttonDefaultText: 'Chọn vùng', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.dataRegion = [
        {id: 'Vung 1', label: "Vùng 1"},
        {id: 'Vung 2', label: "Vùng 2"},
        {id: 'Vung 3', label: "Vùng 3"},
        {id: 'Vung 4', label: "Vùng 4"},
        {id: 'Vung 5', label: "Vùng 5"},
        {id: 'Vung 6', label: "Vùng 6"},
        {id: 'Vung 7', label: "Vùng 7"}];
//Định nghĩa xử lý
    $scope.textAction = {buttonDefaultText: 'Chọn xử lý', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.dataAction = [
        {id: 1, label: "Không làm gì"},
        {id: 2, label: "Tạo Checklist"},
        {id: 3, label: "PreChecklist"},
        {id: 4, label: "Tạo checklist INDO"},
        {id: 5, label: "Chuyển phòng ban khác"}
    ];
    //Định nghĩa NPS
    $scope.textNps = {buttonDefaultText: 'Chọn điểm NPS', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.dataNps = [
        {id: 0, label: "Điểm 0"},
        {id: 1, label: "Điểm 1"},
        {id: 2, label: "Điểm 2"},
        {id: 3, label: "Điểm 3"},
        {id: 4, label: "Điểm 4"},
        {id: 5, label: "Điểm 5"},
        {id: 6, label: "Điểm 6"},
        {id: 7, label: "Điểm 7"},
        {id: 8, label: "Điểm 8"},
        {id: 9, label: "Điểm 9"},
        {id: 10, label: "Điểm 10"}
    ];
    //Định nghĩa CSAT
    $scope.textCsat = {buttonDefaultText: 'Chọn điểm CSAT', checkAll: 'Chọn tất cả', uncheckAll: 'Bỏ chọn tất cả'};
    $scope.dataCsat = [
        {id: 1, label: "Điểm 1"},
        {id: 2, label: "Điểm 2"},
        {id: 3, label: "Điểm 3"},
        {id: 4, label: "Điểm 4"},
        {id: 5, label: "Điểm 5"}
    ];
    $scope.settings = {
        smartButtonMaxItems: 2,
        smartButtonTextConverter: function (itemText, originalItem) {
//                if (itemText === 'Jhon') {
//                return 'Jhonny!';
//                }

            return itemText;
        }
    };
    $scope.startd = new Date();
    $scope.endd = new Date();

    $scope.getData = function (pageno) {
        //Tạo mảng id các kết quả liên hệ đã chọn
        var listIdResult = [];
        angular.forEach($scope.contactResultf, function (value, key) {
            listIdResult.push(value.id);
        });
        //Tạo mảng loại khảo sát
        var listTypeSurvey = [];
        angular.forEach($scope.typeSurvey, function (value, key) {
            listTypeSurvey.push(value.id);
        });
        //Tạo mảng vùng
        var listRegion = [];
        angular.forEach($scope.region, function (value, key) {
            listRegion.push(value.id);
        });
        //Tạo mảng xử lý
        var listAction = [];
        angular.forEach($scope.action, function (value, key) {
            listAction.push(value.id);
        });
        //Tạo mảng điểm NPS
        var listNps = [];
        angular.forEach($scope.pointNps, function (value, key) {
            listNps.push(value.id);
        });
        // Tạo mảng điểm CSAT NV Kinh Doanh 
        var listCsatEmp = [];
        angular.forEach($scope.pointCsatEmp, function (value, key) {
            listCsatEmp.push(value.id);
        });
         //Tạo mảng điểm CSAT NV Triển khai
        var listCsatDep = [];
        angular.forEach($scope.pointCsatDep, function (value, key) {
            listCsatDep.push(value.id);
        });
         //Tạo mảng điểm CSAT Dịch vụ Internet
        var listCsatInt = [];
        angular.forEach($scope.pointCsatInt, function (value, key) {
            listCsatInt.push(value.id);
        });
         //Tạo mảng điểm CSAT Dịch vụ TV
        var listCsatTv = [];
        angular.forEach($scope.pointCsatTv, function (value, key) {
            listCsatTv.push(value.id);
        });
        console.log($scope.filters);
        console.log(listRegion);
//        console.log($scope.contactResultf);
        // This would fetch the data on page change.
        //In practice this should be in a factory.
        $scope.do = 3;
        $scope.pagenumber = pageno;
        $scope.users = [];
        $scope.trigger = false;
//        $http.get("http://yourdomain/apiname/{itemsPerPage}/{pagenumber}").success(function(response){ 
        var url = API_URL + "surveys/getHistoryFrontend";
        $http({
            method: 'POST',
            url: url,
            data: {itemPer: $scope.itemsPerPage, pageNum: $scope.pagenumber, filter: $scope.filters, listIdResult: listIdResult, listTypeSurvey: listTypeSurvey, listRegion: listRegion, listAction: listAction, listNps: listNps, listCsatDep: listCsatDep,listCsatEmp: listCsatEmp,listCsatInt: listCsatInt,listCsatTv: listCsatTv},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            //ajax request to fetch data into vm.data
            if (response.do == 1) {
                $scope.do = 1;
                $scope.users = response.data;  // data to be displayed on current page.
                $scope.total_count = response.total_count; // total data count.

                $scope.action1 = "Không làm gì";
                $scope.action2 = "Tạo checklist";
                $scope.action3 = "PreChecklist";
                $scope.action4 = "Tạo checklist INDO";
                $scope.action5 = "Chuyển phòng ban khác";
                $scope.type1 = 'Sau triển khai';
                $scope.type2 = 'Sau bảo trì';

            }
            else {
                $scope.do = 2;
            }
            console.log($scope.users);
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
    };


//    if ($state.current.data.state == 'history') {
//        $scope.getData(1); // Call the function to fetch initial data on page load.
//    }
    $scope.getSurvey = function () {
        $scope.getData(1)
    }
    $scope.getDetailSurvey = function ($http, contractNum, idSurvey) {
        var url = domain + "/history/detail_survey_frontend";
        $http({
            method: 'POST',
            url: url,
            data: {surveyID: idSurvey, contractNum: contractNum},
            headers: {'Content-Type': 'application/json'}
        }).success(function (response) {
            $('.history-detail').html(response);
//            $('#modal-table').modal('show');
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
    /*
     * Lấy lịch sử chi tiết của khảo sát khi state là history-detail
     */
    if ($state.current.data.state == 'history-detail') {
        var idSurvey = $stateParams.idSurvey;
        var contractNum = $stateParams.contractNum;
        $scope.getDetailSurvey($http, contractNum, idSurvey);
    }
//    $scope.getHistory = function ($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL)
//    {
//        var url = API_URL + "surveys/getHistoryFrontend";
//        $http({
//            method: 'POST',
//            url: url,
////            data: {sohd: sohdType},
//            headers: {'Content-Type': 'application/json'}
//        }).success(function (response) {
//            console.log(response);
//            $scope.historyUser = response;
////Bind dữ liệu sang text
//            $scope.connect0 = "Không cần liên hệ";
//            $scope.connect1 = "Không liên lạc được"
//            $scope.connect2 = "Gặp KH, KH từ chối CS"
//            $scope.connect3 = "Không gặp người SD"
//            $scope.connect4 = "Gặp người SD";
//
//            $scope.action1 = "Không làm gì";
//            $scope.action2 = "Tạo checklist";
//            $scope.action3 = "PreChecklist";
//            $scope.action4 = "Tạo checklist INDO";
//            $scope.action5 = "Chuyển phòng ban khác";
//            setTimeout(function () {
//                var oTable1 = $('#sample-table-2').dataTable({
//                    "aoColumns": [
//                        {"bSortable": false}, null, null, null, null, null, null, {"bSortable": false}
//                    ],
//                    "bJQueryUI": false,
//                    "oLanguage": {
//                        "sLengthMenu": "Hiển thị _MENU_ bản tin mỗi trang",
//                        "sZeroRecords": "Không tìm thấy",
//                        "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
//                        "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
//                        "sInfoFiltered": "(Lọc từ _MAX_ tổng số bản ghi)",
//                        "sSearch": "Tìm kiếm"
//                    }
//                });
//            }, 1);
////            $scope.loadTable(); 
//        }).error(function (response) {
////            $scope.searchStatus = 'Không tim thấy thông tin của hợp đồng';
////            return;
//        });
//    }
//    $scope.getHistory($scope, $http, $templateRequest, $sce, $compile, $mdDialog, $mdSidenav, $mdMedia, API_URL);
    $scope.openSideNavPanel = function () {
        $mdSidenav('left').open();
    };
    $scope.closeSideNavPanel = function () {
        $mdSidenav('left').close();
    };
//    jQuery(function ($) {
    $scope.returnClick = function ()
    {
        location.href = domain;
    }
//$event.target.parent().find('input[name=surveyID]')
//    $scope.detailSurvey = function ($event) {
////       var ha= angular.element($event.currentTarget).attr('surid');
//        var url = domain + "/history/detail_survey_frontend";
//        $http({
//            method: 'POST',
//            url: url,
//            data: {surveyID: angular.element($event.currentTarget).attr('surid')},
//            headers: {'Content-Type': 'application/json'}
//        }).success(function (response) {
//            $('.modal-body').html(response);
//            $('#modal-table').modal('show');
//        }).error(function (response) {
//
//        });
//    }
});