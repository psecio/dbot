<?php

namespace Psecio\DBot;

class State
{
    /**
     * The current connection instance
     * @var \Ratchet\Client\WebSocket
     */
    protected $connection;

    /**
     * Current bot token
     * @var string
     */
    protected $token;

    /**
     * Loop instance
     * @var \React\EventLoop
     */
    protected $loop;

    /**
     * Default heartbeat interval
     * @var integer
     */
    protected $interval = 5;

    /**
     * Discord API operations to class relationships
     * @var [type]
     */
    protected $ops = [
        0 => 'Dispatch',
        1 => 'Hello',
        2 => 'Identify',
        10 => 'Hello',
        11 => 'Heartbeatack'
    ];

    /**
     * Current dispatch relationships
     * @var array
     */
    protected $dispatch = [];

    /**
     * Current bot status (used in identify)
     * @var string
     */
    protected $status = self::STATUS_DISCONNECTED;

    /**
     * Status constants
     * @var string
     */
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTED = 'connected';
    const STATUS_AUTHED = 'authorized';

    /**
     * Init the State handler and set the connection, token and loop properties
     *
     * @param \Ratchet\Client\WebSocket $conn Connection instance
     * @param string $token Bot token (from API)
     * @param \React\EventLoop $loop  Loop instance
     */
    public function __construct($conn, $token, $loop)
    {
        $this->connection = $conn;
        $this->token = $token;
        $this->loop = $loop;
    }

    /**
     * Logging output method
     *
     * @param string $message Message to output
     */
    public static function log($message)
    {
        echo '['.date('Y-m-d H:i:s').'] '. $message."\n";
    }

    /**
     * Get the current connection
     *
     * @return \Ratchet\Client\WebSocket instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the current token value
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the current event loop
     *
     * @return \React\EventLoop instance
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get the current heartbeat interval
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set the current heartbeat interval
     *
     * @param int $interval Interval in seconds
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * Determine the action (command) to be taken based on the JSON input
     *
     * @param object $json JSON object, parsed from API response
     */
    public function action($json)
    {
        State::log('Incoming: '.print_r($json, true));

        $op = $json->op;
        $loop = $this->getLoop();

        $commandNs = '\\Psecio\\DBot\\Command\\'.$this->ops[$op];
        $command = new $commandNs($this, $loop);
        $command->execute($json);
    }

    /**
     * Authorize the bot and update its state
     */
    public function authorize()
    {
        $loop = $this->getLoop();

        $command = new \Psecio\DBot\Command\Identify($this, $loop);
        $command->execute(null);

        $this->status = self::STATUS_AUTHED;
    }

    /**
     * Check the current state to see if the status is marked as authed (post-identify)
     *
     * @return boolean Authed/not authed status
     */
    public function isAuthed()
    {
        return ($this->status == self::STATUS_AUTHED);
    }

    /**
     * Set the dispatch array value
     *
     * @param array $dispatch Dispatch set
     */
    public function addDispatch(array $dispatch)
    {
        $this->dispatch = $dispatch;
    }

    /**
     * Dispatch the action (command) based on the type
     *
     * @param string $type Type of action
     * @param object $json JSON object
     * @return mixed Result from call of dispatch handler
     */
    public function dispatch($type, $json)
    {
        if (!array_key_exists($type, $this->dispatch)) {
            return null;
        }
        $dis = $this->dispatch[$type];

        if ($dis instanceof \Closure) {
            return $dis($json);
        } elseif (is_callable($dis)) {
            $obj = $dis[0];
            return $obj->$dis[1]($json);
        }
    }
}
