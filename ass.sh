#!/usr/bin/env bash

chmod -R 0777 var
if [ "$1" = "-f" ]
then
    echo "force updating schema"
    bin/console d:s:u --dump-sql --force
fi
bin/console cache:clear --env=dev
bin/console cache:clear --env=prod
bin/console assets:install --symlink
echo '------------------------------------------------------------'
echo '------------------------------------------------------------'
bin/console d:s:u --dump-sql
echo '------------------------------------------------------------'
echo 'you are good to go! (or run again with -f for schema update)'
chmod -R 0777 var
