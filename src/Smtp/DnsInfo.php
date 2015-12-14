<?php

namespace Moccalotto\Net\Smtp;

/**
 * Probe DNS info for a domain.
 */
class DnsInfo
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * Constructor.
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Check if the domain has any DNS entries.
     */
    public function exists()
    {
        return checkdnsrr($this->domain, 'ANY');
    }

    /**
     * Extract all MX hosts.
     */
    public function mxHosts()
    {
        if (! $this->exists()) {
            return [];
        }

        $mx_hosts = $mx_weigths = [];

        if (! getmxrr($this->domain, $mx_hosts, $mx_weigths)) {
            return [];
        }

        $res = array_combine($mx_hosts, $mx_weigths);

        asort($res);

        return $res;
    }
}
