<?php

namespace spec\Moccalotto\Net;

use PhpSpec\ObjectBehavior;

class TorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Net\Tor');
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement('Moccalotto\Net\Contracts\TorDetectorContract');
    }
}
