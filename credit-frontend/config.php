<?php
// config.php

// Point this at wherever your credit-gateway (Step 3) actually lives.
// If Apache serves it at htdocs/credit-gateway, this is right.
// If you're running it with `php -S localhost:8080` instead, use that URL.
define('GATEWAY_URL', 'http://localhost/credit-gateway');
define('GATEWAY_TIMEOUT', 10);
define('APP_NAME', 'Hobbiton Credit Rating Engine');