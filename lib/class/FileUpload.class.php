<?php
defined('_MCSHOP') or die("Security block!");

class FileUpload
{
	private $maxSize;
	private $FILE;
	private $extension;

	public function __construct($FILE, $maxSize = 1000000 /*1MB*/)
	{
		$this->FILE = $FILE;
		$this->maxSize = $maxSize;
		$this->extension = FileUpload::getExtension($FILE['name']);
	}

	#return 0: keine Datei angegeben
	#return -1: Allgemeiner Fehler
	#return -2: Datei ist zu groß
	public function isValidUpload()
	{
		if($this->FILE['error'] == 4)
			return 0;
		if($this->FILE['error'] != 0)
			return -1;
		if(!is_uploaded_file($this->FILE['tmp_name']))
			return -1;
		if(filesize($this->FILE['tmp_name']) > $this->maxSize)
			return -2;
		return true;
	}

	#return -3: keine Bilddatei
	#return -4: ungültige Bilddatei
	public function isValidImageUpload($maxHeight = 0, $maxWidth = 0)
	{
		$return = $this->isValidUpload();
		if($return !== true) return $return;

		$extension = ($this->extension == 'jpeg' ? 'jpg' : $this->extension);

		$return = FileUpload::createThumbnail($this->FILE['tmp_name'], $this->FILE['tmp_name'], $maxWidth, $maxHeight, null, $extension);
		if($return < 0)
			return $return;
		return true;

		#region alt
		try
		{
			if($extension == 'jpg')
			{
				$originalImage = imagecreatefromjpeg($this->FILE['tmp_name']);
			}
			elseif($extension == 'png')
			{
				$originalImage = imagecreatefrompng($this->FILE['tmp_name']);
			}
			elseif($extension == 'gif')
			{
				$originalImage = imagecreatefromgif($this->FILE['tmp_name']);
			}
			else
			{
				return -3;
			}
		}
		catch(Exception $e)
		{
			return -4;
		}

		list($width,$height) = getimagesize($this->FILE['tmp_name']);

		list($newWidth, $newHeight) = FileUpload::calcSize($width, $height, $maxWidth, $maxHeight);

		if($newWidth != $width || $newHeight != $height)#Bildgröße muss verändert werden
		{
			$newImage = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($newImage,$originalImage, 0,0,0,0, $newWidth,$newHeight,$width,$height);
			unlink($this->FILE['tmp_name']);

			if($extension == 'jpg')
			{
				imagejpeg($newImage, $this->FILE['tmp_name'], 100);
			}
			elseif($extension == 'png')
			{
				imagepng($newImage, $this->FILE['tmp_name'], 100);
			}
			else
			{
				imagegif($newImage, $this->FILE['tmp_name']);
			}
			imagedestroy($newImage);
		}
		imagedestroy($originalImage);
		return true;
		#endregion
	}

