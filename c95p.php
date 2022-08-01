<?php
$percentiles = 0.95;

if (count($argv) < 2) {
    echo "script calculates 95 percentile for the last 2 days from rrd data\n";
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

$values_in = $rrd->readfile($rrdFilename, 'IN');
$values_out = $rrd->readfile($rrdFilename, 'OUT');
$values_max = $rrd->readfile($rrdFilename, 'MAX');

echo "by IN:\t"  . $rrd->percentileMax($values_in,  $percentiles) . " Mbps\n";
echo "by OUT:\t" . $rrd->percentileMax($values_out, $percentiles) . " Mbps\n";
echo "by MAX:\t" . $rrd->percentileMax($values_max, $percentiles) . " Mbps\n";