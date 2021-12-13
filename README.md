# tracker
A simple GPS tracker that displays the position of your vehicle on a web-accessible map.

Instructions - Car
------------------
Install a computer in your car with GPS and an Internet connection.  
I use a Raspberry Pi 3 with a USB GPS dongle (the ublox series 7 is cheap and effective, plenty on eBay) and a USB 3G modem.
Install **gpsd** (for the utility gpspipe), **bc** (for calculations in bash script) and **jq** (for parsing javascript).
Install the file **sendcurrentposition.sh** in the **car** folder onto your car's computer and set it to run every few minutes on a cronjob.
Edit the first line of the file to create a short vehicle ID ($veh) associated with your vehicle.
Edit the last line of the file to point to your own server.
This file sends the car's ID, current position, speed and track to the server.

Instructions - Server
---------------------
Put all of the files and folders in the **server** folder on your webserver, retaining the folder structure.  Set the details of your vehicles in config/config.json.
NOTE: the default design relies on "security through obscurity" - it is good practice to protect access to these files and folders on the server.
At the very least, use https instead of http to encrypt the details of the request made to the server.  The car ID is a weak form of password.

How it works - high level
-------------------------
The car computer sends a JSON string using HTTP POST to the server's update.php.  This sanitizes the inputs and appends them to a logfile corresponding to the car ID.
To view the position, go to `https://[yourserver]/index.php?veh=[yourcarid]`
index.php gets the last line of the logfile, extracts its parameters and displays the position and other data on an OSM base layer using LeafletJS.



