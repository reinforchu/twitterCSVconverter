<?php
/**
 * License: GPLv3
 * Date: 2014-05-15 Asia/Tokyo
 * GitHub: https://github.com/reinforchu/twitterCSVconverter
 * Web: http://reinforce.tv/
 */

/**
 * Convert CSV file to JSON file of twitter
 * @author reinforchu
 * @version 0.1.0.0
 */
class twitterCSVconverter {
	private $outputPath;
	private $status;

	/**
	 * Constructor
	 * Execute
	 * @param sourcePath JSON files path
	 * @param outputPath CSV file path
	 */
	public function __construct($sourcePath, $outputPath, $csvDefine) {
		$this->outputPath = $outputPath;
		self::initCSVfile($csvDefine);
		self::CSVconvert($sourcePath);
	}

	/**
	 * for Stream API Search and Trend JSON file
	 * @param path JSON files path
	 */
	private function CSVconvert($path) {
		$i = $count = 0;
		if ($handle = opendir($path)) {
			while (FALSE !== ($fileName = readdir($handle))) {
				++$i;
				if ($i > 2 ) {
					$filePath = $path.$fileName;
					$data = json_decode(file_get_contents($filePath), TRUE);
					if (isset($data['text'])) {
						$this->status = $data;
						self::writeData("\r\n");
						self::writeData($count);
						self::writeData(',');
						self::writeData(self::getID());
						self::writeData(',');
						self::writeData(self::getScreenName());
						self::writeData(',');
						self::writeData("\"".self::getUserName()."\"");
						self::writeData(',');
						self::writeData("\"".self::getBody()."\"");
						self::writeData(',');
						self::writeData("\"".self::getDate()."\"");
						self::writeData(',');
						self::writeData(self::getURL());
						self::writeData(',');
						self::writeData("\"".self::getSource()."\"");
						++$count;
					}
				}
			}
			closedir($handle);
		}
	}
	
	private function getID() {
		return $this->status['user']['id'];
	}
	
	private function getScreenName() {
		return $this->status['user']['screen_name'];
	}
	
	private function getUserName() {
		return $this->status['user']['name'];
	}
	
	private function getBody() {
		return $this->status['text'];
	}
	
	private function getDate() {
		sscanf($this->status['created_at'], "%s %s %d %s %s %d", $week, $month, $day, $time, $gmt, $year);
		$rcf2822 = "{$week}\054\040{$day}\040{$month}\040{$year}\040{$time}\040{$gmt}";
		$unixEpoch = strtotime($rcf2822);
		$localTime = date("o/m/d\040H:i:s", $unixEpoch);
		return $localTime;
	}
	
	private function getURL() {
		return "https://twitter.com/{$this->status['user']['screen_name']}/status/{$this->status['id_str']}";
	}
	
	private function getSource() {
		if (preg_match("/\A(<.+>)(.+)(<.+>)\z/u", $this->status['source'], $matches, PREG_OFFSET_CAPTURE, 0)) {
			return $matches['2']['0'];
		} else {
			return 'web';
		}
	}
	
	/**
	 * CSV file writer
	 * @param define columns
	 */
	private function initCSVfile($define) {
		$pointer = fopen($this->outputPath, 'w+');
		if (flock($pointer, LOCK_EX)) {
			fwrite($pointer, $define);
			flock($pointer, LOCK_UN);
			fclose($pointer);
		}
	}
	
		private function writeData($body) {
		$pointer = fopen($this->outputPath, 'a');
		if (flock($pointer, LOCK_EX)) {
			fwrite($pointer, mb_convert_encoding($body, 'SJIS')); // for Excel
			flock($pointer, LOCK_UN);
			fclose($pointer);
		}
	}
}