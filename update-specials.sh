#!/bin/bash

wget -O /var/www/html/specials.html http://m58cafe.calvarychatt.com/index.php/featured-specials & sleep 5

sleep 1

count=$(grep -s "erm_product with_image" -c /var/www/html/specials.html)

echo $count

if [ $count -eq 4 ]
then
  sed -i '73i .erm_menu {zoom: 116%;}' /var/www/html/specials.html
fi

