<?php
// config.php
// Configuration for the PHP gateway layer.
//
// Deliberately NOT here: the list of feature names. That lives in
// Python's config.py, which is the single source of truth for the


define('PYTHON_SERVICE_URL', 'https://credit-rating-engine.onrender.com');
define('PYTHON_SERVICE_TIMEOUT', 30);
define('APP_NAME', 'Hobbiton Credit Rating Gateway');