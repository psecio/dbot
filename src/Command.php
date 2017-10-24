<?php

namespace Psecio\DBot;

abstract class Command
{
    /**
     * Current bot instance
     * @var Psecio\DBot\Bot
     */
    protected $bot;

    /**
     * Curernt loop instance
     * @var \React\EventLoop
     */
    protected $loop;

    /**
     * Init the instance and set the bot and loop instance
     *
     * @param Psecio\DBot\Bot $bot Bot instance
     * @param \React\EventLoop $loop EventLoop instance
     */
    public function __construct(&$bot, &$loop)
    {
        $this->bot = $bot;
        $this->loop = $loop;
    }

    /**
     * Get the current bot instance
     *
     * @return Psecio\DBot\Bot instance
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * Get the current event loop instance
     *
     * @return \React\EventLoop instance
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get the current connection from the Bot
     *
     * @return [\Ratchet\Client\WebSocket
     */
    public function getConnection()
    {
        return $this->getBot()->getConnection();
    }

    /**
     * Abstract method definition for child class actions
     *
     * @var object $json JSON object
     */
    public abstract function execute($json);
}
