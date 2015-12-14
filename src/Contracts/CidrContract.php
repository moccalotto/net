<?php

namespace Moccalotto\Net\Contracts;

interface CidrContract
{
    /**
     * @return IpContract
     */
    public function net();

    /**
     * @return int
     */
    public function maskBits();

    /**
     * Check if this CIDR allows a given ip.
     *
     * @param IpContract $ip
     *
     * @return bool
     */
    public function matches(IpContract $ip);
}
