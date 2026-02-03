<?php
include 'config.php';




if (checklogin()) {
    die();
}

include 'tpls/report.tpl.php';
