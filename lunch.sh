#!/bin/bash

echo "Turning off Power Save"

xset s off -display :0
xset -dpms -display :0
xset s noblank -display :0

killall weekday-breakfast.sh
killall weekend-breakfast.sh

while :
do
	SOCKET=/tmp/uzbl_socket_`ps -aux | pgrep uzbl-core`; echo uri localhost/lunch.html | socat - unix-connect:$SOCKET
	sleep 14

	echo uri http://localhost/specials.html | socat - unix-connect:$SOCKET
	sleep 14

done
