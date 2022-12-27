<?php

require_once './config.php';

unset($_SESSION['ship_id']);

header('Location: index.php');
die;
