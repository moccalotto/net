<?php

namespace spec\Moccalotto\Net;

use PhpSpec\ObjectBehavior;
use Moccalotto\Net\Ip;

class CidrSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(new Ip('127.0.0.0'), 16);
        $this->shouldHaveType('Moccalotto\Net\Cidr');
    }

    public function it_implements_correct_interface()
    {
        $this->beConstructedWith(new Ip('127.0.0.0'), 16);
        $this->shouldImplement('Moccalotto\Net\Contracts\CidrContract');
    }

    public function it_can_match_v4_with_zero_mask()
    {
        $this->beConstructedWith(new Ip('127.0.0.0'), 0);
        $this->matches(new Ip('123.123.123.123'))->shouldBe(true);
    }

    public function it_can_match_v6_with_zero_mask()
    {
        $this->beConstructedWith(new Ip('ffff::ffff'), 0);
        $this->matches(new Ip('::1'))->shouldBe(true);
    }

    public function it_will_not_match_v4_against_v6()
    {
        $this->beConstructedWith(new Ip('::1'), 0);
        $this->matches(new Ip('127.0.0.1'))->shouldBe(false);
    }

    public function it_will_not_match_v6_against_v4()
    {
        $this->beConstructedWith(new Ip('127.0.0.1'), 0);
        $this->matches(new Ip('::1'))->shouldBe(false);
    }
}
