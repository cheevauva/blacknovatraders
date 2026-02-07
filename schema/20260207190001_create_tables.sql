CREATE TABLE links 
(
    link_id int unsigned NOT NULL auto_increment,
    link_start int unsigned DEFAULT '0' NOT NULL,
    link_dest int unsigned DEFAULT '0' NOT NULL,
    link_type tinyint(3) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (link_id),
    KEY link_start (link_start),
    KEY link_dest (link_dest)
);

CREATE TABLE config
(
    name varchar(50),
    value text,
    UNIQUE name(name)
);

CREATE TABLE planets 
(
    planet_id int unsigned NOT NULL auto_increment,
    sector_id int unsigned DEFAULT '0' NOT NULL,
    name tinytext,
    organics bigint(20) DEFAULT '0' NOT NULL,
    ore bigint(20) DEFAULT '0' NOT NULL,
    goods bigint(20) DEFAULT '0' NOT NULL,
    energy bigint(20) DEFAULT '0' NOT NULL,
    colonists bigint(20) DEFAULT '0' NOT NULL,
    credits bigint(20) DEFAULT '0' NOT NULL,
    fighters bigint(20) DEFAULT '0' NOT NULL,
    torps bigint(20) DEFAULT '0' NOT NULL,
    owner int unsigned DEFAULT '0' NOT NULL,
    corp int unsigned DEFAULT '0' NOT NULL,
    base enum('Y','N') DEFAULT 'N' NOT NULL,
    sells enum('Y','N') DEFAULT 'N' NOT NULL,
    prod_organics int DEFAULT '20.0' NOT NULL,
    prod_ore int DEFAULT '0' NOT NULL,
    prod_goods int DEFAULT '0' NOT NULL,
    prod_energy int DEFAULT '0' NOT NULL,
    prod_fighters int DEFAULT '0' NOT NULL,
    prod_torp int DEFAULT '0' NOT NULL,
    defeated enum('Y','N') DEFAULT 'N' NOT NULL,
    PRIMARY KEY (planet_id),
    KEY owner (owner),
    KEY corp (corp)
);

CREATE TABLE traderoutes 
(
    traderoute_id int unsigned NOT NULL auto_increment,
    source_id int unsigned DEFAULT '0' NOT NULL,
    dest_id int unsigned DEFAULT '0' NOT NULL,
    source_type enum('P','L','C','D') DEFAULT 'P' NOT NULL,
    dest_type enum('P','L','C','D') DEFAULT 'P' NOT NULL,
    move_type enum('R','W') DEFAULT 'W' NOT NULL,
    owner int unsigned DEFAULT '0' NOT NULL,
    circuit enum('1','2') DEFAULT '2' NOT NULL,
    PRIMARY KEY (traderoute_id),
    KEY owner (owner)
);

