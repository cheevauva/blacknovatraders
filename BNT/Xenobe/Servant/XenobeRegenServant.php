<?php

declare(strict_types=1);

namespace BNT\Xenobe\Servant;

class XenobeRegenServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;

    #[\Override]
    public function serve(): void
    {
        global $xen_unemployment;

        $this->playerinfo['credits'] = $this->playerinfo['credits'] + $xen_unemployment;

        $maxenergy = NUM_ENERGY($this->playerinfo['power']);

        if ($this->playerinfo['ship_energy'] <= ($maxenergy - 50)) {
            $this->playerinfo['ship_energy'] = $this->playerinfo['ship_energy'] + round(($maxenergy - $this->playerinfo['ship_energy']) / 2);
        }

        $maxarmor = NUM_ARMOUR($this->playerinfo['armor']);

        if ($this->playerinfo['armor_pts'] <= ($maxarmor - 50)) {
            $this->playerinfo['armor_pts'] = $this->playerinfo['armor_pts'] + round(($maxarmor - $this->playerinfo['armor_pts']) / 2);
        }

        $available_fighters = NUM_FIGHTERS($this->playerinfo['computer']) - $this->playerinfo['ship_fighters'];

        if (($this->playerinfo['credits'] > 5) && ($available_fighters > 0)) {
            if (round($this->playerinfo['credits'] / 6) > $available_fighters) {
                $purchase = ($available_fighters * 6);
                $this->playerinfo['credits'] = $this->playerinfo['credits'] - $purchase;
                $this->playerinfo['ship_fighters'] = $this->playerinfo['ship_fighters'] + $available_fighters;
            }

            if (round($this->playerinfo['credits'] / 6) <= $available_fighters) {
                $purchase = (round($this->playerinfo['credits'] / 6));
                $this->playerinfo['ship_fighters'] = $this->playerinfo['ship_fighters'] + $purchase;
                $this->playerinfo['credits'] = 0;
            }
        }

        $available_torpedoes = NUM_TORPEDOES($this->playerinfo['torp_launchers']) - $this->playerinfo['torps'];

        if (($this->playerinfo['credits'] > 2) && ($available_torpedoes > 0)) {
            if (round($this->playerinfo['credits'] / 3) > $available_torpedoes) {
                $purchase = ($available_torpedoes * 3);
                $this->playerinfo['credits'] = $this->playerinfo['credits'] - $purchase;
                $this->playerinfo['torps'] = $this->playerinfo['torps'] + $available_torpedoes;
            }

            if (round($this->playerinfo['credits'] / 3) <= $available_torpedoes) {
                $purchase = (round($this->playerinfo['credits'] / 3));
                $this->playerinfo['torps'] = $this->playerinfo['torps'] + $purchase;
                $this->playerinfo['credits'] = 0;
            }
        }


        $this->playerinfoUpdate();
    }
}
