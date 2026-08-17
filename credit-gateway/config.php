<?php
// config.php
// Configuration for the PHP gateway layer.
//
// Deliberately NOT here: the list of feature names. That lives in
// Python's config.py, which is the single source of truth for the
// model's contract. PHP doesn't need a copy of it — see handlers/score.php.

define('PYTHON_SERVICE_URL', 'http://localhost:5000');
define('PYTHON_SERVICE_TIMEOUT', 10); // seconds — don't let a slow model hang the gateway forever
define('APP_NAME', 'Hobbiton Credit Rating Gateway');