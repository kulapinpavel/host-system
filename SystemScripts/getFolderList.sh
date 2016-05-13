#!/bin/bash
#Скрипт получает текущий каталог

ls -alFhG --time-style long-iso $1 | tail -n +2
