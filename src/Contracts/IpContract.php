<?php

namespace Moccalotto\Net\Contracts;

interface IpContract
{
    /**
     * Get human readable address.
     *
     * @return string
     */
    public function address();

    /**
     * Get the binary representation of the address.
     *
     * @return string
     */
    public function bin();

    /**
     * Get IP address as base2 encoded number.
     *
     * @return string
     */
    public function base2();

    /**
     * Get the IP protocol version.
     *
     * @return int
     */
    public function version();

    /**
     * Get number of bits in the IP address.
     *
     * @return int
     */
    public function bitCount();
}
