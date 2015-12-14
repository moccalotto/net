<?php

namespace Moccalotto\Net\Smtp;

use Moccalotto\Net\Smtp\Exceptions\SocketException;
use Moccalotto\Net\Smtp\Exceptions\ConnectionException;

/**
 * Socket wrapper class.
 */
class Socket
{
    protected $socket;

    protected function ensureOpenSocket()
    {
        if (! $this->socket) {
            throw new SocketException('Socket is not open');
        }
    }

    public function __construct($host, $port, $timeout)
    {
        $this->socket = @fsockopen($host, $port, $errorNo, $errorStr, $timeout);

        if (! $this->socket) {
            throw new ConnectionException($errorStr, $errorNo);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function open()
    {
        return is_resource($this->socket);
    }

    public function write($str)
    {
        $this->ensureOpenSocket();

        $length = fwrite($this->socket, $str);

        if ($length !== mb_strlen($str, '8bit')) {
            throw new WriteException('Could not write to socket');
        }

        return $this;
    }

    public function writeline($str, $terminator = "\r\n")
    {
        $this->ensureOpenSocket();

        if (! $this->socket) {
            throw new SocketException('Socket is not open');
        }

        return $this->write($str.$terminator);
    }

    /**
     * Read until EOL or EOF (whichever comes first).
     *
     * @return string
     */
    public function readLine()
    {
        $this->ensureOpenSocket();

        return fgets($this->socket);
    }

    /**
     * Read until EOF or $max_bytes (whichever comes first).
     *
     * @param int $max_bytes
     *
     * @return string
     */
    public function read($max_bytes = 1024)
    {
        $this->ensureOpenSocket();

        return fread($this->socket, $max_bytes);
    }

    public function close()
    {
        if (! $this->open()) {
            return false;
        }

        if (! fclose($this->socket)) {
            throw new SocketException('Could not close socket');
        }

        $this->socket = null;

        return true;
    }
}
