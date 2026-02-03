<?php

include 'config.php';

if (!checklogin(false)) {
    redirectTo('index.php');
    return;
}

include 'tpls/new.tpl.php';

