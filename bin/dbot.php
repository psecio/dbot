<?php

require_once __DIR__.'/../vendor/autoload.php';

use Psecio\DBot\Bot;

$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

$bot = new Bot($_ENV['BOT_TOKEN']);
$bot->init();
