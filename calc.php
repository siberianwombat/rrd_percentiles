<?php
$percentiles = 0.95;

if (count($argv) < 2) {
    echo "calculates percentiles from the log file by prefix\n";
    echo "usage: php {$argv[0]} xai|retn|utt|/path/to/file.rrd (prefix)\n";
    die;
}

$prefix = count($argv) > 2 ? $argv[2] : "";

$config = require dirname(__FILE__).'/config.php';

require dirname(__FILE__).'/rrd.lib.php';

$src = $argv[1];
$rrd = new rrd($config);

$logFilename = $rrd->getLogFilename($src);
echo "reading $logFilename " . ($prefix ? "with prefix [$prefix]" : "without filtering") . " \n";

$values = $rrd->readLogFile($logFilename, $prefix);

$values_in = array();
$values_out = array();
$values_max = array();
foreach($values as $line) {
    $values_in[] = $line[1];
    $values_out[] = $line[2];
    $values_max[] = $line[1]>$line[2] ? $line[1] : $line[2];
}

echo "by IN:\t"  . $rrd->percentileMax($values_in,  $percentiles) . " Mbps\n";
echo "by OUT:\t" . $rrd->percentileMax($values_out, $percentiles) . " Mbps\n";
echo "by MAX:\t" . $rrd->percentileMax($values_max, $percentiles) . " Mbps\n";