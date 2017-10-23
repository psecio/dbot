<?php

namespace Psecio\DBot\Command;

class Heartbeatack extends \Psecio\DBot\Command
{
    public function execute($json)
    {
        \Psecio\DBot\State::log('Execute: HEARTBEAT-ACK');
        // Nothing to see...
    }
}
