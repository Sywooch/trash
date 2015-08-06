<?php
/**
 *
 */
$output = shell_exec('ssh sel-web3.docdoc.pro "php ~/docdoc/phpunit" --configuration ~/docdoc/phpunit.xml');

echo $output;
exit;
