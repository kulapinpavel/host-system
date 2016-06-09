#!/bin/bash
user_id=$1;
password=$2;
port=$3;
host_ip='*';
servername='hostsystem';
serveradmin='kulapinpavel@yandex.ru';
docroot='/var/www/hostsystem/public_html/web';
user_group='hostsystem';

cat << EOF >> /etc/apache2/sites-available/$user_id.hostsystem.conf
<VirtualHost $host_ip:$port>
        ServerName $servername
        ServerAdmin $serveradmin
        DocumentRoot $docroot

        ErrorLog \${APACHE_LOG_DIR}/error.log
        CustomLog \${APACHE_LOG_DIR}/access.log combined

        <Directory "$docroot">
                AllowOverride All
                Require all granted
        </Directory>

        <IfModule mod_ruid2.c>
                RUidGid $user_id $user_group
        </IfModule>
</VirtualHost>
EOF

a2ensite $user_id.hostsystem.conf

useradd -m -G hostsystem $user_id;
echo $user_id:$password | chpasswd

mkdir /home/$user_id/apache2
mkdir /home/$user_id/apache2/sites-enabled
mkdir /home/$user_id/www

echo "Listen $port" >> /etc/apache2/ports.conf
echo "IncludeOptional /home/$user_id/apache2/sites-enabled/*.conf" >> /etc/apache2/apache2.conf

service apache2 reload
