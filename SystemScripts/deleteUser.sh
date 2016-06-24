#!/bin/bash
user_id=$1;
port=$2;
delete_homedir=$3;

userdel $user_id

sed -i "/Listen $port/d" /etc/apache2/ports.conf
sed -i "/IncludeOptional \/home\/$user_id\/apache2\/sites-enabled\/\*\.conf/d" /etc/apache2/apache2.conf

a2dissite $user_id.hostsystem.conf

rm /etc/apache2/sites-available/$user_id.hostsystem.conf

if $delete_homedir
then
        rm -rf /home/$user_id
else
        echo 'not deleting homedir'
fi

service apache2 reload