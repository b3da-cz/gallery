#!/usr/bin/env bash

chmod -R 0777 var
if [ "$1" = "-f" ]
then
    echo "force updating schema"
    app/console d:s:u --dump-sql --force
fi
app/console cache:clear --env=dev
app/console cache:clear --env=prod
app/console assets:install --symlink
echo '------------------------------------------------------------'
echo '------------------------------------------------------------'
app/console d:s:u --dump-sql
echo '------------------------------------------------------------'
echo 'you are good to go! (or run again with -f for schema update)'
chmod -R 0777 var
