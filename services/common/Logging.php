<?php
/**
 * Logging class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class Logging {

    // declare log file and file pointer as private properties
    private $log_file, $log_size, $nl, $fp;
	private $sources = array();
	private static $logging;
    // set log file (path and name)
    public function lfile($path, $size=1) {
        $this->log_file = $path;
        $this->log_size = $size *(1024*1024); // Megs to bytes
    }
	
	private static function getInstance()
	{
		if(!isset($logging)){
			$logging = new Logging();
			$logging->lfile(false);
		}
		return $logging;
	}
	
	public static function debug($source, $message)
	{
		Logging::log($source, 'DEBUG', $message);
	}
	
	public static function info($source, $message)
	{
		Logging::log($source, 'INFO', $message);
	}
	
	public static function warn($source, $message)
	{
		Logging::log($source, 'WARNING', $message);
	}
	
	public static function error($source, $message)
	{
		Logging::log($source, 'ERROR', $message);
	}
	
	private static function log($source, $type, $message)
	{
		Logging::getInstance()->lwrite($source, "[$type] $message");
	}
	
	private function getScriptName($object) {
		$className = get_class($object);
		if(!isset($sources[$className]))
		{
			$reflection = new ReflectionClass($object);
			$sources[$className] = $reflection->getFileName();
		}
		return $sources[$className];
    }
	
    // write message to the log file
    public function lwrite($source, $message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        //$script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		$script_name = pathinfo($this->getScriptName($source), PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[Y-m-d:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time [$script_name] $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'C:/xampp/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
		
        // define log file from lfile method or use previously set default
		$lfile = $this->log_file ? $this->log_file : $log_file_default;
		if (file_exists($lfile)) {
			if (filesize($lfile) > $this->log_size) {
				$this->fp = fopen($lfile, 'w');
				fclose($this->fp);
				unlink($lfile);
			  }
		}
		// open log file for writing only and place file pointer at the end of the file
		// (if the file does not exist, try to create it)
		$this->fp = fopen($lfile, 'a') or exit("Can't open file!");
    }
}
?>