CREATE TABLE ships 
(
    ship_id int unsigned NOT NULL auto_increment,
    ship_name char(20),
    ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL,
    character_name char(20) NOT NULL,
    password varchar(36) NOT NULL,
    email char(60) NOT NULL,
    hull tinyint(3) unsigned DEFAULT '0' NOT NULL,
    engines tinyint(3) unsigned DEFAULT '0' NOT NULL,
    power tinyint(3) unsigned DEFAULT '0' NOT NULL,
    computer tinyint(3) unsigned DEFAULT '0' NOT NULL,
    sensors tinyint(3) unsigned DEFAULT '0' NOT NULL,
    beams tinyint(3) unsigned DEFAULT '0' NOT NULL,
    torp_launchers tinyint(3) DEFAULT '0' NOT NULL,
    torps bigint(20) DEFAULT '0' NOT NULL,
    shields tinyint(3) unsigned DEFAULT '0' NOT NULL,
    armor tinyint(3) unsigned DEFAULT '0' NOT NULL,
    armor_pts bigint(20) DEFAULT '0' NOT NULL,
    cloak tinyint(3) unsigned DEFAULT '0' NOT NULL,
    credits bigint(20) DEFAULT '0' NOT NULL,
    sector int unsigned DEFAULT '0' NOT NULL,
    ship_ore bigint(20) DEFAULT '0' NOT NULL,
    ship_organics bigint(20) DEFAULT '0' NOT NULL,
    ship_goods bigint(20) DEFAULT '0' NOT NULL,
    ship_energy bigint(20) DEFAULT '0' NOT NULL,
    ship_colonists bigint(20) DEFAULT '0' NOT NULL,
    ship_fighters bigint(20) DEFAULT '0' NOT NULL,
    ship_damage smallint(5) DEFAULT '0' NOT NULL,
    turns smallint(4) DEFAULT '0' NOT NULL,
    on_planet enum('Y','N') DEFAULT 'N' NOT NULL,
    dev_warpedit smallint(5) DEFAULT '0' NOT NULL,
    dev_genesis smallint(5) DEFAULT '0' NOT NULL,
    dev_beacon smallint(5) DEFAULT '0' NOT NULL,
    dev_emerwarp smallint(5) DEFAULT '0' NOT NULL,
    dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL,
    dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL,
    dev_minedeflector bigint(20) DEFAULT '0' NOT NULL,
    turns_used int unsigned DEFAULT '0' NOT NULL,
    last_login datetime,
    rating int DEFAULT '0' NOT NULL,
    score int DEFAULT '0' NOT NULL,
    team int DEFAULT '0' NOT NULL,
    team_invite int DEFAULT '0' NOT NULL,
    interface enum('N','O') DEFAULT 'N' NOT NULL,
    token tinytext NULL,
    planet_id int unsigned DEFAULT '0' NOT NULL,
    preset1 int DEFAULT '0' NOT NULL,
    preset2 int DEFAULT '0' NOT NULL,
    preset3 int DEFAULT '0' NOT NULL,
    trade_colonists enum('Y', 'N') DEFAULT 'Y' NOT NULL,
    trade_fighters enum('Y', 'N') DEFAULT 'N' NOT NULL,
    trade_torps enum('Y', 'N') DEFAULT 'N' NOT NULL,
    trade_energy enum('Y', 'N') DEFAULT 'Y' NOT NULL,
    cleared_defences tinytext,
    lang varchar(30) DEFAULT 'english.inc' NOT NULL,
    dhtml enum('Y', 'N') DEFAULT 'Y' NOT NULL,
    dev_lssd enum('Y','N') DEFAULT 'Y' NOT NULL,
    role varchar(15) DEFAULT 'user' NOT NULL,
    PRIMARY KEY (email),
    KEY email (email),
    KEY sector (sector),
    KEY ship_destroyed (ship_destroyed),
    KEY on_planet (on_planet),
    KEY team (team),
    KEY ship_id (ship_id),
    KEY token (token(255))
);

CREATE TABLE universe 
(
    sector_id int unsigned NOT NULL auto_increment,
    sector_name tinytext,
    zone_id int DEFAULT '0' NOT NULL,
    port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL,
    port_organics bigint(20) DEFAULT '0' NOT NULL,
    port_ore bigint(20) DEFAULT '0' NOT NULL,
    port_goods bigint(20) DEFAULT '0' NOT NULL,
    port_energy bigint(20) DEFAULT '0' NOT NULL,
    KEY zone_id (zone_id),
    KEY port_type (port_type),
    beacon tinytext,
    angle1 float(10,2) DEFAULT '0.00' NOT NULL,
    angle2 float(10,2) DEFAULT '0.00' NOT NULL,
    distance bigint(20) unsigned DEFAULT '0' NOT NULL,
    fighters bigint(20) DEFAULT '0' NOT NULL,
    PRIMARY KEY (sector_id)
);

CREATE TABLE zones
(
    zone_id int unsigned NOT NULL auto_increment,
    zone_name tinytext,
    owner int unsigned DEFAULT '0' NOT NULL,
    corp_zone enum('Y', 'N') DEFAULT 'N' NOT NULL,
    allow_beacon enum('Y','N','L') DEFAULT 'Y' NOT NULL,
    allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL,
    allow_planetattack enum('Y','N') DEFAULT 'Y' NOT NULL,
    allow_warpedit enum('Y','N','L') DEFAULT 'Y' NOT NULL,
    allow_planet enum('Y','L','N') DEFAULT 'Y' NOT NULL,
    allow_trade enum('Y','L','N') DEFAULT 'Y' NOT NULL,
    allow_defenses enum('Y','L','N') DEFAULT 'Y' NOT NULL,
    max_hull int DEFAULT '0' NOT NULL,
    PRIMARY KEY(zone_id),
    KEY zone_id(zone_id)
);

