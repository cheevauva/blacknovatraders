<?php

include("config.php");
include("languages/$lang");

connectdb();

$title = $l_new_title2;

try {
    if ($account_creation_closed) {
        throw new \Exception($l_new_closed_message);
    }

    $username = fromPost('username', new \Exception($l_new_blank));
    $character = fromPost('character', new \Exception($l_new_blank));
    $shipname = fromPost('shipname', new \Exception($l_new_blank));
    $password = fromPost('password', new \Exception($l_login_pw . ' ' . $l_none));

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

    if (sqlGetPlayerByEmail($username)) {
        throw new \Exception("$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>");
    }

    $stamp = date("Y-m-d H:i:s");
    $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM ships");
    $res = $query->fields;

    $mturns = $res[mturns];

    if ($mturns > $max_turns) {
        $mturns = $max_turns;
    }

    $token = uuidv7();

    $sql = "
    INSERT INTO ships (
        ship_name, ship_destroyed, character_name, password, email, 
        armor_pts, credits, ship_energy, ship_fighters, turns, 
        on_planet, dev_warpedit, dev_genesis, dev_beacon, dev_emerwarp, 
        dev_escapepod, dev_fuelscoop, dev_minedeflector, last_login, 
        interface, token, trade_colonists, trade_fighters, trade_torps, 
        trade_energy, cleared_defences, lang, dhtml, dev_lssd
    ) VALUES (
        :ship_name, :ship_destroyed, :character_name, :password, :email,
        :armor_pts, :credits, :ship_energy, :ship_fighters, :turns,
        :on_planet, :dev_warpedit, :dev_genesis, :dev_beacon, :dev_emerwarp,
        :dev_escapepod, :dev_fuelscoop, :dev_minedeflector, :last_login,
        :interface, :token, :trade_colonists, :trade_fighters, :trade_torps,
        :trade_energy, :cleared_defences, :lang, :dhtml, :dev_lssd
    )
    "; 

    $stmt = db()->PrepareStmt($sql);
    $stmt->InParameter($shipname, ':ship_name');
    $stmt->InParameter('N', ':ship_destroyed');
    $stmt->InParameter($character, ':character_name');
    $stmt->InParameter(md5($password), ':password');
    $stmt->InParameter($username, ':email');
    $stmt->InParameter('N', ':on_planet');
    $stmt->InParameter($escape, ':dev_escapepod');
    $stmt->InParameter($scoop, ':dev_fuelscoop');
    $stmt->InParameter($stamp, ':last_login');
    $stmt->InParameter('N', ':interface');
    $stmt->InParameter($token, ':token');
    $stmt->InParameter('Y', ':trade_colonists');
    $stmt->InParameter('N', ':trade_fighters');
    $stmt->InParameter('N', ':trade_torps');
    $stmt->InParameter('Y', ':trade_energy');
    $stmt->InParameter(NULL, ':cleared_defences');
    $stmt->InParameter($default_lang, ':lang');
    $stmt->InParameter('Y', ':dhtml');
    $stmt->InParameter((int) $start_armor, ':armor_pts');
    $stmt->InParameter((int) $start_credits, ':credits');
    $stmt->InParameter((int) $start_energy, ':ship_energy');
    $stmt->InParameter((int) $start_fighters, ':ship_fighters');
    $stmt->InParameter((int) $mturns, ':turns');
    $stmt->InParameter((int) $start_editors, ':dev_warpedit');
    $stmt->InParameter((int) $start_genesis, ':dev_genesis');
    $stmt->InParameter((int) $start_beacon, ':dev_beacon');
    $stmt->InParameter((int) $start_emerwarp, ':dev_emerwarp');
    $stmt->InParameter((int) $start_minedeflectors, ':dev_minedeflector');
    $stmt->InParameter((int) $start_lssd, ':dev_lssd');

    if (!$stmt->Execute()) {
        throw new \Exception($db->ErrorMsg());
    }

    $playerinfo = sqlGetPlayerByEmail($username);

    $l_new_message = str_replace("[pass]", $password, $l_new_message);
    $link_to_game = "http://";
    $link_to_game .= ltrim($gamedomain, "."); //trim off the leading . if any
    $link_to_game .= str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
    mail("$username", "$l_new_topic", "$l_new_message\r\n\r\n$link_to_game", "From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());

    $sql = "
    INSERT INTO zones VALUES(
        NULL, 
        :zone_name, 
        :ship_id, 
        :allow_attack, 
        :allow_planetattack, 
        :allow_trade, 
        :allow_defenses, 
        :allow_shipyard, 
        :allow_build, 
        :allow_energy, 
        :allow_warpedit, 
        0
    )
    ";
    $stmt1 = $db->PrepareStmt($sql);
    $stmt1->InParameter($character . "'s Territory", ':zone_name');
    $stmt1->InParameter((int) $playerinfo['ship_id'], ':ship_id');
    $stmt1->InParameter('N', ':allow_attack');
    $stmt1->InParameter('Y', ':allow_planetattack');
    $stmt1->InParameter('Y', ':allow_trade');
    $stmt1->InParameter('Y', ':allow_defenses');
    $stmt1->InParameter('Y', ':allow_shipyard');
    $stmt1->InParameter('Y', ':allow_build');
    $stmt1->InParameter('Y', ':allow_energy');
    $stmt1->InParameter('Y', ':allow_warpedit');

    $result1 = $stmt1->Execute();

    $stmt2 = $db->PrepareStmt("INSERT INTO ibank_accounts (ship_id, balance, loan)   VALUES(:ship_id, :balance, :loan)");
    $stmt2->InParameter((int) $playerinfo['ship_id'], ':ship_id');
    $stmt2->InParameter(0, ':balance');
    $stmt2->InParameter(0, ':loan');

    $result2 = $stmt2->Execute();

    setcookie('token', $token, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
    playerlog($playerinfo['ship_id'], LOG_LOGIN, $ip);
    sqlUpdateLogin($playerinfo['ship_id'], $token);
    header('Location:' . "main.php?id=" . $playerinfo['ship_id']);
    die;
} catch (\Exception $ex) {
    include("header.php");

    bigtitle();

    echo $ex->getMessage() . '<br>' . $l_new_err;

    include("footer.php");
}

