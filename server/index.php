<!DOCTYPE html>
<html>

<head>
  <title>Vehicle Tracker</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>

  <meta http-equiv="refresh" content="60">
</head>

<style>
body {
    padding: 0;
    margin: 0;
}
html, body, #map {
    height: 100%;
    width: 100vw;
}
</style>

<body>

<?php

// Load config file
$config=file_get_contents("config/config.json");

// Load and sanitise vehicle ID [1-20 lower-case letters]
$veh="";
if ( preg_match("/[a-z]{1,20}$/", $_GET["veh"]) ) { $veh=$_GET["veh"]; }

// Read last line of logfile
$jsonvars = `tail -n 1 logs/$veh`;

// Extract variables from JSON.
$vars = json_decode( $jsonvars , true, 2 );
$dtg=$vars["dtg"];
$lat=$vars["lat"];
$lon=$vars["lon"];
$spd=$vars["spd"];
$trk=$vars["trk"];

// Convert speed from m/s to mph
$spdmph=$spd*(2.24);

// Cast speed and track to integers
$spdmph=(int)$spdmph;
$trkint=(int)$trk;

// Get file for compass rose (nearest 5 degrees)
// Display plain circle if stopped
if($spdmph > 0) {
  $compassfile="/img/compass/".(round($trk/5)*5).".png";
}
else {
  $compassfile="/img/compass/null.png";
}

// Translate veh into long names
$vehDB = json_decode($config, true);
for ($i=0; $i < 2; $i++) {
  if($vehDB["vehicles"][$i]["veh"] == $veh) {
  $vehLong=$vehDB["vehicles"][$i]["long"];
  }
}

// Time since last update
$dateUpdate=strtotime($dtg); //UNIX format in seconds
$dateNow=strtotime("now");   //UNIX format in seconds
$updatedAgoDays=(int)(($dateNow-$dateUpdate)/86400);
$updatedAgoHours=date("H",$dateNow-$dateUpdate);
$updatedAgoMinutes=date("i",$dateNow-$dateUpdate);
 if ($updatedAgoDays != 0) {
  $updatedAgoString=$updatedAgoDays." days ago";
 }
 elseif ($updatedAgoHours != 0) {
  $updatedAgoString=$updatedAgoHours." hours ago";
 }
 else {
  $updatedAgoString=$updatedAgoMinutes." minutes ago";
 }

// Get vehicle icon
$vehicon="/img/veh/".$veh.".png";

?>

 <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

 <div id ="map"></div>

 <script>
 // Define map box centred on vehicle location
 var map = L.map('map').setView([<?php echo "$lat" ?>, <?php echo "$lon" ?>], 13);

 var vehIcon = L.icon({
  iconUrl: '<?php echo $vehicon; ?>',
  iconSize: [100,66]
 });

 // Create compass rose under vehicle icon
 var compassIcon = L.icon({
  iconUrl: '<?php echo $compassfile; ?>',
  iconSize: [150,150]
 });

 L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: '[youraccesstokengoeshere]'
 }).addTo(map);

 // Add compass rose to map if speed > 0
 L.marker([<?php echo "$lat" ?>, <?php echo "$lon" ?>], {icon: compassIcon}).addTo(map)
      .openPopup();

 // Add vehicle icon to map
 L.marker([<?php echo "$lat" ?>, <?php echo "$lon" ?>], {icon: vehIcon}).addTo(map)
    .openPopup();

 // Infobox at bottom right of map
 var infobox = L.control({
    position: 'bottomright'
 });
 infobox.onAdd = function (e) {
    this._div = L.DomUtil.create('div', 'info');
    this.refresh();
    return this._div;
 };
 infobox.refresh = function (properties) {
    this._div.innerHTML = "<table bgcolor=white><tr><td></td><td><b><?php echo $vehLong; ?></b></td></tr><tr><td>Updated:</td><td><?php echo $updatedAgoString; ?></td></tr><tr><td>Latitude:</td><td><?php echo $lat; ?></td></tr><tr><td>Longitude:</td><td><?php echo $lon; ?></td></tr><tr><td>Track:</td><td><?php echo $trkint."T"; ?></td></tr><tr><td>Speed:</td><td><?php echo $spdmph."mph"; ?></td></tr></table>";
 };
 infobox.addTo(map);

 </script>

</body>
</html>
