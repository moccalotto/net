<?php

namespace Moccalotto\Net\Smtp;

/**
 * DTO to describe the result of email validation.
 */
class EmailStatus
{
    const SYNTAX = [false, 'syntax',   'email has incorrect syntax'];
    const NO_DNS = [false, 'no_dns',   'domain has not DNS entries'];
    const NO_MX = [false, 'no_mx',    'domain has no MX servers'];
    const BOUNCED = [false, 'bounced',  'smtp server bounced the email'];
    const DEFERRED = [false, 'deferred', 'no smtp server could presently accept the email'];
    const HANDSKAKE = [true,  'handshake','server handshake ok'];
    const ACCEPTED = [true,  'accepted', 'email accepted'];

    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $code;

    /**
     * Constructor.
     *
     * @param array $status Use one of the EmailStatus::* constants here.
     * @param string $message The return message from the SMTP server.
     */
    public function __construct(array $status, $message = '')
    {
        $this->success = (bool) $status[0];
        $this->type = $status[1];
        $this->description = $status[2];
        $this->message = $message;
        $this->code = (int) $message; // extract the number from the message
    }

    /**
     * The status type.
     *
     * @return bool
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * The status  message (if any).
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * The status code (if any).
     *
     * @return int
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Does the status indicate that the mail bounced?
     *
     * @return bool
     */
    public function bounced()
    {
        return $this->type == 'bounced';
    }

    /**
     * Does the status indicate that the email was accepted?
     *
     * @return bool
     */
    public function accepted()
    {
        return $this->type == 'accepted';
    }

    /**
     * Does the status indicate that the operation succeeded?
     *
     * @return bool
     */
    public function success()
    {
        return $this->success;
    }
}
