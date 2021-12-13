#!/bin/bash
# Set vehicle ID string
veh="car1"

# Retrieve current GPS parameters
gpsdata=$( gpspipe -w -n 10 | grep -m 1 lat )

# Extract from JSON
 lat=$( echo $gpsdata | jq '.lat' )
 lon=$( echo $gpsdata | jq '.lon' )
 spd=$( echo $gpsdata | jq '.speed' )
 trk=$( echo $gpsdata | jq '.track' )
 dtg=$( echo $gpsdata | jq '.time' | tr -d '"' )

# Check if vehicle has moved >10m since last transmission
 # Read last transmitted lat and lon from file
 if [ -f "lastlat" ]; then lastlat=$(cat lastlat); else lastlat=0; fi
 if [ -f "lastlon" ]; then lastlon=$(cat lastlon); else lastlon=0; fi
 # 10m roughly corresponds to 10^-5 degrees of latitude.  Assume same for longitude at mid latitudes.
 # Take differences of lat and long, square both with 10^-9 precision to make positive.
 # XOR outputs 0 if unmoved in lat and long, anything else has moved.
 moved=$(echo "scale=9; latdiff=(($lat-$lastlat)^2); londiff=(($lon-$lastlon)^2); latdiff || londiff " | bc -l)

# Assemble data string
gpsstring=$(echo "-F 'dtg=$dtg' -F 'veh=$veh' -F 'lat=$lat' -F 'lon=$lon' -F 'spd=$spd' -F 'trk=$trk'")

# If vehicle has moved then send the new position to server and update lastlat and lastlon files
 if [ $moved -ne 0 ]
  then
   curl -o sendcurrentposition.log -X POST $gpsstring https://[yourservergoeshere]/update.php
   echo $lat > lastlat
   echo $lon > lastlon
  fi

