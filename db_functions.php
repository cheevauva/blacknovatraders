<?php

function ipBansCheck($ip)
{
    return db()->column("SELECT * FROM ip_bans WHERE :ip LIKE ban_mask", [
        'ip' => $ip,
    ]);
}


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
