<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipGenScoreDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $ship;

    #[\Override]
    public function serve(): void
    {
        global $ore_price, $organics_price, $goods_price, $energy_price, $upgrade_cost, $upgrade_factor;
        global $dev_genesis_price, $dev_beacon_price, $dev_emerwarp_price, $dev_warpedit_price;
        global $dev_minedeflector_price, $dev_escapepod_price, $dev_fuelscoop_price, $dev_lssd_price;
        global $fighter_price, $torpedo_price, $armor_price, $colonist_price;
        global $base_credits;

        $sql = "
        SELECT 
            ROUND(SQRT(
                (
                    (
                        ROUND(POW(:upgrade_factor, hull)) + 
                        ROUND(POW(:upgrade_factor, engines)) + 
                        ROUND(POW(:upgrade_factor, power)) + 
                        ROUND(POW(:upgrade_factor, computer)) + 
                        ROUND(POW(:upgrade_factor, sensors)) + 
                        ROUND(POW(:upgrade_factor, beams)) + 
                        ROUND(POW(:upgrade_factor, torp_launchers)) + 
                        ROUND(POW(:upgrade_factor, shields)) + 
                        ROUND(POW(:upgrade_factor, armor)) + 
                        ROUND(POW(:upgrade_factor, cloak))
                    ) * :upgrade_cost
                )
                + (ships.torps * :torpedo_price)
                + (armor_pts * :armor_price)
                + (ship_ore * :ore_price)
                + (ship_organics * :organics_price)
                + (ship_goods * :goods_price)
                + (ship_energy * :energy_price)
                + (ship_colonists * :colonist_price)
                + (ship_fighters * :fighter_price)
                + (dev_warpedit * :dev_warpedit_price)
                + (dev_genesis * :dev_genesis_price)
                + (dev_beacon * :dev_beacon_price)
                + (dev_emerwarp * :dev_emerwarp_price)
                + IF(dev_escapepod = 'Y', :dev_escapepod_price, 0)
                + IF(dev_fuelscoop = 'Y', :dev_fuelscoop_price, 0)
                + IF(dev_lssd = 'Y', :dev_lssd_price, 0)
                + (dev_minedeflector * :dev_minedeflector_price)
                + ships.credits
                + IFNULL((
                    SELECT 
                        IF(COUNT(*) > 0,
                            SUM(organics) * :organics_price +
                            SUM(ore) * :ore_price +
                            SUM(goods) * :goods_price +
                            SUM(energy) * :energy_price +
                            SUM(colonists) * :colonist_price +
                            SUM(fighters) * :fighter_price +
                            IF(base = 'Y', :base_credits + SUM(torps) * :torpedo_price, 0) +
                            SUM(credits)
                        , 0)
                    FROM 
                        planets 
                    WHERE 
                        owner = ships.ship_id
                ), 0)
                + IFNULL((
                    SELECT 
                        balance - loan 
                    FROM 
                        ibank_accounts 
                    WHERE 
                        ship_id = ships.ship_id
                ), 0)
            )) AS score
        FROM
            ships 
        WHERE 
            ship_id = :sid AND 
            ship_destroyed = 'N'
        ";

        $this->db()->q("UPDATE ships SET score = :score WHERE ship_id = :sid", [
            'score' => $this->db()->fetchColumn($sql, [
                'sid' => $this->ship,
                'upgrade_factor' => $upgrade_factor,
                'upgrade_cost' => $upgrade_cost,
                'torpedo_price' => $torpedo_price,
                'armor_price' => $armor_price,
                'ore_price' => $ore_price,
                'organics_price' => $organics_price,
                'goods_price' => $goods_price,
                'energy_price' => $energy_price,
                'colonist_price' => $colonist_price,
                'fighter_price' => $fighter_price,
                'dev_warpedit_price' => $dev_warpedit_price,
                'dev_genesis_price' => $dev_genesis_price,
                'dev_beacon_price' => $dev_beacon_price,
                'dev_emerwarp_price' => $dev_emerwarp_price,
                'dev_escapepod_price' => $dev_escapepod_price,
                'dev_fuelscoop_price' => $dev_fuelscoop_price,
                'dev_lssd_price' => $dev_lssd_price,
                'dev_minedeflector_price' => $dev_minedeflector_price,
                'base_credits' => $base_credits
            ]),
            'sid' => $this->ship,
        ]);
    }
}
