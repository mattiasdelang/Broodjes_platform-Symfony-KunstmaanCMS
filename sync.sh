#!/usr/bin/env bash

# This script will download the production database + uploads folder to your local working copy

project="broodjesplatform"
server="broodjesplatform.nine.staging.kunstmaan.com"
port=22
mysqlrootpw="ieFeequ7"

echo "syncing files from live server"
#rsync -qazhL --progress --omit-dir-times --del --rsh=/usr/bin/ssh -e "ssh -p $port" --exclude "*bak" --exclude "*~" --exclude ".*" $server:/home/projects/$project/data/shared/web/uploads/media/* web/uploads/media/ || exit 1
echo "making database backup on server"
ssh -t -p $port $server "sudo skylab backup $project --quick"
echo "making cache directory"
mkdir -p var/cache
echo "syncing database backup"
scp -P $port "$server:/home/projects/$project/backup/mysql.dmp.gz" var/cache/mysql.dmp.gz
echo "restoring database"
echo "drop database $project" | mysql "$project" -u "$project" -p$mysqlrootpw
echo "create database $project DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci" | mysql -u "$project" -p$mysqlrootpw

if [ "$(uname)" == "Darwin" ]; then
    rm -f "var/cache/mysql.dmp"
    gunzip "var/cache/mysql.dmp.gz"
    cat "var/cache/mysql.dmp" | mysql "$project" -u "$project" -p$mysqlrootpw
elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
    zcat "var/cache/mysql.dmp.gz" | mysql "$project" -u "$project" -p$mysqlrootpw
fi