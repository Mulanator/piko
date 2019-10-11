Liest einen Kostal Piko 10.1 Wechselrichter aus.

Für das Auslesen und Senden eines Tagesberichts muss jeweils ein Cronjob angelegt werden

$ sudo su -
# crontab -e

#Senden z.b. jede Minute von 06-21h
* 06-21 * * *  /usr/bin/php /var/www/piko/piko.php > /dev/null 2>&1

#Tagesbericht um 21:05 Uhr täglich
5 21 * * * /usr/bin/php /var/www/piko/mailreport.php > /dev/null 2>&1