<?php

namespace Moccalotto\Net;

use Moccalotto\Net\Contracts\TorDetectorContract;

/**
 * Class for detecting TOR users.
 *
 * It looks up the client IP in a public DNSBL
 *
 * @link https://www.dan.me.uk/dnsbl
 */
class TorServerDetector implements TorDetectorContract
{
    public function isTorClient(Ip $ip)
    {
        $query = sprintf('%s.torexit.dan.me.uk', $this->reversedOctetString($ip));

        return gethostbyname($query) === '127.0.0.100';
    }
}
