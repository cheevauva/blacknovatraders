<?php

include 'config.php';

if (checklogin()) {
    die();
}

try {
    if ($playerinfo['turns'] < 1) {
        throw new \Exception($l_warp_turn);
    }

    if ($playerinfo['dev_warpedit'] < 1) {
        throw new \Exception($l_warp_none);
    }

    $sectorinfo = sectoryById($playerinfo['sector']);
    $zoneinfo = zoneById($sectorinfo['zone_id']);

    if ($zoneinfo['allow_warpedit'] == 'N') {
        throw new \Exception($l_warp_forbid);
    }

    if ($zoneinfo['allow_warpedit'] == 'L') {
        $zoneowner_info = $zoneinfo;

        $zoneteam = shipById($zoneowner_info['owner']);

        if ($zoneowner_info['owner'] != $playerinfo['ship_id']) {
            if (($zoneteam['team'] != $playerinfo['team']) || ($playerinfo['team'] == 0)) {
                throw new \Exception($l_warp_forbid);
            }
        }
    }

    $links = linksByStart($playerinfo['sector']);

    switch (requestMethod()) {
        case 'GET':
            // count($links) >= $link_max
            include 'tpls/warpedit.tpl.php';
            break;
        case 'POST':
            switch (fromPost('action')) {
                case 'link':
                    $target_sector = intval(fromPost('target_sector', new \Exception('target_sector')));
                    $oneway = fromPost('oneway');

                    if ($playerinfo['sector'] == $target_sector) {
                        throw new \Exception($l_warp_cantsame);
                    }

                    $tgSector = sectoryById($target_sector);

                    if (empty($tgSector)) {
                        throw new \Exception($l_warp_nosector);
                    }

                    $tgZone = zoneById($tgSector['zone_id']);

                    if ($tgZone['allow_warpedit'] == 'N' && !$oneway) {
                        throw new \Exception(str_replace("[target_sector]", $target_sector, $l_warp_twoerror));
                    }

                    if (count($links) >= $link_max) { // @todo refactoring to count sql
                        throw new \Exception($l_warp_sectex);
                    }

                    $linksFromTo = linksByStartAndDest($playerinfo['sector'], $target_sector);
                    $linksToFrom = linksByStartAndDest($target_sector, $playerinfo['sector']);

                    if (!empty($linksFromTo)) {
                        throw new \Exception(str_replace("[target_sector]", $target_sector, $l_warp_linked));
                    }

                    linkCreate($playerinfo['sector'], $target_sector);
                    shipDevWarpeditSub($playerinfo['ship_id'], 1);
                    shipTurn($playerinfo['ship_id'], 1);

                    if (!$oneway && empty($linksToFrom)) {
                        linkCreate($target_sector, $playerinfo['sector']);
                    }

                    redirectTo('warpedit.php');
                    break;
                case 'unlink':
                    $target_sector = intval(fromPost('target_sector', new \Exception('target_sector')));
                    $bothway = fromPost('bothway');

                    $tgSector = sectoryById($target_sector);

                    if (empty($tgSector)) {
                        throw new \Exception($l_warp_nosector);
                    }

                    $tgZone = zoneById($tgSector['zone_id']);

                    if ($tgZone['allow_warpedit'] == 'N' && $bothway) {
                        throw new \Exception(str_replace("[target_sector]", $target_sector, $l_warp_forbidtwo));
                    }

                    if (!linksByStartAndDest($playerinfo['sector'], $target_sector)) {
                        throw new \Exception(str_replace("[target_sector]", $target_sector, $l_warp_unlinked));
                    }
                    
                    linksDeleteByStartAndDest($playerinfo['sector'], $target_sector);
                    shipDevWarpeditSub($playerinfo['ship_id'], 1);
                    shipTurn($playerinfo['ship_id'], 1);
                    
                    if ($bothway) {
                        linksDeleteByStartAndDest($target_sector, $playerinfo['sector']);
                    }
                    redirectTo('warpedit.php');
                    break;
            }
            break;
    }
} catch (\Exception $ex) {
    echo responseJsonByException($ex);
}
