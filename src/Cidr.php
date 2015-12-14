<?php

namespace Moccalotto\Net;

use Moccalotto\Net\Contracts\CidrContract;
use Moccalotto\Net\Contracts\IpContract;

class Cidr implements CidrContract
{
    /**
     * @var IpContract
     */
    protected $net;

    /**
     * @var int
     */
    protected $maskBits;

    /**
     * Constructor.
     *
     * @param IpContract $net
     * @param int $maskBits
     */
    public function __construct(IpContract $net, $maskBits)
    {
        $this->net = $net;
        $this->maskBits = (int) $maskBits;
    }

    /**
     * Get the cidr network.
     *
     * @return IpContract
     */
    public function net()
    {
        return $this->net;
    }

    /**
     * Get the number of bits in the match mask.
     *
     * @return int
     */
    public function maskBits()
    {
        return $this->maskBits;
    }

    /**
     * Check if this cidr matches a given ip.
     *
     * @param IpContract $ip
     *
     * @return bool
     */
    public function matches(IpContract $ip)
    {
        if ($this->net->version() !== $ip->version()) {
            return false;
        }

        if (0 === $this->maskBits) {
            return true;
        }

        $masked_ip = substr($ip->base2(), 0, $this->maskBits);
        $masked_net = substr($this->net->base2(), 0, $this->maskBits);

        return $masked_ip === $masked_net;
    }
}
