<?

function create_schema()
{
global $maxlen_password;

// Delete all tables in the database
echo "Dropping all tables...";
mysql_query("DROP TABLE IF EXISTS ibank_accounts");
mysql_query("DROP TABLE IF EXISTS links");
mysql_query("DROP TABLE IF EXISTS planets");
mysql_query("DROP TABLE IF EXISTS traderoutes");
mysql_query("DROP TABLE IF EXISTS news");
mysql_query("DROP TABLE IF EXISTS newstypes");
mysql_query("DROP TABLE IF EXISTS newsactions");
mysql_query("DROP TABLE IF EXISTS ships");
mysql_query("DROP TABLE IF EXISTS teams");
mysql_query("DROP TABLE IF EXISTS universe");
mysql_query("DROP TABLE IF EXISTS zones");
mysql_query("DROP TABLE IF EXISTS messages");
mysql_query("DROP TABLE IF EXISTS furangee");
mysql_query("DROP TABLE IF EXISTS sector_defence");
mysql_query("DROP TABLE IF EXISTS scheduler");
mysql_query("DROP TABLE IF EXISTS ip_bans");
mysql_query("DROP TABLE IF EXISTS IGB_transfers");
mysql_query("DROP TABLE IF EXISTS logs");
mysql_query("DROP TABLE IF EXISTS bn_news");
echo "All tables have been dropped...<BR>";

// Create database schema
echo "Creating tables...<BR>";
echo "Creating table: links...";
mysql_query("CREATE TABLE links(" .
            "link_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "link_start bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "link_dest bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "PRIMARY KEY (link_id)," .
            "KEY link_start (link_start)," .
            "KEY link_dest (link_dest)" .
            ")");
echo "created.<BR>";

echo "Creating table: planets...";
mysql_query("CREATE TABLE planets(" .
            "planet_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "sector_id bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "name tinytext," .
            "organics bigint(20) DEFAULT '0' NOT NULL," .
            "ore bigint(20) DEFAULT '0' NOT NULL," .
            "goods bigint(20) DEFAULT '0' NOT NULL," .
            "energy bigint(20) DEFAULT '0' NOT NULL," .
            "colonists bigint(20) DEFAULT '0' NOT NULL," .
            "credits bigint(20) DEFAULT '0' NOT NULL," .
            "fighters bigint(20) DEFAULT '0' NOT NULL," .
            "torps bigint(20) DEFAULT '0' NOT NULL," .
            "owner bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "corp bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "base enum('Y','N') DEFAULT 'N' NOT NULL," .
            "sells enum('Y','N') DEFAULT 'N' NOT NULL," .
            "prod_organics float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_ore float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_goods float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_energy float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_fighters float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
            "prod_torp float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
            "defeated enum('Y','N') DEFAULT 'N' NOT NULL," .
            "PRIMARY KEY (planet_id)," .
            "KEY owner (owner)," .
            "KEY corp (corp)" .
            ")") or die ("blerg!"); // now that is one of the coolest error messages I have seen in a while....
echo "created.<BR>";

echo "Creating table: traderoutes...";
mysql_query("CREATE TABLE traderoutes(" .
            "traderoute_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "source_id bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "dest_id bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "source_type enum('P','L') DEFAULT 'P' NOT NULL," .
            "dest_type enum('P','L') DEFAULT 'P' NOT NULL," .
            "move_type enum('R','W') DEFAULT 'W' NOT NULL," .
            "owner bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "circuit enum('1','2') DEFAULT '2' NOT NULL," .
            "PRIMARY KEY (traderoute_id)," .
            "KEY owner (owner)" .
            ")") or die ("blerg!");
echo "created.<BR>";

echo "Creating table: ships...";
mysql_query("CREATE TABLE ships(" .
            "ship_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "ship_name char(20)," .
            "ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL," .
            "character_name char(20) NOT NULL," .
            "password char($maxlen_password) NOT NULL," .
            "email char(40) NOT NULL," .
            "hull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "engines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "power tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "computer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "sensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "beams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "torp_launchers tinyint(3) DEFAULT '0' NOT NULL," .
            "torps bigint(20) DEFAULT '0' NOT NULL," .
            "shields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "armour tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "armour_pts bigint(20) DEFAULT '0' NOT NULL," .
            "cloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "credits bigint(20) DEFAULT '0' NOT NULL," .
            "sector bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "ship_ore bigint(20) DEFAULT '0' NOT NULL," .
            "ship_organics bigint(20) DEFAULT '0' NOT NULL," .
            "ship_goods bigint(20) DEFAULT '0' NOT NULL," .
            "ship_energy bigint(20) DEFAULT '0' NOT NULL," .
            "ship_colonists bigint(20) DEFAULT '0' NOT NULL," .
            "ship_fighters bigint(20) DEFAULT '0' NOT NULL," .
            "turns smallint(4) DEFAULT '0' NOT NULL," .
            "ship_damage set('engines','power','computer','sensors','torps','cloak','shields') NOT NULL," .
            "on_planet enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_warpedit smallint(5) DEFAULT '0' NOT NULL," .
            "dev_genesis smallint(5) DEFAULT '0' NOT NULL," .
            "dev_beacon smallint(5) DEFAULT '0' NOT NULL," .
            "dev_emerwarp smallint(5) DEFAULT '0' NOT NULL," .
            "dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_minedeflector bigint(20) DEFAULT '0' NOT NULL," .
            "turns_used bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "last_login datetime," .
            "rating bigint(20) DEFAULT '0' NOT NULL," .
            "score bigint(20) DEFAULT '0' NOT NULL," .
            "team bigint(20) DEFAULT '0' NOT NULL," .
            "team_invite bigint(20) DEFAULT '0' NOT NULL," .
            "interface enum('N','O') DEFAULT 'N' NOT NULL," .
		        "ip_address tinytext NOT NULL," .
            "planet_id bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "preset1 bigint(20) DEFAULT '0' NOT NULL," .
            "preset2 bigint(20) DEFAULT '0' NOT NULL," .
            "preset3 bigint(20) DEFAULT '0' NOT NULL," .
            "trade_colonists enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
            "trade_fighters enum('Y', 'N') DEFAULT 'N' NOT NULL," .
            "trade_torps enum('Y', 'N') DEFAULT 'N' NOT NULL," .
            "trade_energy enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
            "cleared_defences tinytext," .
            "lang varchar(30) DEFAULT 'english.inc' NOT NULL," .
            "dhtml enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
            "PRIMARY KEY (email)," .
            "KEY email (email)," .
            "KEY sector (sector)," .
            "KEY ship_destroyed (ship_destroyed)," .
            "KEY on_planet (on_planet)," .
            "KEY team (team)," .
            "KEY ship_id (ship_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: universe...";
mysql_query("CREATE TABLE universe(" .
            "sector_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "sector_name tinytext," .
            "zone_id bigint(20) DEFAULT '0' NOT NULL," .
            "port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL," .
            "port_organics bigint(20) DEFAULT '0' NOT NULL," .
            "port_ore bigint(20) DEFAULT '0' NOT NULL," .
            "port_goods bigint(20) DEFAULT '0' NOT NULL," .
            "port_energy bigint(20) DEFAULT '0' NOT NULL," .
            "KEY zone_id (zone_id)," .
            "KEY port_type (port_type)," .
            "beacon tinytext," .
            "angle1 float(10,2) DEFAULT '0.00' NOT NULL," .
            "angle2 float(10,2) DEFAULT '0.00' NOT NULL," .
            "distance bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "fighters bigint(20) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY (sector_id)," .
            "KEY sector_id (sector_id)," .
            "UNIQUE sector_id_2 (sector_id)," .
            "UNIQUE sector_id_3 (sector_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: zones...";
mysql_query("CREATE TABLE zones(" .
            "zone_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "zone_name tinytext," .
            "owner bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "corp_zone enum('Y', 'N') DEFAULT 'N' NOT NULL," .
            "allow_beacon enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_planetattack enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_warpedit enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_planet enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
            "allow_trade enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
            "allow_defenses enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
            "max_hull bigint(20) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY(zone_id)," .
            "KEY zone_id(zone_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: ibank_accounts...";
mysql_query("CREATE TABLE ibank_accounts(" .
            "ship_id bigint(20) DEFAULT '0' NOT NULL," .
            "balance bigint(20) DEFAULT '0'," .
            "loan bigint(20)  DEFAULT '0'," .
            "PRIMARY KEY(ship_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: IGB_transfers...";
mysql_query("CREATE TABLE IGB_transfers(" .
            "transfer_id bigint(20) DEFAULT '0' NOT NULL auto_increment," .
            "source_id bigint(20) DEFAULT '0' NOT NULL," .
            "dest_id bigint(20) DEFAULT '0' NOT NULL," .
            "time TIMESTAMP(14)," .
            "PRIMARY KEY(transfer_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: teams...";
mysql_query("CREATE TABLE teams(" .
            "id bigint(20) DEFAULT '0' NOT NULL," .
            "creator bigint(20) DEFAULT '0'," .
            "team_name tinytext," .
            "description tinytext," .
            "number_of_members tinyint(3) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY(id)" .
            ")");
echo "created.<BR>";

echo "Creating table: bn_news...";


mysql_query("CREATE TABLE bn_news (" .
   "news_id int(11) DEFAULT '0' NOT NULL auto_increment," .
   "headline varchar(100) NOT NULL," .
   "newstext text NOT NULL," .
   "user_id int(11)," .
   "date timestamp(8)," .
   "news_type varchar(10)," .
   "PRIMARY KEY (news_id)," .
   "KEY news_id (news_id)," .
   "UNIQUE news_id_2 (news_id)" .
   ")");
echo "created.<BR>";

echo "Creating default values for the reference tables...";
//Insert default values into the reference tables
mysql_query("INSERT INTO newstypes VALUES ( 'GEN', 'General News')");
mysql_query("INSERT INTO newstypes VALUES ( 'BAT', 'Battle Results')");
mysql_query("INSERT INTO newstypes VALUES ( 'SCAN', 'Unauthorized Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'SSCAN', 'Ship to Ship Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'PSCAN', 'Planet Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'PDEF', 'Planet Defeated')");
mysql_query("INSERT INTO newsactions VALUES( 'SDEF', 'Ship Defeated')");
echo "created.<BR>";

echo "Creating internal messaging tables...";
mysql_query("CREATE TABLE messages (" .
             "ID bigint(20) NOT NULL auto_increment," .
             "sender_id bigint(20) NOT NULL default '0'," .
             "recp_id bigint(20) NOT NULL default '0'," .
             "subject varchar(250) NOT NULL default ''," .
             "message longtext NOT NULL," .
             "notified enum('Y','N') NOT NULL default 'N'," .
             "PRIMARY KEY  (ID) " .
             ") TYPE=MyISAM");
echo "created.<BR>";

echo "Creating table: furangee...";
mysql_query("CREATE TABLE furangee(" .
            "furangee_id char(40) NOT NULL," .
            "active enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "aggression smallint(5) DEFAULT '0' NOT NULL," .
            "orders smallint(5) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY (furangee_id)," .
            "KEY furangee_id (furangee_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: sector_defence...";
mysql_query("CREATE TABLE sector_defence(" .
            "defence_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "ship_id bigint(20) DEFAULT '0' NOT NULL," .
            "sector_id bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "defence_type enum('M','F') DEFAULT 'M' NOT NULL," .
            "quantity bigint(20) DEFAULT '0' NOT NULL," .
            "fm_setting enum('attack','toll') DEFAULT 'toll' NOT NULL," .
            "PRIMARY KEY (defence_id)," .
            "KEY sector_id (sector_id)," .
            "KEY ship_id (ship_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: scheduler...";
mysql_query("CREATE TABLE scheduler(" .
            "sched_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "loop enum('Y','N') DEFAULT 'N' NOT NULL," .
            "ticks_left bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "ticks_full bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "spawn bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "file varchar(30) NOT NULL," .
            "extra_info varchar(50) NOT NULL," .
            "PRIMARY KEY (sched_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: ip_bans...";
mysql_query("CREATE TABLE ip_bans(" .
            "ban_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "ban_mask varchar(16) NOT NULL," .
            "PRIMARY KEY (ban_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: logs...";
mysql_query("CREATE TABLE logs(" .
            "log_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "ship_id bigint(20) DEFAULT '0' NOT NULL," .
            "type mediumint(5) DEFAULT '0' NOT NULL," .
            "time TIMESTAMP(14)," .
            "data varchar(255)," .
            "PRIMARY KEY (log_id)" .
            ")");
echo "created.<BR>";

//Finished
echo "Database schema creation complete.<BR>";
}

?>