CREATE TABLE ibank_accounts
(
    ship_id int DEFAULT '0' NOT NULL,
    balance bigint(20) DEFAULT '0',
    loan bigint(20)  DEFAULT '0',
    loantime datetime,
    PRIMARY KEY(ship_id)
);

CREATE TABLE IGB_transfers
(
    transfer_id int NOT NULL auto_increment,
    source_id int DEFAULT '0' NOT NULL,
    dest_id int DEFAULT '0' NOT NULL,
    time datetime,
    PRIMARY KEY(transfer_id)
);

CREATE TABLE teams
(
    id int DEFAULT '0' NOT NULL,
    creator int DEFAULT '0',
    team_name tinytext,
    description tinytext,
    number_of_members tinyint(3) DEFAULT '0' NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE news 
(
    news_id int(11) NOT NULL auto_increment,
    headline varchar(100) NOT NULL,
    newstext text NOT NULL,
    user_id int(11),
    date datetime,
    news_type varchar(10),
    PRIMARY KEY (news_id),
    KEY news_id (news_id),
    UNIQUE news_id_2 (news_id)
);

INSERT INTO news (headline, newstext, date, news_type) VALUES ('Big Bang!','Scientists have just discovered the Universe exists!',NOW(), 'col25');

CREATE TABLE messages 
(
    ID int NOT NULL auto_increment,
    sender_id int NOT NULL default '0',
    recp_id int NOT NULL default '0',
    subject varchar(250) NOT NULL default '',
    sent varchar(19) NULL,
    message longtext NOT NULL,
    notified enum('Y','N') NOT NULL default 'N',
    PRIMARY KEY  (ID) 
);

CREATE TABLE xenobe
(
    xenobe_id char(40) NOT NULL,
    active enum('Y','N') DEFAULT 'Y' NOT NULL,
    aggression smallint(5) DEFAULT '0' NOT NULL,
    orders smallint(5) DEFAULT '0' NOT NULL,
    PRIMARY KEY (xenobe_id),
    KEY xenobe_id (xenobe_id)
);

CREATE TABLE sector_defence
(
    defence_id int unsigned NOT NULL auto_increment,
    ship_id int DEFAULT '0' NOT NULL,
    sector_id int unsigned DEFAULT '0' NOT NULL,
    defence_type enum('M','F') DEFAULT 'M' NOT NULL,
    quantity bigint(20) DEFAULT '0' NOT NULL,
    fm_setting enum('attack','toll') DEFAULT 'toll' NOT NULL,
    PRIMARY KEY (defence_id),
    KEY sector_id (sector_id),
    KEY ship_id (ship_id)
);

CREATE TABLE scheduler
(
    sched_id int unsigned NOT NULL auto_increment,
    repeate enum('Y','N') DEFAULT 'N' NOT NULL,
    ticks_left int unsigned DEFAULT '0' NOT NULL,
    ticks_full int unsigned DEFAULT '0' NOT NULL,
    spawn int unsigned DEFAULT '0' NOT NULL,
    sched_file varchar(30) NOT NULL,
    extra_info varchar(50),
    last_run BIGINT(20),
    PRIMARY KEY (sched_id)
);

CREATE TABLE ip_bans
(
    ban_id int unsigned NOT NULL auto_increment,
    ban_mask varchar(16) NOT NULL,
    PRIMARY KEY (ban_id)
);

CREATE TABLE logs
(
    log_id int unsigned NOT NULL auto_increment,
    ship_id int DEFAULT '0' NOT NULL,
    type mediumint(5) DEFAULT '0' NOT NULL,
    time datetime,
    data varchar(255),
    PRIMARY KEY (log_id),
    KEY idate (ship_id,time)
);

CREATE TABLE bounty 
(
    bounty_id int unsigned NOT NULL auto_increment,
    amount bigint(20) unsigned DEFAULT '0' NOT NULL, 
    bounty_on int unsigned DEFAULT '0' NOT NULL,
    placed_by int unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (bounty_id),
    KEY bounty_on (bounty_on),
    KEY placed_by (placed_by)
);

CREATE TABLE movement_log
(
    event_id int unsigned NOT NULL auto_increment,
    ship_id int DEFAULT '0' NOT NULL,
    sector_id int DEFAULT '0',
    time datetime ,
    PRIMARY KEY (event_id),
    KEY ship_id(ship_id),
    KEY sector_id (sector_id)
);