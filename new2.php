<?php

use BNT\Ship\Servant\ShipNewServant;

require_once './config.php';

loadlanguage($lang);
connectdb();

try {
    $new = new ShipNewServant;
    $new->character = strval($_POST['character'] ?? '');
    $new->username = strval($_POST['username'] ?? '');
    $new->shipname = strval($_POST['shipname'] ?? '');
    $new->password = strval($_POST['password'] ?? '');
    $new->ip = $ip;
    $new->serve();
    echo twig()->render('new/new2.twig', [
        'user_created' => 1,
        'message' => $l_new_pwsent,
    ]);
} catch (\Exception $ex) {
    echo twig()->render('new/new2.twig', [
        'message' => $ex->getMessage() . $l_new_err,
    ]);
}
