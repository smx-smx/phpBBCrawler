<?php
/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
require_once '../vendor/autoload.php';

spl_autoload_register();

function errHandle($errNo, $errStr, $errFile, $errLine) {
	if (error_reporting() == 0) {
        // @ suppression used, don't worry about it
        return;
    }
	
	$msg = "$errStr in $errFile on line $errLine";
	if ($errNo == E_NOTICE || $errNo == E_WARNING) {
		throw new ErrorException($msg, $errNo);
	} else {
		echo $msg;
	}
}

set_error_handler('errHandle');
assert_options(ASSERT_ACTIVE, true);
assert_options(ASSERT_BAIL, true);

if($argc < 3){
	fwrite(STDERR, "Usage: {$argv[0]} <name> <baseUrl> [<username> <password>]" . PHP_EOL);
	return 1;
}
$name = $argv[1];
$baseUrl = $argv[2];

$exporter = new Exporter($name, $baseUrl);
if($argc > 3){
	$username = $argv[3];
	$password = $argv[4];	
	$exporter->login($username, $password);
}
$exporter->run();
