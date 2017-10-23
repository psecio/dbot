<?php

namespace Psecio\DBot\Command;

class Hello extends \Psecio\DBot\Command
{
    public function execute($json)
    {
        \Psecio\DBot\State::log('Execute: HELLO');

        $interval = ((int)$json->d->heartbeat_interval / 1000) - 2;

        $bot = $this->getBot();
        $bot->setInterval($interval);
        $bot->authorize();
    }
}
