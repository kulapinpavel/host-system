#!/bin/bash
#Скрипт получает список пользователей

cat /etc/passwd | awk '/bash/{print}'
