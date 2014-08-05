<?php

class Caching{
	private $cache_file_hash;
	private $cache_file_compressed;
	private $cache_file_uncompressed;

	private $cached_etag;
	private $contentType;

	private $dir;
	private $files;

	public function __construct($cacheFile, $contentType, $dir, $files){
		$this->cache_file_hash = $cacheFile.'.md5';
		$this->cache_file_compressed = $cacheFile.'.gz';
		$this->cache_file_uncompressed = $cacheFile.'.plain';

		$this->dir = $dir;
		$this->files = $files;

		$this->contentType = $contentType;

		$this->cached_etag = @file_get_contents($this->cache_file_hash);
	}

	public function send(){
		header('Content-Type: '.$this->contentType);
		header("Expires: ".gmdate("D, d M Y H:i:s", time() + 3600*24)." GMT");

		if($this->validateEtag()){
			header('HTTP/1.1 304 Not Modified');
		}
		else{
			$this->updateFiles();
			$this->sendFile();
		}
	}

	private function validateEtag(){
		return isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] && $this->cached_etag == $_SERVER['HTTP_IF_NONE_MATCH'];
	}

	private function updateFiles(){
		$new_etag = '';
		foreach($this->files as $file){
			$new_etag .= md5_file($this->dir.$file);
		}
		$new_etag = md5($new_etag);

		if(!$new_etag || $this->cached_etag != $new_etag
			|| !file_exists($this->cache_file_hash)
			|| !file_exists($this->cache_file_compressed)
			|| !file_exists($this->cache_file_uncompressed)
			){
			$this->cached_etag = $new_etag;

			$fpHash = fopen($this->cache_file_hash, 'w');
			fputs($fpHash, $new_etag);
			fclose($fpHash);

			$fpUncompressed = fopen($this->cache_file_uncompressed, 'w');
			foreach($this->files as $file){
				fputs($fpUncompressed, file_get_contents($this->dir.$file));
			}
			fclose($fpUncompressed);

			$fpCompressed = fopen($this->cache_file_compressed, 'wb');
			fputs($fpCompressed, gzencode(file_get_contents($this->cache_file_uncompressed), 9, FORCE_GZIP));
			fclose($fpCompressed);
		}
	}

	private function sendFile(){
		header('Etag: '.$this->cached_etag);

		if($this->clientAcceptsCompressedData()){
			header("Content-Encoding: gzip");
			echo @file_get_contents($this->cache_file_compressed);
		}
		else{
			echo @file_get_contents($this->cache_file_uncompressed);
		}
	}

	private function clientAcceptsCompressedData(){
		return isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], "gzip") !== false;
	}
}


?>