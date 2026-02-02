<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

include 'tpls/device.tpl.php';

