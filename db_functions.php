<?php

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

function shipTurn($shipId, $turns)
{
    return db()->q('UPDATE ships SET turns = turns - :turns, turns_used = turns_used + :turns WHERE ship_id = :shipId', [
        'turns' => $turns,
        'shipId' => $shipId,
    ]);
}

function shipCreditsAdd($shipId, $credits)
{
    db()->q("UPDATE ships SET credits = credits + :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipCreditsSub($shipId, $credits)
{
    db()->q("UPDATE ships SET credits = credits - :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipRetreatToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET turns=turns - 2, turns_used = turns_used + 2, sector=:sector where ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipResetClearedDefences($shipId)
{
    db()->q("UPDATE ships SET cleared_defences = '' WHERE ship_id= :shipId", [
        'shipId' => $shipId,
    ]);
}

function shipSetClearedDefences($shipId, $cleared_defences)
{
    db()->q("UPDATE ships SET cleared_defences = :cleared_defences WHERE ship_id= :shipId", [
        'shipId' => $shipId,
        'cleared_defences' => $cleared_defences,
    ]);
}

function shipMoveToSector($shipId, $sector)
{
    db()->q("UPDATE ships SET turns=turns - 1, turns_used = turns_used + 1, sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}
