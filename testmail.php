<?php

require_once 'mail.php';

$result = kirimOTP(
    'raymondalim2@gmail.com',
    'Remon',
    '4241'
);

var_dump($result);