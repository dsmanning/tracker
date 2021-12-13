<?php

// Get and sanitise parameters
	// Date-time-group is in the format YYYY-MM-DDTHH:MM:SS.SSSZ
if ( preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3}[A-Z]$/", $_POST["dtg"]) ) { $dtg=$_POST["dtg"]; }
	// Vehicle ID is 1-20 lower-case letters
if ( preg_match("/[a-z]{1,20}$/", $_POST["veh"]) ) { $veh=$_POST["veh"]; }
	// Latitude is a number
if ( preg_match("/[0-9]{1,2}\.[0-9]{1,9}$/", $_POST["lat"]) ) { $lat=$_POST["lat"]; }
	// Longitude is a number
if ( preg_match("/[0-9]{1,3}\.[0-9]{1,9}$/", $_POST["lon"]) ) { $lon=$_POST["lon"]; }
	// Speed is a number
if ( preg_match("/[0-9]{1,3}\.[0-9]{1,3}$/", $_POST["spd"]) ) { $spd=$_POST["spd"]; }
	// Track is a number
if ( preg_match("/[0-9]{1,2}\.[0-9]{1,4}$/", $_POST["spd"]) ) { $trk=$_POST["trk"]; }

// Assemble array of vars
$vars = array("dtg" => $dtg, "veh" => $veh, "lat" => (float)$lat, "lon" => (float)$lon, "spd" => (float)$spd, "trk" => (float)$trk);

// Open file for vehicle in append mode
$file = fopen("logs/$veh", "a") or die("Error opening vehicle log $veh for appending");

print json_encode($vars);

// Write JSON to file
fwrite( $file, json_encode($vars) . PHP_EOL );

?>
