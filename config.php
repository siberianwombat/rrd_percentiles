<?php
$config = array(
    'percentiles'   => 0.95,
    'default_sources' => array(
        'xai' => array(
            'rrd_file' => '/var/lib/cacti/rra/j00_traffic_in_7805.rrd',
            'log_file' => '/var/log/rrd/xai.rrd.log',
        ),
        'retn' => array(
            'rrd_file' => '/var/lib/cacti/rra/mx00_traffic_in_8152.rrd',
            'log_file' => '/var/log/rrd/retn.rrd.log',
        ),
        'utt' => array(
            'rrd_file' => '/var/lib/cacti/rra/j00_traffic_in_7827.rrd',
            'log_file' => '/var/log/rrd/utt.rrd.log',
        ),
    ),
    'default_log_template' => '/var/log/rrd/{RRDFILE}.rrd.log',
);

if (file_exists('/usr/local/etc/rrdtool/config.php'))
{
	include '/usr/local/etc/rrdtool/config.php';
}

return $config;