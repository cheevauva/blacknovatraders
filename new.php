<?php

require_once './config.php';
loadlanguage($lang);

echo twig()->render('new/new.twig');