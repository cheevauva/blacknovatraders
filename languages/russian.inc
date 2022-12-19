<?php

foreach (parse_ini_file('resources/languages/russian.ini') as $k => $v) {
    $$k = $v;
}