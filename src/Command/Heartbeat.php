<?php

namespace Psecio\DBot\Command;

class Heartbeat extends \Psecio\DBot\Command
{
    public function execute($json)
    {
        \Psecio\DBot\State::log('Execute: HEARTBEAT');

        $json = json_encode([
            'op' => 1,
            'd' => 1
        ]);

        $this->getConnection()->send($json);
    }
}
