<html lang="en-US" ng-app="outbound">
    <!--<meta name="csrf-token" content="{{ csrf_token() }}" />-->
    <head>
        <title>Surveys</title>
        <!-- Load Bootstrap CSS -->

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
              rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{asset('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css')}}">
        <link rel="stylesheet" type="text/css"
              href="{{asset('assets/outboundapp/bootstrap/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" type="text/css"
              href="{{asset('assets/outboundapp/font-awesome/css/font-awesome.min.css')}}" />

        <link rel="stylesheet" type="text/css"
              href="{{asset('assets/outboundapp/css/tooltipster.css')}}" />
        <link rel="stylesheet" type="text/css"
              href="{{asset('assets/outboundapp/css/angular-material.min.css')}}" />

        <!-- ace styles -->
        <link rel="stylesheet" href="{{asset('assets/css/chosen.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}" />

 <!--<script src="{{asset('assets/js/jquery-2.0.3.min.js')}}"></script>-->
         <!--<script src='assets/js/chosen.jquery.min.js'></script>-->

        <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/custom-chosen-content.css')}}" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
              href="{{asset('assets/outboundapp/css/styles.css')}}" />

    </head>
    <body id="body-page" style="background-color: #fff;font-size: 12px" >
        <div  ng-controller="accountController">
            <div ui-view>

            </div>
            @yield('content')
        </div>
        <script src="{{asset('assets/outboundapp/js/jquery.min.js')}}"></script>
        <script src="{{asset('assets/outboundapp/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('assets/outboundapp/js/jquery.tooltipster.js')}}"></script>
        <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
        <!-- Load Javascript Libraries (AngularJS, JQuery, Bootstrap) -->
        <script type="text/javascript" src="{{asset('//code.jquery.com/ui/1.11.4/jquery-ui.js')}}"></script>
        <script src="{{asset('assets/outboundapp/lib/angular1.4.8/angular.min.js')}}"></script>
        <script
        src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-animate.min.js')}}"></script>
        <script src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-aria.min.js')}}"></script>
        <script
        src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-messages.min.js')}}"></script>
        <script
        src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-material.min.js')}}"></script>
        <script
        src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-ui-router.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('assets/outboundapp/lib/angular1.4.8/angular-sanitize.js')}}"></script>



        <script
        src="{{asset('assets/outboundapp/lib/dirPagination.js')}}"></script>
         <script
        src="{{asset('assets/outboundapp/lib/underscore-min.js')}}"></script>
         <script
        src="{{asset('assets/outboundapp/lib/angularjs-dropdown-multiselect.js')}}"></script>
        <!-- AngularJS Application Scripts -->
        <script src="{{asset('assets/outboundapp/app.js')}}?t=<?php echo time();?>"></script>
        <!-- <script src="assets/outboundapp/controllers/employees"></script> -->
        <script src="{{asset('assets/outboundapp/controllers/surveys.js')}}?t=<?php echo time();?>"></script>
        <script src="{{asset('assets/outboundapp/controllers/account.js')}}?t=<?php echo time();?>"></script>
        <script src="{{asset('assets/outboundapp/controllers/history.js')}}?t=<?php echo time();?>"></script>
        <script>
            $(document).ready(function () {
                $('.hide-box3').click(function () {
                    $('.box-select-box').css('display', 'none');
                    $('.box-check-multi').css('display', 'none');
                })
                $('.show-box3').click(function () {
                    $('.box-select-box').css('display', 'inline-block');

                    $('.box-check-multi').css('display', 'inline-block');
                })
                $('.show-box3').removeAttr('tabindex');
            });


        </script>
    </div>
</body>
</html>