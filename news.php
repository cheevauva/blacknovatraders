<?php

include 'config.php';

if (empty($_GET['startdate'])) {
    $startdate = date("Y/m/d");
} else {
    $startdate = date(strval($_GET['startdate']));
}

$title = $l_news_title;
$previousday = date('Y/m/d', strtotime($startdate . ' -1 day'));
$nextday = date('Y/m/d', strtotime($startdate . ' +1 day'));
$rows = newsByDate($startdate);

include 'tpls/news.tpl.php';
