<?php

namespace Moccalotto\Net;

use LogicException;
use Moccalotto\Net\Contracts\CidrContract;
use Moccalotto\Net\Contracts\IpContract;

/**
 * Class for filtering IP addresses against CIDRs.
 */
class IpFilter
{
    protected $cidrs = [];

    /**
     * Constructor.
     *
     * @param CidrContract[] $cidrs
     *
     * @throws LogicException if not all elements of $cidrs are objects that implement CidrContract
     */
    public function __construct(array $cidrs)
    {
        $this->cidrs = $cidrs;

        foreach ($cidrs as $cidr) {
            if (! $cidr instanceof CidrContract) {
                throw new LogicException('First argument must be an array of CidrContract');
            }
        }
    }

    /**
     * Check if the ip matches one of the specified cidrs.
     *
     * @param IpContract $ip
     *
     * @return bool TRUE if $ip matches a single cidr in the list
     */
    public function matchesOne(IpContract $ip)
    {
        foreach ($this->cidrs as $cidr) {
            if ($cidr->matches($ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the ip matches all of the specified cidrs.
     *
     * @param IpContract $ip
     *
     * @return bool TRUE if $ip matches all $cidrs in the list
     */
    public function matchesAll(IpContract $ip)
    {
        foreach ($this->cidrs as $cidr) {
            if (! $cidr->matches($ip)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the ip matches none of the specified cidrs.
     *
     * @param IpContract $ip
     *
     * @return $bool TRUE if $ip matches none if the $cidrs in the list
     */
    public function matchesNone(IpContract $ip)
    {
        return ! $this->matchesOne($ip);
    }
}
