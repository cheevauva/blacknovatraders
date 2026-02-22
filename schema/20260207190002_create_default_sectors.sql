INSERT INTO universe (sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) 
VALUES ('Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0');
UPDATE universe SET sector_id = 0 WHERE sector_id = 1;
ALTER TABLE universe AUTO_INCREMENT = 1;
INSERT INTO universe (sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) 
VALUES ('Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1');
