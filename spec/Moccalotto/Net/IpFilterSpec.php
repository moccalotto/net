<?php

namespace spec\Moccalotto\Net;

use PhpSpec\ObjectBehavior;
use Moccalotto\Net\Ip;
use Moccalotto\Net\Cidr;

class IpFilterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('127.0.0.0'), 16),
        ]);
        $this->shouldHaveType('Moccalotto\Net\IpFilter');
    }

    public function it_throws_exception_if_constructed_with_bad_params()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('10.0.0.0'), 16),
            'fail',
            new Cidr(new Ip('192.168.0.0'), 24),
        ]);
        $this->shouldThrow('\LogicException')->duringInstantiation();
    }

    public function it_matches_one_v4()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('10.0.0.0'), 16),
            new Cidr(new Ip('127.0.0.1'), 32),
            new Cidr(new Ip('192.168.0.0'), 24),
        ]);

        $this->matchesOne(new Ip('127.0.0.1'))->shouldBe(true);
        $this->matchesOne(new Ip('10.0.1.1'))->shouldBe(true);
        $this->matchesOne(new Ip('192.168.0.1'))->shouldBe(true);
    }

    public function it_matches_all_v4()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('127.0.0.1'), 16),
            new Cidr(new Ip('127.0.0.0'), 31),
            new Cidr(new Ip('127.0.0.0'), 16),
        ]);

        $this->matchesAll(new Ip('127.0.0.1'))->shouldBe(true);
        $this->matchesAll(new Ip('127.0.0.2'))->shouldBe(false);
        $this->matchesAll(new Ip('127.0.1.1'))->shouldBe(false);
    }

    public function it_matches_one_v6()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('8000::1'), 128),
            new Cidr(new Ip('::0'), 127),
            new Cidr(new Ip('::2'), 127),
        ]);

        $this->matchesOne(new Ip('8000::1'))->shouldBe(true);
        $this->matchesOne(new Ip('::1'))->shouldBe(true);
        $this->matchesOne(new Ip('::3'))->shouldBe(true);
        $this->matchesOne(new Ip('::4'))->shouldBe(false);
        $this->matchesOne(new Ip('8000::2'))->shouldBe(false);
    }

    public function it_matches_all_v6()
    {
        $this->beConstructedWith([
            new Cidr(new Ip('8000::1'), 128),
            new Cidr(new Ip('8000::0'), 127),
            new Cidr(new Ip('8000::3'), 126),
        ]);

        $this->matchesAll(new Ip('8000::1'))->shouldBe(true);
        $this->matchesAll(new Ip('8000::2'))->shouldBe(false);
        $this->matchesAll(new Ip('8000::0'))->shouldBe(false);
        $this->matchesAll(new Ip('8000::3'))->shouldBe(false);
    }
}
