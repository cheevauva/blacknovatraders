<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

class ShipCheckNewbieServant extends \UUA\Servant
{

    public $ship;
    public $isNewbie;

    public function serve()
    {
        global $newbie_hull;
        global $newbie_engines;
        global $newbie_power;
        global $newbie_computer;
        global $newbie_sensors;
        global $newbie_armor;
        global $newbie_shields;
        global $newbie_beams;
        global $newbie_torp_launchers;
        global $newbie_cloak;
        
        
        $isNewbie = $this->ship['hull'] <= $newbie_hull;
        $isNewbie &= $this->ship['engines'] <= $newbie_engines;
        $isNewbie &= $this->ship['power'] <= $newbie_power;
        $isNewbie &= $this->ship['computer'] <= $newbie_computer;
        $isNewbie &= $this->ship['sensors'] <= $newbie_sensors;
        $isNewbie &= $this->ship['armor'] <= $newbie_armor;
        $isNewbie &= $this->ship['shields'] <= $newbie_shields;
        $isNewbie &= $this->ship['beams'] <= $newbie_beams;
        $isNewbie &= $this->ship['torp_launchers'] <= $newbie_torp_launchers;
        $isNewbie &= $this->ship['cloak'] <= $newbie_cloak;

        $this->isNewbie = !empty($this->isNewbie);
    }
}
