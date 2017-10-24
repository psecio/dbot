<?php

namespace Psecio\DBot;

use Psecio\DBot\State;

class Bot
{
    /**
     * Default WSS URL (from the Discord API docs)
     * @var string
     */
    protected $wssUrl = 'wss://gateway.discord.gg/?v=6&encoding=json';

    /**
     * Current bot token
     * @var string
     */
    protected $token;

    /**
     * Current set of dispatch handlers
     * @var [type]
     */
    protected $dispatch = [];

    /**
     * Init the bot and set the token and, optionally, the WSS URL
     *
     * @param string $botToken Current bot token
     * @param string $wssUrl WSS URL [optional]
     */
    public function __construct($botToken, $wssUrl = null)
    {
        if ($wssUrl !== null) {
            $this->wssUrl = $wssUrl;
        }
        $this->token = $botToken;
    }

    /**
     * Add a new dispatch handler
     *
     * @param string $type Dispatch type
     * @param string|Callable $callback Callback to execute when dispatching action
     */
    public function addDispatch($type, $callback)
    {
        $this->dispatch[$type] = $callback;
    }

    /**
     * Init the bot and set up the loop/actions for the WebSocket
     */
    public function init()
    {
        $loop = \React\EventLoop\Factory::create();
        $reactConnector = new \React\Socket\Connector($loop);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);
        $token = $this->token;
        $dispatch = $this->dispatch;

        $connector($this->wssUrl)
        ->then(function(\Ratchet\Client\WebSocket $conn) use ($token, $loop, $dispatch) {
            $state = new State($conn, $token, $loop);
            $state->addDispatch($dispatch);

            $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn, $state, $loop) {
                echo "Received: {$msg}\n";

                $json = json_decode($msg);
                $state->action($json, $loop);
            });

            $conn->on('close', function($code = null, $reason = null) {
                echo "Connection closed ({$code} - {$reason})\n";
                die();
            });

        }, function(\Exception $e) use ($loop) {
            echo "Could not connect: {$e->getMessage()}\n";
            $loop->stop();
        });

        $loop->run();
    }
}
