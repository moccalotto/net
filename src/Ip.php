<?php

namespace Moccalotto\Net;

use Moccalotto\Net\Contracts\IpContract;
use DomainException;
use LogicException;

class Ip implements IpContract
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $bin;

    /**
     * Constructor.
     *
     * @param string $ip
     *
     * @throws DomainException if $ip could not be parsed
     */
    public function __construct($ip)
    {
        $this->ip = $ip;
        $this->bin = @inet_pton($ip);

        if (false === $this->bin) {
            throw new DomainException(sprintf(
                'Could not parse IP: "%s"',
                $ip
            ));
        }
    }

    /**
     * Get human readable address.
     *
     * @return string
     */
    public function address()
    {
        return $this->ip;
    }

    /**
     * Get the binary representation of the address.
     *
     * @return string
     */
    public function bin()
    {
        return $this->bin;
    }

    /**
     * Get IP address as base2 encoded number.
     *
     * @return string
     */
    public function base2()
    {
        $bindata = $this->bin();

        $pattern = sprintf('a%d', strlen($bindata));

        $unpacked = str_split(unpack($pattern, $bindata)[1]);

        return array_reduce($unpacked, function ($carry, $char) {
            return $carry.str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }, '');
    }

    /**
     * Get the IP protocol version.
     *
     * @return int
     */
    public function version()
    {
        return 32 === $this->bitCount() ? 4 : 6;
    }

    /**
     * Get number of bits in the IP address.
     *
     * @return int
     */
    public function bitCount()
    {
        return strlen($this->bin) * 8;
    }

    /**
     * Reverse the octets of an IPv4 address.
     *
     * @return string
     *
     * @throws LogicException if the IP version is different from 4.
     */
    protected function reversedOctetString()
    {
        if (4 !== $this->version()) {
            throw new LogicException(sprintf(
                'The ip address %s is version %d, but only IP version 4 is supported',
                $this->ip,
                $this->version()
            ));
        }

        $octets = explode('.', $this->ip);

        return vsprintf(
            '%d.%d.%d.%d',
            array_reverse($octets)
        );
    }
}
