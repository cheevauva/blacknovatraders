<?php

declare(strict_types=1);

namespace BNT\Planet\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Planet\Entity\Planet;
use BNT\Planet\DAO\PlanetRetrieveManyByCriteriaDAO;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\Enum\BalanceEnum;

/*
 * NOTES on what this function does and how
 * 
 * We need to track what the player_id is and what corp they belong to if they belong to a corp,
 *   these two values are not passed in as arrays
 *   ship_id = the owner of the planet          ($ship_id = $prodpercentarray[ship_id])
 *   team_id = the corperation creators ship_id ($team_id = $prodpercentarray[team_id])
 * 
 * First we generate a list of values based on the commodity
 *   (ore, organics, goods, energy, fighters, torps, corp, team, sells)
 * 
 * Second we generate a second list of values based on the planet_id
 * Because team and ship_id are not arrays we do not pass them through the second list command.
 * When we write the ore production percent we also clear the selling and corp values out of the db
 * When we pass through the corp array we set the value to $team we grabbed out of the array.
 * in the sells and corp the prodpercent = the planet_id.
 * 
 * We run through the database checking to see if any planet production is greater than 100, or possibly negative
 *   if so we set the planet to the default values and report it to the player.
 * 
 * There has got to be a better way, but at this time I am not sure how to do it.
 * Off the top of my head if we could sort the data passed in, in order of planets we could check before we do the writes
 *   This would save us from having to run through the database a second time checking our work.
 * 
 * 
 * This should patch the game from being hack with planet Hack.
 * Patched by TMD [TheMightyDude]
 */
class PlanetChangeProductionServitor implements ServantInterface
{
    public Ship $ship;
    public array $changedPlanets = [];
    public bool $doIt = true;

    #[\Override]
    public function serve(): void
    {
        global $l_unnamed;

        $retrievePlanets = PlanetRetrieveManyByCriteriaDAO::new($this->container);
        $retrievePlanets->owner = $this->ship->ship_id;
        $retrievePlanets->serve();

        foreach ($retrievePlanets->planets as $planet) {
            $planet = Planet::as($planet);

            if (empty($planet->name)) {
                $planet->name = $l_unnamed;
            }

            if ($planet->prod_ore < 0) {
                $planet->prod_ore = 110;
            }

            if ($planet->prod_organics < 0) {
                $planet->prod_organics = 110;
            }

            if ($planet->prod_goods < 0) {
                $planet->prod_goods = 110;
            }

            if ($planet->prod_energy < 0) {
                $planet->prod_energy = 110;
            }

            if ($planet->prod_fighters < 0) {
                $planet->prod_fighters = 110;
            }

            if ($planet->prod_torp < 0) {
                $planet->prod_torp = 110;
            }

            if ($planet->prod_ore + $planet->prod_organics + $planet->prod_goods + $planet->prod_energy + $planet->prod_fighters + $planet->prod_torp > 100) {
                $planet->prod_ore = BalanceEnum::default_prod_ore;
                $planet->prod_organics = BalanceEnum::default_prod_organics;
                $planet->prod_goods = BalanceEnum::default_prod_goods;
                $planet->prod_energy = BalanceEnum::default_prod_energy;
                $planet->prod_fighters = BalanceEnum::default_prod_fighters;
                $planet->prod_torp = BalanceEnum::default_prod_torp;

                $this->changedPlanets[] = $planet;
            }
        }
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        foreach ($this->changedPlanets as $planet) {
            PlanetSaveDAO::call($this->container, $planet);
        }
    }
}
