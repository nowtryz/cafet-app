<?php
if (file_exists(CONTENT_DIR . 'config.php')) require_once CONTENT_DIR . 'config.php';
else require_once INCLUDES_DIR . 'default_configurations.php';

if (Config::debug) php_info();
else (new ErrorPageBuilder(404))->print();