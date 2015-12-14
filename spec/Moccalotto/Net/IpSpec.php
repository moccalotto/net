<?php

namespace spec\Moccalotto\Net;

use PhpSpec\ObjectBehavior;

class IpSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->shouldHaveType('Moccalotto\Net\Ip');
    }

    public function it_implements_correct_interface()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->shouldImplement('Moccalotto\Net\Contracts\IpContract');
    }

    public function it_parses_ipv4()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->shouldHaveType('Moccalotto\Net\Ip');
    }

    public function it_parses_ipv6()
    {
        $this->beConstructedWith('::1');
        $this->shouldHaveType('Moccalotto\Net\Ip');
    }

    public function it_throws_exceptions_when_parsing_malformed_ips()
    {
        $this->beConstructedWith('127-0-0-1');
        $this->shouldThrow('LogicException')->duringInstantiation();
        $this->shouldHaveType('Moccalotto\Net\Ip');
    }

    public function it_converts_v4_to_base2()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->base2()->shouldBe('01111111000000000000000000000001');
    }

    public function it_converts_v6_to_base2()
    {
        $this->beConstructedWith('8000::1');
        $this->base2()->shouldBe('10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001');
    }

    public function it_converts_v4_to_bin()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->bin()->shouldBe(inet_pton('127.0.0.1'));
    }

    public function it_converts_v6_to_bin()
    {
        $this->beConstructedWith('8000::1');
        $this->bin()->shouldBe(inet_pton('8000::1'));
    }
}
