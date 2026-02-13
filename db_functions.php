<?php

function linksByStartAndDest($link_start, $link_dest)
{
    return db()->fetchAll("SELECT * FROM links WHERE link_start = :link_start AND link_dest = :link_dest", [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function linksByStart($sectorId)
{
    return db()->fetchAll("SELECT * FROM links WHERE link_start= :sectorId ORDER BY link_dest ASC", [
        'sectorId' => $sectorId,
    ]);
}

function linksDeleteByStartAndDest($link_start, $link_dest)
{
    return db()->q('DELETE FROM links WHERE link_start= :link_start AND link_dest= :link_dest', [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function linkCreate($link_start, $link_dest)
{
    return db()->q("INSERT INTO links SET link_start= :link_start, link_dest= :link_dest", [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function defencesBySectorAndFighters($sectorId)
{
    return db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id=:sectorId and defence_type ='F' ORDER BY quantity DESC", [
        'sectorId' => $sectorId,
    ]);
}

function defencesCleanUp()
{
    return db()->q("delete from sector_defence where quantity <= 0 ");
}

function shipDevWarpeditSub($ship, $devWarpedit)
{
    return db()->q('UPDATE ships SET dev_warpedit = dev_warpedit - :dev_warpedit WHERE ship_id= :ship', [
        'ship' => $ship,
        'dev_warpedit' => $devWarpedit,
    ]);
}

function shipDevBeaconSub($ship, $beacon)
{
    return db()->q('UPDATE ships SET dev_beacon = dev_beacon - :beacon WHERE ship_id= :ship', [
        'ship' => $ship,
        'beacon' => $beacon,
    ]);
}

function shipTurn($shipId, $turns)
{
    return db()->q('UPDATE ships SET last_login = NOW(), turns = turns - :turns, turns_used = turns_used + :turns WHERE ship_id = :shipId', [
        'turns' => $turns,
        'shipId' => $shipId,
    ]);
}

function shipCreditsAdd($shipId, $credits)
{
    db()->q("UPDATE ships SET last_login = NOW(), credits = credits + :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipCreditsSub($shipId, $credits)
{
    db()->q("UPDATE ships SET last_login = NOW(), credits = credits - :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET last_login=NOW(), sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipRetreatToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET last_login=NOW(), turns=turns - 2, turns_used = turns_used + 2, sector=:sector where ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipResetClearedDefences($shipId)
{
    db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = '' WHERE ship_id= :shipId", [
        'shipId' => $shipId,
    ]);
}

function shipSetClearedDefences($shipId, $cleared_defences)
{
    db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = :cleared_defences WHERE ship_id= :shipId", [
        'shipId' => $shipId,
        'cleared_defences' => $cleared_defences,
    ]);
}

function shipMoveToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET last_login = NOW(), turns=turns - 1, turns_used = turns_used + 1, sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}
