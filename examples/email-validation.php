<?php

use Moccalotto\Net\Smtp\MailChecker;

require '../vendor/autoload.php';

$email = json_decode(file_get_contents('../composer.json'))->authors[0]->email;

$mc = new MailChecker('moccalotto@gmail.com', 'example.com');

print_r($mc->verify($email));
