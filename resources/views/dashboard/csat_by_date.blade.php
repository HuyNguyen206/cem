<?php
    $pointToday = number_format($pointToday, 2);
    $pointYesterday = number_format($pointYesterday, 2);
    $pointLastweek = number_format($pointLastweek, 2);
    $pointLastmonth = number_format($pointLastmonth, 2);
?>
<div class="col-left-data-service">
    <div>{{$title}}</div>
    <div>{{number_format(round($pointCountry,2), 2)}}</div> 
</div>
<div class="col-right-data-service">
    <div><p class="col-xs-12"><span class="col-xs-8 pull-left">{{trans($transfile.'.Today')}}</span> <span class="col-xs-4 text-right">{{$pointToday or 0}}</span></p></div>
    <div><p class="col-xs-12"><span class="col-xs-8 pull-left">{{trans($transfile.'.Yesterday')}}</span> <span class="col-xs-4 text-right">{{$pointYesterday or 0}}</span></p></div>
    <div><p class="col-xs-12"><span class="col-xs-8 pull-left">{{trans($transfile.'.Last week')}}</span> <span class="col-xs-4 text-right">{{$pointLastweek or 0}}</span></p></div>
    <div><p class="col-xs-12"><span class="col-xs-8 pull-left">{{trans($transfile.'.Last Month')}}</span> <span class="col-xs-4 text-right">{{$pointLastmonth or 0}}</span></p></div>
</div>