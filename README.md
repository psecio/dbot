## DBot: A Discord bot in PHP

The `DBot` library provides a long-running bot process written in PHP.

### Installation

```
git clone git@github.com:psecio/dbot.git
composer install
```

Copy the `.env.dist` file to `.env` and configure it with your bot's token.

### Usage

```
php bin/dbot.php
```


### Adding a Dispatch action

When the Discord API sends a new event on the websocket, your bot will receive the message with a `t` value in the JSON. This is the "dispatch type". Handlers for these can be added to the `bin/dbot.php` script like this for a `GUILD_CREATE`:

```php
<?php
$bot = new Bot($_ENV['BOT_TOKEN']);

$bot->addDispatch('GUILD_CREATE', function($json) {
    echo 'guild create!!!!'."\n";

    return 'test';
});

$bot->init();
?>
```
