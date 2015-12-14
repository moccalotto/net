<?php

namespace Moccalotto\Net\Contracts;

use Moccalotto\Net\Ip;

interface TorDetectorContract
{
    /**
     * Detect if an given IP is registered as a tor exit point.
     *
     * @param Ip $ip The IP to check
     *
     * @return bool
     */
    public function isTorClient(Ip $ip);
}
