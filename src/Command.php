<?php

namespace Psecio\DBot;

abstract class Command
{
    protected $bot;
    protected $loop;

    public function __construct(&$bot, &$loop)
    {
        $this->bot = $bot;
        $this->loop = $loop;
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function getConnection()
    {
        return $this->getBot()->getConnection();
    }

    public abstract function execute($json);
}
