# b3da/Gallery

### Installation

* you need `vipsthumbnail` !, (install by `sudo apt install libvips-tools`)

* clone this repo, run `composer install` in project dir

* set db and directory info in `app/config/parameters.yml` (in case you didn't at the end of composer install) as well as some gallery options - see commented `parameters.yml.dist` for details

* create db by running `bin/console d:d:c`

* create db schema by running `bin/console d:s:c`

* install assets and clear cache `./ass.sh`

* create user `bin/console f:u:create` (pick your username, email and password)

* promote created user to superadmin `bin/console f:u:promote USERNAME --super`

### Setup

* visit `/admin` (if you are not logged in, you will be redirected to /login first...)

* create _Gallery_, upload some _Images_ and check your `web/uploads/gallery/` if everything works ok

* after uploading (and maybe adding titles/descriptions to _Images_), check preview in administration and set __scale__ and __margin__ for _Gallery_

* when _Gallery_ is prepared, don't forget to make it _public_!

### Todo

* drag and drop position changing for _Gallery_ and _Image_ admin listings

* improve styles

* add tests

* add css `picture` with diff srcsets

* tweak image quality of generated _Images_ a bit more 

* make it more lightweight on js/css side

* add _Video_ uploading, previewing and viewing (and maybe processing)

* add another public _Gallery_ templates

* filter visits

* add spherical photo viewer
