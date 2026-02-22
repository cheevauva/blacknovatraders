<?php

include 'config.php';

if (checkship()) {
    die();
}

include 'tpls/device.tpl.php';
