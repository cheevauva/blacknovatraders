<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\DAO\ShipRetrieveByEmailAndCharacterAndShipnameDAO;
use BNT\Ship\DAO\ShipCreateDAO;
use BNT\Enum\BalanceEnum;
use BNT\Email\Email;
use BNT\Ship\Ship;
use BNT\Zone\Zone;
use BNT\IBankAccount\IBankAccount;

class ShipNewServant implements ServantInterface
{

    public string $username;
    public string $character;
    public string $shipname;
    public string $password;
    public string $ip;
    public bool $start_lssd = false;  //do ships start with an lssd ?
    public int $start_editors = 0; //starting warp editors
    public int $start_minedeflectors = 0; //start mine deflectors
    public int $start_emerwarp = 0; //start emergency warp units
    public int $start_beacon = 0; //start space_beacons
    public int $start_genesis = 0; //starting genesis torps
    public bool $escape = false;  //start game equip[[ped with escape pod?]]
    public bool $scoop = false;  //start game equipped with fuel scoop
    //
    public Ship $ship;

    public function serve(): void
    {
        global $account_creation_closed;
        global $l_new_closed_message;
        global $l_new_blank;
        global $l_new_inusechar;
        global $l_new_inuse;
        global $l_new_inuseship;
        global $default_lang;
        global $l_new_topic;
        global $l_new_message;

        if (!empty($account_creation_closed)) {
            throw new \Exception($l_new_closed_message);
        }

        if (empty($this->username) || empty($this->character) || empty($this->shipname) || empty($this->password)) {
            throw new \Exception($l_new_blank);
        }

        $ship = ShipRetrieveByEmailAndCharacterAndShipnameDAO::call($this->username, $this->character, $this->shipname);

        if ($ship) {
            if ($ship->email === $this->username) {
                throw new \Exception($l_new_inuse);
            }

            if ($ship->character_name === $this->character) {
                throw new \Exception($l_new_inusechar);
            }

            if ($ship->ship_name === $this->shipname) {
                throw new \Exception($l_new_inuseship);
            }
        }

        $ship = new Ship;
        $ship->ship_name = $this->shipname;
        $ship->password($this->password);
        $ship->sector = 1;
        $ship->character_name = $this->character;
        $ship->email = $this->username;
        $ship->armor_pts = BalanceEnum::start_armor->val();
        $ship->credits = BalanceEnum::start_credits->val();
        $ship->ship_energy = BalanceEnum::start_energy->val();
        $ship->ship_fighters = BalanceEnum::start_fighters->val();
        $ship->turns = BalanceEnum::max_turns->val();
        $ship->dev_warpedit = $this->start_editors;
        $ship->dev_genesis = $this->start_genesis;
        $ship->dev_beacon = $this->start_beacon;
        $ship->dev_emerwarp = $this->start_emerwarp;
        $ship->dev_escapepod = $this->escape;
        $ship->dev_fuelscoop = $this->scoop;
        $ship->dev_minedeflector = $this->start_minedeflectors;
        $ship->interface = 'N';
        $ship->ip_address = $this->ip;
        $ship->lang = $default_lang;
        $ship->dev_lssd = $this->start_lssd;

        ShipCreateDAO::call($ship);

        $zone = new Zone;
        $zone->zone_name = sprintf("%s 's Territory", $this->character);
        $zone->owner = $ship->ship_id;

        $ibankAccount = new IBankAccount;
        $ibankAccount->ship_id = $ship->ship_id;

        $email = new Email;
        $email->email = $this->username;
        $email->subject = $l_new_topic;
        $email->subject = $l_new_message;
    }

    protected function old()
    {
        $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[ships]");
        $res = $query->fields;

        $mturns = $res[mturns];

        if ($mturns > $max_turns)
            $mturns = $max_turns;
    }

}
