<?php

require_once 'mail.php';

$result = kirimOTP(
    'melvinjs050906@gmail.com',
    'MelvinJS',
    '4241'
);

var_dump($result);