	#Wenn $target ein Dateiname ist, wird versucht, die hochgeladene Datei dorthin zu verschieben.
	#Wenn $target ein Ordner ist, wird ein zufälliger Dateiname generiert (Standardlänge 28 Zeichen + Dateiendung)
	public function moveUploadedFile($target, $len = 28)
	{
		if(is_dir($target)) //Dateiname generieren
		{
			#Slash am Ende anfügen
			$target = str_replace('\\', '/', $target);
			if((strlen($target) > 0) && ($target[strlen($target)-1] != '/')) $target .= '/';
			do
			{
				$filename = strtolower(random_number_by_length($len));
			} while(file_exists($target.$filename.'.'.$this->extension));
			$target .= $filename.'.'.$this->extension;
		}
		if(rename($this->FILE['tmp_name'], $target))
		{
			chmod($target, 0754);
			return $filename.'.'.$this->extension;
		}
		return false;
	}

/*Wenn $target ein Dateiname ist, wird versucht, die hochgeladene Datei dorthin zu verschieben.
Wenn $target ein Ordner ist, wird ein zufälliger Dateiname generiert (Standardlänge 28 Zeichen + Dateiendung)

returns
 -1: Ungültige maximale Größe
 -2: keine/ungültige Quelldatei
 -3: Zieldatei ist größer als $maxFileSize
return <string>: Der neue Dateiname
*/
	public static function createThumbnail($source, $target, $maxWidth = 0, $maxHeight = 0, $len = 28, $maxFileSize = MAX_PRODUCT_IMAGE_FILE_SIZE){
		if($maxWidth <= 0 && $maxHeight <= 0)
			return -1;
		if(!file_exists($source))
			return -2;

		//Die Dateiendung wird unabhängig vom Dateinamen ermittelt
		$originalImage = FileUpload::imageCreateFromAny($source, $ex);
		if(!$ex)
			return -2;

		if(is_dir($target)){
			$target = str_replace('\\', '/', $target);
			if((strlen($target) > 0) && ($target[strlen($target)-1] != '/')) $target .= '/';
			do
			{
				$filename = strtolower(random_number_by_length($len));
			} while(file_exists($target.$filename.'.'.$ex));
			$target .= $filename.'.'.$ex;
		}

		$width = imagesx($originalImage);
		$height = imagesy($originalImage);

		list($newWidth, $newHeight) = FileUpload::calcSize($width, $height, $maxWidth, $maxHeight);

		if($newWidth == $width && $newHeight == $height){#Bildgröße muss nicht verändert werden
			if(filesize($source) > $maxFileSize)
				return -3;
			copy($source, $target);
		}
		else{
			$newImage = imagecreatetruecolor($newWidth, $newHeight);

			if($ex == 'png'){
				imagealphablending($newImage, false);
				$color = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
				imagefill($newImage, 0, 0, $color);
				imagesavealpha($newImage, true);
			}
			elseif($ex == 'gif' && imagecolortransparent($originalImage) >= 0){
				$transparent_color = imagecolorsforindex($originalImage, $trnprt_indx);
				$transparency = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($newImage, 0, 0, $transparency);
				imagecolortransparent($newImage, $transparency);
			}
			imagecopyresampled($newImage,$originalImage, 0,0,0,0, $newWidth,$newHeight,$width,$height);

			if(file_exists($target)) unlink($target);
				
			if($ex == 'jpg'){
				imagejpeg($newImage, $target, 100);
			}
			elseif($ex == 'gif'){
				imagegif($newImage, $target);
			}
			else{
				imagepng($newImage, $target, 8);
			}
			imagedestroy($newImage);
		}
		imagedestroy($originalImage);

		chmod($target, 0754);
		if(filesize($target) > $maxFileSize){
			unlink($target);
			return -3;
		}
		return $target;
	}

	private static function calcSize($width, $height, $maxWidth = 0, $maxHeight = 0){
		$newWidth = $width;
		$newHeight = $height;
		if(($maxWidth > 0) && ($width > $maxWidth))
		{
			$newWidth = $maxWidth;
			$newHeight = $newWidth * $height / $width;

			if(($maxHeight > 0) && ($newHeight > $maxHeight))
			{
				$newHeight = $maxHeight;
				$newWidth = $newHeight * $width / $height;
			}
		}
		elseif(($maxHeight > 0) && ($height > $maxHeight))
		{
			$newHeight = $maxHeight;
			$newWidth = $newHeight * $width / $height;
		}
		return array($newWidth, $newHeight);
	}

	public static function getExtension($str){
		$i = strrpos($str,'.');
		if(!$i) return null; 

		$l = strlen($str) - $i - 1;
		$ext = substr($str,-$l);
		return strtolower($ext);
	}

	public static function imageCreateFromAny($filepath, &$ext) {
		$type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
		$allowedTypes = array(
			1,  // [] gif
			2,  // [] jpg
			3  // [] png
			//6   // [] bmp
		);
		if (!in_array($type, $allowedTypes)) {
			return false;
		}
		switch ($type) {
			case 1 :
				$im = imageCreateFromGif($filepath);
				$ext = 'gif';
				break;
			case 2 :
				$im = imageCreateFromJpeg($filepath);
				$ext = 'jpg';
				break;
			case 3 :
				$im = imageCreateFromPng($filepath);
				$ext = 'png';
				break;
			/*case 6 :
				$im = imageCreateFromBmp($filepath);
				break;*/
			default:
				$ext = null;
		}   
		return $im; 
	}
}
?>