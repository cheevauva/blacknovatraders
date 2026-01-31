<?php

include("config.php");
include("languages/$lang");

connectdb();

if (empty($_GET['startdate'])) {
    $startdate = date("Y/m/d");
} else {
    $startdate = date(strval($_GET['startdate']));
}

$title = $l_news_title;
$previousday = date('Y/m/d', strtotime($startdate . ' -1 day'));
$nextday = date('Y/m/d', strtotime($startdate . ' +1 day'));
$rows = sqlGetNewsByDate($startdate);

include 'news.tpl.php';
