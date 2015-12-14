<?php

namespace Moccalotto\Net;

class DomainFilter
{
    /**
     * @var string[]
     */
    protected $allowedDomains;

    /**
     * Constructor.
     *
     * A domain pattern is a glob-like string á la this:
     * "complete.example.com"
     * "*.sub.example.com"
     * "*.example.com"
     * "specific.sub.*.com"
     *
     * BE AWARE THAT *.example.com does NOT match foo.bar.example.com
     * to make this work, you must use *.*.example.com or *.bar.example.com, etc.
     * This reflects the behavior of ssl certificate domain matching
     *
     * @param string[] $allowedDomains. Domain whitelist patterns
     */
    public function __construct(array $allowedDomains)
    {
        $this->allowedDomains = $allowedDomains;
    }

    /**
     * Verify that at least one entry in the allowedDomains list accepts a given domain.
     *
     * @param string $domain the domain to be verified.
     *
     * @return bool
     */
    public function matchesOne($domain)
    {
        if (empty($domain)) {
            return false;
        }

        foreach ($this->allowedDomains as $pattern) {
            if ($this->domainMatchesPattern($domain, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify that all entries in the allowedDomains list accepts a given domain.
     *
     * @param string $domain the domain to be verified.
     *
     * @return bool
     */
    public function matchesAll($domain)
    {
        foreach ($this->allowedDomains as $pattern) {
            if (! $this->domainMatchesPattern($domain, $pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify that all entries in the allowedDomains list rejects a given domain.
     *
     * @param string $domain
     *
     * @return bool
     */
    public function matchesNone($domain)
    {
        return ! $this->matchesOne($domain);
    }

    /**
     * Determine if domain matches a pattern.
     *
     * @param string $domain
     * @param string $pattern
     *
     * @return bool
     */
    protected function domainMatchesPattern($domain, $pattern)
    {
        $domain_parts = explode('.', $domain);
        $against_parts = explode('.', $pattern);

        if (count($against_parts) != count($domain_parts)) {
            return false;
        }

        // for each "part" of the domain name, check that the domain matches the filter
        foreach ($against_parts as $index => $filter) {
            // match wildcard
            if ('*' === $filter) {
                continue;
            }

            // match exact value
            if ($domain_parts[$index] === $filter) {
                continue;
            }

            // nothing matches, return false.
            return false;
        }

        // everything matched, return true
        return true;
    }
}
