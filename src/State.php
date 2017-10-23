<?php

namespace Psecio\DBot;

class State
{
    protected $connection;
    protected $token;
    protected $loop;
    protected $interval = 5;
    protected $ops = [
        0 => 'Dispatch',
        1 => 'Hello',
        2 => 'Identify',
        10 => 'Hello',
        11 => 'Heartbeatack'
    ];

    protected $status = self::STATUS_DISCONNECTED;

    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTED = 'connected';
    const STATUS_AUTHED = 'authorized';

    public function __construct($conn, $token, $loop)
    {
        $this->connection = $conn;
        $this->token = $token;
        $this->loop = $loop;
    }

    public static function log($message)
    {
        echo '['.date('Y-m-d H:i:s').'] '. $message."\n";
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    public function action($json)
    {
        State::log('Incoming: '.print_r($json, true));

        $op = $json->op;
        $loop = $this->getLoop();

        $commandNs = '\\Psecio\\DBot\\Command\\'.$this->ops[$op];
        $command = new $commandNs($this, $loop);
        $command->execute($json);
    }

    public function authorize()
    {
        $loop = $this->getLoop();

        $command = new \Psecio\DBot\Command\Identify($this, $loop);
        $command->execute(null);

        $this->status = self::STATUS_AUTHED;
    }

    public function isAuthed()
    {
        return ($this->status == self::STATUS_AUTHED);
    }
}
