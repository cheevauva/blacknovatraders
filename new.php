<?php

include 'config.php';

if (!checklogin(false)) {
    redirectTo('index.php');
    return;
}

try {
    if ($account_creation_closed) {
        throw new \Exception($l_new_closed_message);
    }
    
    switch (requestMethod()) {
        case 'POST':


            $username = fromPost('username', new \Exception($l_new_blank));
            $character = fromPost('character', new \Exception($l_new_blank));
            $shipname = fromPost('shipname', new \Exception($l_new_blank));
            $password = fromPost('password', new \Exception($l_login_pw . ' ' . $l_none));

            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception($l_new_inuse);
            }

            //ADDDED these variables for future reference- we'll move em to config values
            //*eventually* - the goal will be to move "start up" config values to some other location
            //where we will only include them where needed- why include em every page load, when they are used maybe 1% of the time?

            $start_lssd = 'N';  //do ships start with an lssd ?
            $start_editors = 0; //starting warp editors
            $start_minedeflectors = 0; //start mine deflectors
            $start_emerwarp = 0; //start emergency warp units
            $start_beacon = 0; //start space_beacons
            $start_genesis = 0; //starting genesis torps
            $escape = 'N';  //start game equip[[ped with escape pod?]]
            $scoop = 'N';  //start game equipped with fuel scoop?

            if (shipByEmail($username)) {
                throw new \Exception("$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>");
            }

            $stamp = date("Y-m-d H:i:s");

            $mturns = mturnsMax();
            $token = uuidv7();
            
            $playerData = [
                'ship_name' => $shipname,
                'ship_destroyed' => 'N',
                'character_name' => $character,
                'password' => md5($password),
                'email' => $username,
                'armor_pts' => (int) $start_armor,
                'credits' => (int) $start_credits,
                'ship_energy' => (int) $start_energy,
                'ship_fighters' => (int) $start_fighters,
                'turns' => (int) $mturns,
                'on_planet' => 'N',
                'dev_warpedit' => (int) $start_editors,
                'dev_genesis' => (int) $start_genesis,
                'dev_beacon' => (int) $start_beacon,
                'dev_emerwarp' => (int) $start_emerwarp,
                'dev_escapepod' => $escape,
                'dev_fuelscoop' => $scoop,
                'dev_minedeflector' => (int) $start_minedeflectors,
                'last_login' => $stamp,
                'interface' => 'N',
                'token' => $token,
                'trade_colonists' => 'Y',
                'trade_fighters' => 'N',
                'trade_torps' => 'N',
                'trade_energy' => 'Y',
                'cleared_defences' => NULL,
                'lang' => $language,
                'dhtml' => 'Y',
                'dev_lssd' => (int) $start_lssd
            ];

            shipCreate($playerData);
            
            $playerinfo = shipByEmail($username);

            $l_new_message = str_replace("[pass]", $password, $l_new_message);
            $link_to_game = "http://";
            $link_to_game .= ltrim($gamedomain, "."); //trim off the leading . if any
            $link_to_game .= str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
            mail("$username", "$l_new_topic", "$l_new_message\r\n\r\n$link_to_game", "From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());

            zoneCreate($playerinfo['ship_id'], $character . "'s Territory");
            bankAccountCreate($playerinfo['ship_id']);

            setcookie('token', $token, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
            playerlog($playerinfo['ship_id'], LOG_LOGIN, $ip);
            shipSetToken($playerinfo['ship_id'], $token);
            redirectTo('main.php?id=' . $playerinfo['ship_id']);
            break;
        case 'GET':
            include 'tpls/new.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    switch (requestMethod()) {
        case 'POST':
            echo responseJsonByException($ex);
            break;
        case 'GET':
            include 'tpls/new.tpl.php';

            break;
    }
}
