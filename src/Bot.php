<?php

namespace Psecio\DBot;

use Psecio\DBot\State;

class Bot
{
    protected $wssUrl = 'wss://gateway.discord.gg/?v=6&encoding=json';
    protected $token;
    protected $dispatch = [];

    public function __construct($botToken, $wssUrl = null)
    {
        if ($wssUrl !== null) {
            $this->wssUrl = $wssUrl;
        }
        $this->token = $botToken;
    }

    public function addDispatch($type, $callback)
    {
        $this->dispatch[$type] = $callback;
    }

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
