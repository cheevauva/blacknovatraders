<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>PORTS</B><BR><BR>";
  echo "Adding ore to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type='ore' AND port_ore<$ore_limit"));
  echo "Adding ore to all ore ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit"));
  echo "Ensuring minimum ore levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=0 WHERE port_ore<0"));
  echo "<BR>";
  echo "Adding organics to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type='organics' AND port_organics<$organics_limit"));
  echo "Adding organics to all organics ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit"));
  echo "Ensuring minimum organics levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=0 WHERE port_organics<0"));
  echo "<BR>";
  echo "Adding goods to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type='goods' AND port_goods<$goods_limit"));
  echo "Adding goods to all goods ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit"));
  echo "Ensuring minimum goods levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=0 WHERE port_goods<0"));
  echo "<BR>";
  echo "Adding energy to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type='energy' AND port_energy<$energy_limit"));
  echo "Adding energy to all energy ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit"));
  echo "Ensuring minimum energy levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=0 WHERE port_energy<0"));
  echo "<BR>";

?>