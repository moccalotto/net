<?php

namespace Moccalotto\Net\Smtp;

use Exception;

class MailChecker
{
    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var string
     */
    protected $senderFqdn;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @internal
     * @param string email
     * @return array
     */
    protected function dnsInfo($email)
    {
        list(, $domain) = explode('@', $email);

        return new DnsInfo($domain);
    }

    /**
     * Create a socket object.
     *
     * Override this method if you wish to use a custom socket object.
     *
     * @param string $hostname
     * @param int $port
     *
     * @return Socket
     */
    protected function makeSocket($hostname, $port = 25)
    {
        return new Socket($hostname, $port, $this->timeout);
    }

    /**
     * Create a Connection object.
     *
     * Override this method to if you wish to use a custom connection object
     *
     * @param string $hostname
     *
     * @return Connection
     */
    protected function makeConnection($hostname)
    {
        return new Connection(
            $this->makeSocket($hostname),
            $this->senderEmail,
            $this->senderFqdn
        );
    }

    /**
     * Check if a given hostname bounces, defers
     * or accepts a given email address.
     *
     * @param string $hostname
     * @param string $email
     *
     * @return EmailStatus
     */
    protected function verifyAgainstSingleServer($hostname, $email)
    {
        try {
            return $this
                ->makeConnection($hostname)
                ->simulateSend($email);
        } catch (Exception $e) {
            return new EmailStatus(EmailStatus::DEFERRED);
        }
    }

    /**
     * Constructor.
     *
     * @param string $senderEmail email address of sender.
     * @param string $senderFqdn fully qualified domain name of sender address
     * @param int $timeout
     */
    public function __construct($senderEmail, $senderFqdn, $timeout = 10)
    {
        $this->senderEmail = $senderEmail;
        $this->senderFqdn = $senderFqdn;
        $this->timeout = (int) $timeout;
    }

    /**
     * @param string $email
     *
     * @return EmailStatus
     */
    public function verify($email)
    {
        // Stop execution if the email
        // address is syntax-invalid
        if (! filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            return new EmailStatus(EmailStatus::SYNTAX);
        }

        $domain = $this->dnsInfo($email);

        // Stop execution if no
        // DNS records exists
        if (! $domain->exists()) {
            return new EmailStatus(EmailStatus::NO_DNS);
        }

        $mx_hosts = $domain->mxHosts();

        // Stop execution if no
        // DNS records exists
        if (empty($mx_hosts)) {
            return new EmailStatus(EmailStatus::NO_MX);
        }

        foreach ($mx_hosts as $hostname => $priority) {
            // Use the current server to
            // verify the email address
            $status = $this->verifyAgainstSingleServer($hostname, $email);

            // If the server accepts the email,
            // that email address is valid
            if ($status->accepted()) {
                return $status;
            }

            // If the server hard-bounces the email,
            // we must assume that no servers for
            // the domain will accept the mail
            if ($status->bounced()) {
                return $status;
            }

            // If this code is reached, the
            // email is "deferred", and
            // we try the next server
        }

        // If this code is reached, no servers
        // accepted or bounced the email,
        // and we declare it "deferred"
        return new EmailStatus(EmailStatus::DEFERRED);
    }
}
