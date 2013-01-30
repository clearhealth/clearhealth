<?php
/*****************************************************************************
*       FilesController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class FilesController extends WebVista_Controller_Action {

	public function flatAction() {
		$this->view->data = $this->_getParam('data','');
		header('Content-Disposition: attachment; filename="file.txt"');
		//header('Content-type: application/vnd.ms-excel');
		header('Content-type: text/plain');
		$this->render('index');
	}

	public function xmlAction() {
		$this->view->data = $this->_getParam('data','');
		header('Content-Disposition: attachment; filename="file.xml"');
		header('Content-type: application/xml');
		$this->render('index');
	}

	public function typesAction() {
		$type = $this->_getParam('type');
		switch ($type) {
			case 'icon':
			case 'table':
			case 'thumbs':
			case 'thumb':
			case 'tiles':
				break;
			default:
				$type = 'icon';
				break;
		}
		header('Content-type: text/xml');
		$this->render('f'.$type.'.xsl');
	}

	public function thumbsCreatorAction() {
		$filename = tempnam(sys_get_temp_dir(),'picture_');
		$paramImg = $this->_getParam('img');
		$x = explode('/',$paramImg);
		$attachmentId = (int)$x[0];
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('attachmentBlobs')
				->where('attachmentId = ?',$attachmentId);
		if ($row = $db->fetchRow($sqlSelect)) {
			file_put_contents($filename,$row['data']);
		}
		list($width,$height) = getimagesize($filename);
		$paramWidth = $this->_getParam('width');
		$paramHeight = $this->_getParam('height');
		$newWidth = ($paramWidth?$paramWidth:0);
		$newHeight = ($paramHeight?$paramHeight:0);
		$imgInfoList = getimagesize($filename);

		$widthRatio = ($newWidth!=0?$width/$newWidth:1);
		$heightRatio = ($newHeight!=0?$height/$newHeight:1);

		if (($widthRatio<1)||($heightRatio<1)) {
			($widthRatio>$heightRatio?$heightRatio=$widthRatio:$widthRatio=$heightRatio);
			$newWidth = ($newWidth<$width/$widthRatio?$width/$widthRatio:$newWidth);
			$newHeight = ($newHeight<$height/$heightRatio?$height/$heightRatio:$newHeight);
		} else {
			($widthRatio>$heightRatio?$heightRatio=$widthRatio:$widthRatio=$heightRatio);
			$newWidth = ($newWidth<$width/$widthRatio?$width/$widthRatio:$newWidth);
			$newHeight = ($newHeight<$height/$heightRatio?$height/$heightRatio:$newHeight);
		}

		//Get file content type
		$mime = $imgInfoList['mime'];
		//Create img from file(url) based on img type
		switch ($mime) {
		  	case 'image/gif':
		  		$img = imagecreatefromgif($filename);
  				break;
		  	case 'image/png':
  				$img = imagecreatefrompng($filename);
		  		break;
		  	case 'image/jpeg':
				$img = imagecreatefromjpeg($filename);
		  		break; 
		  	case 'image/bmp':
				$img = imagecreatefrombmp($filename);
		  		break;
		}

		$resizedImg = imagecreatetruecolor($newWidth,$newHeight);
		$bgColor = imagecolorallocate($resizedImg,255,255,255);
		imagefill($resizedImg,0,0,$bgColor);

		if (($mime == 'image/gif') || ($mime == 'image/png')) {
			$colorTransparent = imagecolortransparent($img);
			imagecolortransparent($resizedImg, $colorTransparent);
		}

		// File and new size
		imagecopyresampled($resizedImg,$img,($newWidth>$width/$widthRatio?($newWidth-$width/$widthRatio)/2:0),($newHeight>$height/$heightRatio?($newHeight-$height/$heightRatio)/2:0),0,0,$width/$widthRatio,$height/$heightRatio,$width,$height);

		switch ($mime) {
		  	case 'image/gif':
		  		imagegif($resizedImg);
		  		break;
		  	case 'image/png':
		  		imagepng($resizedImg);
		  		break;
		  	case 'image/jpeg':
				imagejpeg($resizedImg);
		  		break;
		  	case 'image/bmp':
				imagetmp($resizedImg);
		  		break;
		}

		header('Content-type: '.$mime);
		imagepng($resizedImg);	
		imagedestroy($resizedImg);
		$this->render();
	}

	public function zipAction() {
		$this->view->data = $this->_getParam('data','');
		header('Content-Disposition: attachment; filename="file.zip"');
		header('Content-type: application/zip');
		$this->render('index');
	}

}
