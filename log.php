<?php
$percentiles = 0.95;

if (count($argv) < 2) {
    echo "script preserves log from from rrd file\n";
    echo "usage: php {$argv[0]} xai|retn|utt|/path/to/file.rrd (--debug)\n";
    die;
}

$debug = count($argv) > 2 && $argv[2] === '--debug';

$config = require dirname(__FILE__).'/config.php';

require dirname(__FILE__).'/rrd.lib.php';

$src = $argv[1];
$rrd = new rrd($config);
$rrd->debug = $debug;

$rrdFilename = $rrd->getRrdFilename($src);
$logFilename = $rrd->getLogFilename($src);
echo "writing data from $rrdFilename to $logFilename\n";

$values = $rrd->readfile($rrdFilename);
$rrd->writeLog($logFilename, $values);