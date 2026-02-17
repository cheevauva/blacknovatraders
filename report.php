<?php

include 'config.php';

if (checkship()) {
    die();
}

include 'tpls/report.tpl.php';
