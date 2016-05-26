#!/bin/bash

echo "Turning off Power Save"

xset s off -display :0
xset -dpms -display :0
xset s noblank -display :0

killall lunchmenu

while :
do
	SOCKET=/tmp/uzbl_socket_`ps -aux | pgrep uzbl-core`; echo uri localhost/weekend-breakfast.html | socat - unix-connect:$SOCKET
	sleep 2700

	echo uri http://localhost/calvarychatt.html | socat - unix-connect: `echo $SOCKET`
	sleep 5

done
