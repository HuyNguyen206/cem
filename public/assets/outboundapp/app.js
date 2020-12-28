
var app = angular.module('outbound', ['ngMaterial','ui.router','ngSanitize','angularUtils.directives.dirPagination','angularjs-dropdown-multiselect'])
        .constant('API_URL', 'http://'+document.domain+'/').constant('domain',"http://"+document.domain)
 	.config(function($mdThemingProvider) {
               $mdThemingProvider.theme('customTheme') 
                  .primaryPalette('grey')
                  .accentPalette('orange')
                  .warnPalette('red');
               });
app.directive('datepicker', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            $(function () {
                element.datepicker({
                    changeYear: true,
                    dateFormat: 'dd-mm-yy',
                    minDate: new Date('01/01/1900'),
                    maxDate: '-1d',
                    onSelect: function (date) {
                        scope.$apply(function () {
                            ngModelCtrl.$setViewValue(date);
                        });
                    }
                });
            });
        }
    }
});
//Chon ngay hen KH
app.directive('datepickerclient', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            $(function () {
                element.datepicker({
                    changeYear: true,
                    dateFormat: 'dd-mm-yy',
                    minDate: 1,
//                    maxDate: '-1d',
                    onSelect: function (date) {
                        scope.$apply(function () {
                            ngModelCtrl.$setViewValue(date);
                        });
                    }
                });
                element.datepicker('setDate', new Date());

            });
        }
    }
});

app.config(function ($stateProvider, $urlRouterProvider) {

    // For any unmatched url, send to /route1
      $urlRouterProvider.otherwise("/outbound")

    $stateProvider
            .state('inputcontract', {
                url: "/inputcontract/:sohdISC/:type/:codedm/:option",
                params: {
                    codedm: '0', // default value of codedm is 0
                    option: '0',
                },
                templateUrl: "/assets/outboundapp/templates/outbound.html",
               controller: 'accountController',
                data: {
			        state: "inputcontract",
			    }  
            })
        .state('outbound', {
            url: "/outbound",
            templateUrl: "/assets/outboundapp/templates/outbound.html",
             controller: 'accountController',
              data: {
        state:"outbound",
    }  
        })
         .state('history', {
            url: "/history",
            templateUrl: "/assets/outboundapp/templates/history.html",
             controller: 'historyController',
              data: {
        state:"history",
    }  
        })
          .state('history/detail', {
           url: "/history/detail/:contractNum/:idSurvey",
            templateUrl: "/assets/outboundapp/templates/history_detail.html",
             controller: 'historyController',
              data: {
        state:"history-detail",
    }  
        })
              .state('survey/edit', {
            url: "/survey/edit/:idSurvey",
            templateUrl: "/assets/outboundapp/templates/outbound.html",
             controller: 'accountController',
              data: {
        state:"edit-survey",
    }  
        })
              .state('survey/retry', {
            url: "/survey/retry/:idSurvey",
            templateUrl: "/assets/outboundapp/templates/outbound.html",
             controller: 'accountController',
              data: {
        state:"edit-survey",
    }  
        })
})
