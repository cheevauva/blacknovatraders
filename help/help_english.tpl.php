<?php $title = "Help"; ?>
<?php include __DIR__ . '/../header.php'; ?>
<?php bigtitle(); ?>


Greetings and welcome to BlackNova Traders!
<BR><BR>
This is a game of inter-galactic exploration. Players explore the universe, trading for commodities and 
increasing their wealth and power. Battles can be fought over space sectors and planets.
<BR><BR>
<A HREF=#mainmenu>Main Menu commands</A><BR>
<A HREF=#techlevels>Tech levels</A><BR>
<A HREF=#devices>Devices</A><BR>
<A HREF=#zones>Zones</A><BR>
<A NAME=mainmenu></A><H2>Main Menu commands:</H2>
<B>Ship report:</B><BR>
Display a detailed report on your ship's systems, cargo and weaponry. You can display this report by 
clicking on your ship's name at the top of the main page.
<BR><BR>
<B>Warp links:</B><BR>
Move from one sector to another through warp links, by clicking on the sector numbers.
<BR><BR>
<B>Long-range scan:</B><BR>
Scan a neighboring sector with your long range scanners without actually moving there.
<?php if ($allow_fullscan): ?>
    A full scan will give you an outlook on all the neighboring sectors in one wide sweep of your 
    sensors.
<?php endif; ?>
<BR><BR>
<B>Ships:</B><BR>
Scan or attack a ship (if it shows up on your sensors) by clicking on the appropriate link on the right 
of the ship's name. The attacked ship may evade your offensive maneuver depending on its tech levels.
<BR><BR>
<B>Trading ports:</B><BR>
Access the port trading menu by clicking on a port's type when you enter a sector where one is present.
<BR><BR>
<B>Planets:</B><BR>
Access the planet menu by clicking on a planet's name when you enter a sector where one is present.
<BR><BR>
<?php if ($allow_navcomp): ?>
    <B>Navigation computer:</B><BR>
    Use your computer to find a route to a specific sector. The navigation computer's power depends on 
    your computer tech level.
    <BR><BR>
<?php endif; ?>
<B>RealSpace:</B><BR>
Use your ship's engines to get to a specific sector. Upgrade your engines' tech level to use RealSpace 
moves effectively. By clicking on the 'Presets' link you can memorize up to 3 sector numbers for quick 
movement or you can target any sector using the 'Other' link. 
<BR><BR>
<B>Trade routes:</B><BR>
Use trade routes to quickly trade commodities between ports. Trade routes take advantage of RealSpace 
movements to go back and forth between two ports and trade the maximum amount of commodities at each 
end. Ensure the remote sector contains a trading port before using a trade route. The trade route 
presets are shared with the RealSpace ones. As with RealSpace moves, any sector can be targeted using 
the 'Other' link
<BR><BR>
<H3>Menu bar (bottom part of the main page):</H3>
<B>Devices:</B><BR>
Use the different devices that your ship carries (Genesis Torpedoes, beacons, Warp Editors, etc.). For 
more details on each individual device, scroll down to the 'Devices' section.
<BR><BR>
<B>Planets:</B><BR>
Display a list of all your planets, with current totals on commodities, weaponry and credits.
<BR><BR>
<B>Log:</B><BR>
Display the log of events that have happened to your ship.
<BR><BR>
<B>Send Message:</B><BR>
Send an e-mail to another player.
<BR><BR>
<B>Rankings:</B><BR>
Display the list of the top players, ranked by their current scores.
<BR><BR>
<B>Last Users:</B><BR>
Display the list of users who recently logged on to the game.
<BR><BR>
<B>Options:</B><BR>
Change user-specific options (currently, only the password can be changed).
<BR><BR>
<B>Feedback:</B><BR>
Send an e-mail to the game admin.
<BR><BR>
<B>Self-Destruct:</B><BR>
Destroy your ship and remove yourself from the game.
<BR><BR>
<B>Help:</B><BR>
Display the help page (what you're reading right now).
<BR><BR>
<B>Logout:</B><BR>
Remove any game cookies from your system, ending your current session.
<BR><BR>
<A NAME=techlevels></A><H2>Tech levels:</H2>
You can upgrade your ship components at any special port. Each component upgrade improves your ship's 
attributes and capabilities.
<BR><BR>
<B>Hull:</B><BR>
Determines the number of holds available on your ship (for transporting commodities and 
colonists).
<BR><BR>
<B>Engines:</B><BR>
Determines the size of your engines. Larger engines can move through RealSpace at a faster pace.
<BR><BR>
<B>Power:</B><BR>
Determines the number of energy your ship can carry.
<BR><BR>
<B>Computer:</B><BR>
Determines the number of fighters your ship can control.
<BR><BR>
<B>Sensors:</B><BR>
Determines the precision of your sensors when scanning a ship or planet. Scan success is dependent upon 
the target's cloak level.
<BR><BR>
<B>Armour:</B><BR>
Determines the number of armor points your ship can use.
<BR><BR>
<B>Shields:</B><BR>
Determines the efficiency of your ship's shield system during combat.
<BR><BR>
<B>Beams:</B><BR>
Determines the efficiency of your ship's beam weapons during combat.
<BR><BR>
<B>Torpedo launchers:</B><BR>
Determines the number of torpedoes your ship can use.
<BR><BR>
<B>Cloak:</B><BR>
Determines the efficiency of your ship's cloaking system. See 'Sensors' for more details.
<BR><BR>
<A NAME=devices></A><H2>Devices:</H2>
<B>Space Beacons:</B><BR>
Post a warning or message which will be displayed to anyone entering this sector. Only 1 beacon can be 
active in each sector, so a new beacon removes the existing one (if any).
<BR><BR>
<B>Warp Editors:</B><BR>
Create or destroy warp links to another sector.
<BR><BR>
<B>Genesis Torpedoes:</B><BR>
Create a planet in the current sector (if one does not yet exist).
<BR><BR>
<B>Mine Deflector:</B><BR>
Protect the player against mines dropped in space. Each deflector takes out 1 mine.
<BR><BR>
<B>Emergency Warp Device:</B><BR>
Transport your ship to a random sector, if manually engaged. Otherwise, an Emergency Warp Device can 
protect your ship when attacked by transporting you out of the reach of the attacker.
<BR><BR>
<B>Escape Pod (maximum of 1):</B><BR>
Keep yourself alive when your ship is destroyed, enabling you to keep your credits and planets.
<BR><BR>
<B>Fuel Scoop (maximum of 1):</B><BR>
Accumulate energy units when using RealSpace movement.
<BR><BR>
<A NAME=zones></A><H2>Zones:</H2>
The galaxy is divided into different areas with different rules being enforced in each zone. To display 
the restrictions attached to your current sector, just click on the zone name (top right corner of the 
main page). Your ship can be towed out of a zone to a random sector when your hull size exceeds the 
maximum allowed level for that specific zone. Attacking other players and using some devices can also 
be disallowed in some zones.
<BR><BR>

<?php include __DIR__ . '/../footer.php'; ?>

