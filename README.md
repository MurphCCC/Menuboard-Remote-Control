This is a fork of the raspberry-remote project.  I have made some tweaks to make this better suited for our needs.  Proper documentation is on the way.  For now just clone this repository and check that the scripts.xml is correct and that the scripts it is pointing to are correct.

# raspberry-remote

**Screenshots**<br />
> **Windows** *Chrome*<br />

![1](https://cloud.githubusercontent.com/assets/7598609/14709828/5f9c97c4-07db-11e6-8756-69604249097d.PNG)
![2](https://cloud.githubusercontent.com/assets/7598609/14709829/5fa31ea0-07db-11e6-978e-4b8dcd69c50f.PNG)
![3](https://cloud.githubusercontent.com/assets/7598609/14709830/5fa4e5dc-07db-11e6-8623-af6afb8ed432.PNG)

*Remote Control for Raspberry Pi (WebUI)*

This project **requires** **Apache server**, **PHP 5+** and **omxplayer**.

Working and tested with **raspbian** *jessie* raw image *(server side)*, 

**Browsers:** All up-to-date browsers. Not tested with **Edge** *(soon or later ...)*

**Setting up apache server and php,**

> https://www.raspberrypi.org/documentation/remote-access/web-server/apache.md

**Clone** running *(jessie)*,

> cd /var/www/html

> sudo -u www-data git clone https://github.com/dennmtr/raspberry-remote

*(www-data, default apache user)*

You may need to change **folder permissions** after cloning if you are using plain sudo *(as root)* *(error report 4)*,

> sudo chown -R www-data:www-data raspberry-remote

You can rename path,

> sudo mv raspberry-remote remote

You can access,

> your-local-ip-address/remote

