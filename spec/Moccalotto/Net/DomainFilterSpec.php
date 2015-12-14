<?php

namespace spec\Moccalotto\Net;

use PhpSpec\ObjectBehavior;

class DomainFilterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType('Moccalotto\Net\DomainFilter');
    }

    public function it_matches_complete_domain_names()
    {
        $domains = [
            'sub.domain-one.com',
            'sub.domain-two.com',
            'some.sub.domain-three.com',
        ];
        $this->beConstructedWith($domains);

        foreach ($domains as $domain) {
            $this->matchesOne($domain)->shouldBe(true);
            $this->matchesNone($domain)->shouldBe(false);

            $this->matchesOne('bob.'.$domain)->shouldBe(false);
            $this->matchesNone('bob'.$domain)->shouldBe(true);

            $this->matchesOne($domain.'.tld')->shouldBe(false);
            $this->matchesNone($domain.'.tld')->shouldBe(true);
        }
    }

    public function it_matches_subdomains_with_wildcards()
    {
        $this->beConstructedWith([
            '*.domain.com',
            '*.another.com',
        ]);

        $this->matchesOne('foo.domain.com')->shouldBe(true);
        $this->matchesOne('foo.another.com')->shouldBe(true);
        $this->matchesOne('foo.athird.com')->shouldBe(false);
    }

    public function it_matches_domains_with_wildcards_anywhere()
    {
        $this->beConstructedWith([
            '*.foo.com',
            'www.*.com',
            'www.foo.*',
        ]);

        $this->matchesAll('www.foo.com')->shouldBe(true);

        $this->matchesNone('www2.bar.net')->shouldBe(true);

        $this->matchesOne('www2.foo.com')->shouldBe(true);
        $this->matchesOne('www.anything.com')->shouldBe(true);
        $this->matchesOne('www.foo.net')->shouldBe(true);
    }
}
