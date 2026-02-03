<?php

include 'config.php';

if (checklogin()) {
    die();
}

include 'tpls/device.tpl.php';

