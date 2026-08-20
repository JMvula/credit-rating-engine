<?php
// config.php

// Point this at wherever your credit-gateway actually lives.
// If Apache serves it at htdocs/credit-gateway, this is right.

define('GATEWAY_URL', 'http://localhost/credit-gateway');
define('GATEWAY_TIMEOUT', 10);
define('APP_NAME', 'Hobbiton Credit Rating Engine');