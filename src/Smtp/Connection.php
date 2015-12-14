<?php

namespace Moccalotto\Net\Smtp;

use LogicException;

class Connection
{
    protected $socket;
    protected $senderEmail;
    protected $senderFqdn;
    protected $exhausted = false;

    /**
     * Constructor.
     *
     * @param Socket $socket
     * @param string $senderEmail
     * @param string $senderFqdn
     */
    public function __construct(Socket $socket, $senderEmail, $senderFqdn)
    {
        $this->senderEmail = $senderEmail;
        $this->senderFqdn = $senderFqdn;

        if (! filter_var($senderEmail, \FILTER_VALIDATE_EMAIL)) {
            throw new LogicException(sprintf(
                'The email address "%s" given as argment 2 is invalid',
                $senderEmail
            ));
        }

        $this->socket = $socket;

        if (! $socket->open()) {
            throw new SocketException('The socket given as argument 1 is closed. You must use an open socket');
        }
    }

    /**
     * Send a message and to the server and get its response.
     *
     * @param string $message
     *
     * @return string
     */
    protected function exchange($message)
    {
        $this->socket->writeline($message);

        return $this->socket->readline();
    }

    /**
     * Say goodbye to the server and close the connection.
     */
    protected function quit()
    {
        $this->exchange('QUIT');
        $this->socket->close();
        $this->exhausted = true;
    }

    /**
     * Handshake with the server.
     *
     * @return EmailStatus
     */
    protected function handshake()
    {
        // If the connection is exhausted,
        // handshaking is not possible
        if ($this->exhausted) {
            throw new LogicException('You cannot handshake after you have quit the connection. You must reconnect by creating a new connection instance');
        }

        // If the server does not say 220 welcome,
        // we assume that something is wrong,
        // and the server defers the email
        $msg = $this->socket->readline();
        if (! preg_match('/220/A', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::DEFERRED, $msg);
        }

        // If the server rejects our FQDN,
        // we assume that it bounces us
        $msg = $this->exchange(sprintf('HELO %s', $this->senderFqdn));
        if (! preg_match('/250/A', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::BOUNCED, $msg);
        }

        $this->handshake = true;

        return new EmailStatus(EmailStatus::HANDSKAKE, $msg);
    }

    /**
     * Validate an email address by
     * simulating sending to it.
     *
     * @param string $email
     *
     * @return EmailStatus
     */
    public function simulateSend($email)
    {
        $status = $this->handshake();

        if (! $status->success()) {
            return $status;
        }

        $msg = $this->exchange(sprintf('MAIL FROM:<%s>', $this->senderEmail));

        if (preg_match('/5[0-9]{2}/A', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::BOUNCED, $msg);
        }

        if (! preg_match('/^250/', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::DEFERRED, $msg);
        }

        $msg = $this->exchange(sprintf('RCPT TO:<%s>', $email));

        if (preg_match('/5[0-9]{2}/A', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::BOUNCED, $msg);
        }

        if (! preg_match('/^250/', $msg)) {
            $this->quit();

            return new EmailStatus(EmailStatus::DEFERRED, $msg);
        }

        $this->quit();

        return new EmailStatus(EmailStatus::ACCEPTED, $msg);
    }
}
