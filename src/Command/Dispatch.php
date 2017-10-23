<?php

namespace Psecio\DBot\Command;

class Dispatch extends \Psecio\DBot\Command
{
    public function execute($json)
    {
        \Psecio\DBot\State::log('Execute: DISPATCH');

        $bot = $this->getBot();
        $loop = $this->getLoop();
        $type = $json->t;

        \Psecio\DBot\State::log('Dispatch type: '.$type);

        $this->getLoop()->addPeriodicTimer($bot->getInterval(), function() use ($bot){
            // \Psecio\DBot\State::log('Execute: HELLO');
            $command = new \Psecio\DBot\Command\Heartbeat($bot, $loop);
            $command->execute(null);
        });
    }
}
