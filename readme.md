## A simple PuSH server

A very simple [PuSH](https://github.com/pubsubhubbub/PubSubHubbub) server,
mainly (IndieWeb)[https://indiewebcamp.com] oriented.

### Requirements

This app is based on [Lumen](http://lumen.laravel.com) and therefore has the
same requirements as Lumen. Namely:

- PHP >= 5.4
- Mcrypt extension
- OpenSSL extension
- Mbstring extension
- Tokenizer extension

Lumen also needs write access to the `storage/` folder.

### Installation

Firstly clone the git repo. Then copy `.env.example` to `.env` and edit as
necessary. At least the `DB_*` values will need to be set, as well as
`APP_TIMEZONE`. A sensible default value for the hub’s lease time has been set,
but you can also edit this if you wish.

Next run

    composer install --no-dev

followed by

    php artisan migrate

to install the dependencies and set up the database tables.

The server also runs a job daily to check if any subscription’s leases have
expired, and if so, remove the subscription. Lumen makes this quite easy. One
cron job needs to be added to your server. Just added

    * * * * * php /path/to/push/artisan schedule:run 1>> /dev/null 2>&1

Now you should be good to go.
