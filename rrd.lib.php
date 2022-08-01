<?php

class rrd {
    public $debug           = false;
    private $db_started     = false;    
    private $db_start_tag   = '/<database>/';
    private $db_end_tag     = '/<\/database>/';
    private $valuesRE       = '/([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}).*?<v>([^<]*)<\/v><v>([^<]*)<\/v>/';
    private $config         = array();

	public function __construct($config) {
        $this->config = $config;
    }

    public function readFile($filename, $returnType = 'ALL') {
        $values = array();
        $lines = explode("\n", `rrdtool dump $filename`);        
        $this->db_started = false;
        foreach($lines as $line) {
            if (preg_match($this->db_start_tag, $line)) {
                $this->db_started = true;
                continue;
            }
            if (!$this->db_started) continue;
            if (preg_match($this->db_end_tag, $line)) break;
            if (preg_match($this->valuesRE, $line, $matches)) {
                $in = $this->fromBytesToMbits($matches[2]);
                $out = $this->fromBytesToMbits($matches[3]);
                $max = $in > $out ? $in : $out;
                switch ($returnType) {
                    case 'ALL': 
                        $values[] = array(
                            'timestamp' => $matches[1],
                            'in'        => $in,
                            'out'       => $out,
                        );
                        break;                        
                    case 'IN':
                        $values[] = $in;
                        break;
                    case 'OUT':
                        $values[] = $out;
                        break;
                    case 'MAX':
                        $values[] = $max;
                        break;
                }
                if ($this->debug) echo "{$matches[1]}\t$in\t$out\n";
            }
        }
        return $values;
    }

    public function percentileMax($arr, $perc) {
        sort($arr);
        $percentile_position = (int)(count($arr) * $perc);
        $percentileValue = $arr[$percentile_position];
        return $percentileValue;
    }

    public function fromBytesToMbits($val) {
        return round(($val * 8)/1e6, 2);
    }

    public function getRrdFilename($source) {
        return $filenameTemplate = array_key_exists($source, $this->config['default_sources'])
            ? $this->config['default_sources'][$source]['rrd_file']
            : $source;
    }

    public function getLogFilename($source, $date = "") {
        $filenameTemplate = array_key_exists($source, $this->config['default_sources'])
            ? $this->config['default_sources'][$source]['log_file']
            : $this->config['default_log_template'];

        $rrdFile = basename($source, '.rrd');
        // $date = date_format('Ym');

        return str_replace(array('{RRDFILE}', '{DATE}'), array($rrdFile, $date), $filenameTemplate);
    }

    public function lastTimestamp($filename) {
        $fp = @fopen($filename, "r");
        if (!$fp) return "2022-07-30 00:00:00";
        $data = array();
        while (($data = fgetcsv($fp, 1024, ",")) !== FALSE) {
            // do nothing
            $lastData = $data;
        }        
        fclose($fp);
        return $lastData[0];
    }

    public function appendLog($filename, $logArray, $appendAfter) {
        $fp = fopen($filename, 'a');

        foreach ($logArray as $logRecord) {
            if ($logRecord['timestamp'] > $appendAfter) {
                fputcsv($fp, $logRecord);
            }
        }

        fclose($fp);
    }

    public function writeLog($filename, $logArray) {
        $lastTimestamp = $this->lastTimestamp($filename);
        $this->appendLog($filename, $logArray, $lastTimestamp);
    }
}