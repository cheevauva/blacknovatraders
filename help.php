<?php

include("config.php");
include("languages/$lang");

switch ($language) {
    case 'russian':
        include_once 'tpls/help/help_russian.tpl.php';
        break;
    case 'english':
        include_once 'tpls/help/help_english.tpl.php';
        break;
}


