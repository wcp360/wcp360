<?php
require dirname(__DIR__).'/vendor/autoload.php';
$_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = getenv('WCP_TEST_DATABASE_URL') ?: 'sqlite:///:memory:';
$_SERVER['WCP_PLANNER'] = getenv('WCP_PLANNER') ?: dirname(__DIR__).'/var/bin/wcp-plan'.(PHP_OS_FAMILY==='Windows'?'.exe':'');
