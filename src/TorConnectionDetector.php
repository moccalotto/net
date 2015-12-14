<?php

namespace Moccalotto\Net;

use Moccalotto\Net\Contracts\TorDetectorContract;

/**
 * Class for detecting TOR users.
 *
 * It looks up the server port, server address and client address in a DNSBL
 *
 * It is more precise that the TorServerDetector in that it avoids false positives.
 *
 * @see TorServerDetector
 *
 * @link https://www.torproject.org/projects/tordnsel.html.en
 */
class TorConnectionDetector implements TorDetectorContract
{
    protected $serverIp;
    protected $serverPort;

    /**
     * Constructor.
     *
     * @param Ip $serverIp
     * @param int $serverPort
     */
    public function __construct(Ip $serverIp, $serverPort)
    {
        $this->serverIp = $serverIp;
        $this->serverPort = $serverPort;
    }

    public function isTorClient(Ip $ip)
    {
        $query = sprintf(
            '%s.%d.%s.ip-port.exitlist.torproject.org',
            $ip->reversedOctetString(),
            $this->serverPort,
            $serverIp->reversedOctetString()
        );

        return gethostbyname($query) === '127.0.0.2';
    }
}
