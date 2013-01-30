<?php
/*****************************************************************************
*       Prescription.php
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


class Prescription {

	// Prescriber Info
	protected $prescriberName = '';
	protected $prescriberStateLicenseNumber = '';
	protected $prescriberDeaNumber = '';
	protected $prescriberSignature = '';

	// Practice Info
	protected $practiceName = '';
	protected $practiceAddress = '';
	protected $practiceCity = '';
	protected $practiceState = '';
	protected $practicePostalCode = '';

	// Patient Info
	protected $patientName = '';
	protected $patientAddress = '';
	protected $patientCity = '';
	protected $patientState = '';
	protected $patientPostalCode = '';
	protected $patientDateOfBirth = '';

	// Medication Info
	protected $medicationDatePrescribed = '';
	protected $medicationDescription = '';
	protected $medicationComment = '';
	protected $medicationQuantity = 0;
	protected $medicationRefills = 0;
	protected $medicationDirections = '';
	protected $medicationSubstitution = 0;

	protected $imageFile = null;
	protected $dpi = 110;
	protected $paperSize = array('width'=>0,'height'=>0); // in inches
	protected $pxPaperSize = array('width'=>0,'height'=>0); // in pixels
	protected $fonts = array('regular'=>'','bold'=>'');

	public function __construct($width=8.5,$height=11) {
		$this->paperSize['width'] = $width;
		$this->paperSize['height'] = $height;
		$this->pxPaperSize['width'] = $this->paperSize['width'] * $this->dpi;
		$this->pxPaperSize['height'] = $this->paperSize['height'] * $this->dpi;
		$basePath = Zend_Registry::get('basePath');
		$this->fonts = array('regular'=>$basePath.'/fonts/vera.ttf','bold'=>$basePath.'/fonts/verabd.ttf');
	}

	public function __set($key,$value) {
		$this->$key = $value;
	}

	public function __get($key) {
		return $this->$key;
	}

	public function create() {
		$pxHeight = $this->pxPaperSize['height'] / 2; // we only use a quadrant
		$pxWidth = $this->pxPaperSize['width'] / 2; // we only use a quadrant
		$im = imagecreatetruecolor($pxWidth,$pxHeight);
		if (!$im) {
			throw new Exception(__('Failed to create prescription'));
		}

		$font = $this->fonts['regular'];
		$fontBold = $this->fonts['bold'];
		$white = imagecolorallocate($im,255,255,255);
		$black = imagecolorallocate($im,0,0,0);
		imagefilledrectangle($im,0,0,$this->pxPaperSize['width'],$this->pxPaperSize['height'],$white);

		// deduct 1pixel to the right vertical and lower horizontal
		$width = $pxWidth - 1;
		$height = $pxHeight - 1;
		imagedashedline($im,0,0,0,$height,$black); // left vertical
		imagedashedline($im,0,0,$width,0,$black); // upper horizontal
		imagedashedline($im,$width,0,$width,$height,$black); // right vertical
		imagedashedline($im,0,$height,$width,$height,$black); // lower horizontal

		$x = 0;
		$y = 20;
		$size = 11;
		$angle = 0;

		$texts = array();
		$texts[] = $this->prescriberName;
		$texts[] = 'S.L.# '.$this->prescriberStateLicenseNumber.'        D.E.A.# '.$this->prescriberDeaNumber;

		foreach ($texts as $text) {
			$box = imagettfbbox($size,$angle,$fontBold,$text);
			$x = abs(ceil(($pxWidth - $box[2]) / 2));
			$y += 20;
			imagettftext($im,$size,0,$x,$y,$black,$fontBold,$text);
		}

		$texts = array();
		$texts[] = $this->practiceName;
		$texts[] = $this->practiceAddress;
		$texts[] = $this->practiceCity.', '.$this->practiceState.' '.$this->practicePostalCode;

		$y += 20;
		foreach ($texts as $text) {
			$box = imagettfbbox($size,$angle,$font,$text);
			$x = abs(ceil(($pxWidth - $box[2]) / 2));
			$y += 20;
			imagettftext($im,$size,0,$x,$y,$black,$font,$text);
		}

		$data = array();
		$data[] = array(
				'label' => __('Patient Name'),
				'text' => $this->patientName
		);
		$data[] = array(
				'label' => __('Address'),
				'text' => $this->patientAddress.', '.$this->patientCity.' '.$this->patientState.' '.$this->patientPostalCode
		);
		$data[] = array(
				array(
					'label' => __('Date'),
					'text' => $this->medicationDatePrescribed
				),
				array(
					'label' => __('DOB'),
					'text' => $this->patientDateOfBirth)
		);
		$data[] = array(
				'label' => __('Description'),
				'text' => $this->medicationDescription
		);
		$data[] = array(
				'label' => _('Comments'),
				'text' => $this->medicationComment
		);
		$data[] = array(
				array(
					'label' => __('Quantity'),
					'text' => $this->medicationQuantity
				),
				array(
					'label' => __('Refill'),
					'text' => $this->medicationRefills.' '.__('Times')
				)
		);
		$data[] = array(
				'label' => __('Directions'),
				'text' => $this->medicationDirections
		);
		$permitted = '';
		if (!$this->medicationSubstitution) {
			$permitted = __('Not').' ';
		}
		$data[] = array(
				'label' => '',
				'text' => __('Substitution').' '.$permitted.__('Permitted')
		);

		$size = 10;
		$labelMaxWidth = 0;
		$textMaxWidth = 0;
		foreach ($data as $key=>$value) {
			if (!isset($value['label'])) {
				foreach ($value as $k=>$v) {
					$label = $v['label'];
					$box = imagettfbbox($size,$angle,$font,$label);
					$maxWidth = abs(ceil($box[2] - $box[0]));
					$data[$key][$k]['labelWidth'] = $maxWidth;
					if ($maxWidth > $labelMaxWidth) {
						$labelMaxWidth = $maxWidth;
					}
					$text = $v['text'];
					$box = imagettfbbox($size,$angle,$font,$text);
					$maxWidth = abs(ceil($box[2] - $box[0]));
					$data[$key][$k]['textWidth'] = $maxWidth;
					if ($maxWidth > $textMaxWidth) {
						$textMaxWidth = $maxWidth;
					}
				}
			}
			else {
				$label = $value['label'];
				$box = imagettfbbox($size,$angle,$font,$label);
				$maxWidth = abs(ceil($box[2] - $box[0]));
				$data[$key]['labelWidth'] = $maxWidth;
				if ($maxWidth > $labelMaxWidth) {
					$labelMaxWidth = $maxWidth;
				}
				$text = $value['text'];
				$box = imagettfbbox($size,$angle,$font,$text);
				$maxWidth = abs(ceil($box[2] - $box[0]));
				$data[$key]['textWidth'] = $maxWidth;
				if ($maxWidth > $textMaxWidth) {
					$textMaxWidth = $maxWidth;
				}
			}
		}

		$y += 30;
		$angle = 0;
		$ttfData = array();
		$ttfData['im'] = $im;
		$ttfData['size'] = $size;
		$ttfData['angle'] = $angle;
		$ttfData['color'] = $black;
		$ttfData['font'] = $font;
		$widths = array();
		$widths['textMaxWidth'] = $textMaxWidth;
		$widths['labelMaxWidth'] = $labelMaxWidth;
		$widths['pxWidth'] = $pxWidth;
		foreach ($data as $value) {
			$y += 20;
			$x = 20;
			$widths['pxWidth'] = $pxWidth;
			if (!isset($value['label'])) {
				$firstItem = true;
				$maxY = $y;
				$valCtr = count($value);
				$widths['pxWidth'] = $pxWidth / $valCtr;
				$multiline = array('offset'=>0,'length'=>$valCtr);
				for ($i = 0; $i < $valCtr; $i++) {
					$tmpY = $y;
					$val = $value[$i];
					$multiline['offset'] = $i;
					if ($firstItem) {
						$this->_createImageText($x,$tmpY,$val,$ttfData,$widths,true,$multiline);
						$firstItem = false;
					}
					else {
						$this->_createImageText($x,$tmpY,$val,$ttfData,$widths,false,$multiline);
					}
					if ($tmpY > $maxY) {
						$maxY = $tmpY;
					}
				}
				$y = $maxY;
			}
			else {
				$this->_createImageText($x,$y,$value,$ttfData,$widths,true);
			}
		}

		$text = __('Signature');
		$y += 40;
		$x = 20;
		imagettftext($im,$size,0,$x,$y,$black,$font,$text);
		// Signature
		if (strlen($this->prescriberSignature) > 0 && file_exists($this->prescriberSignature)) {
			$srcImageFile = $this->prescriberSignature;
			do {
				$imageSize = getimagesize($srcImageFile);
				if (!$imageSize || $imageSize[2] < 1 || $imageSize[2] > 16) {
					break;
				}
				$srcIm = null;
				switch ($imageSize[2]) {
					case 1: // IMAGETYPE_GIF
						$srcIm = imagecreatefromgif($srcImageFile);
						break;
					case 2: // IMAGETYPE_JPEG
						$srcIm = imagecreatefromjpeg($srcImageFile);
						break;
					case 3: // IMAGETYPE_PNG
						$srcIm = imagecreatefrompng($srcImageFile);
						break;
					case 4: // IMAGETYPE_SWF
					case 5: // IMAGETYPE_PSD
					case 6: // IMAGETYPE_BMP
					case 7: // IMAGETYPE_TIFF_II (intel byte order)
					case 8: // IMAGETYPE_TIFF_MM (motorola byte order)
					case 9: // IMAGETYPE_JPC
					case 10: // IMAGETYPE_JP2
					case 11: // IMAGETYPE_JPX
					case 12: // IMAGETYPE_JB2
					case 13: // IMAGETYPE_SWC
					case 14: // IMAGETYPE_IFF
						break;
					case 15: // IMAGETYPE_WBMP
						$srcIm = imagecreatefromwbmp($srcImageFile);
						break;
					case 16: // IMAGETYPE_XBM
						$srcIm = imagecreatefromxbm($srcImageFile);
						break;
				}
				if (!$srcIm) {
					break;
				}
				$srcX = 0;
				$srcY = 0;
				$dimension = $imageSize; //getimagesize($srcImageFile);
				$srcWidth = $dimension[0];
				$srcHeight = $dimension[1];
				$dstIm = $im;
				$dstX = 120;
				$dstY = $y;
				// attach signature
				$dstWidth = 150;
				$dstHeight = 58;
				imagecopyresampled($dstIm,$srcIm,$dstX,$dstY,$srcX,$srcY,$dstWidth,$dstHeight,$srcWidth,$srcHeight);
				//imagecopy($dstIm,$srcIm,$dstX,$dstY,$srcX,$srcY,$srcWidth,$srcHeight);
				$x = $dstX;
				$y += $srcHeight + 10;
				$text = $this->prescriberName;
				imagettftext($im,$size,0,$x,$y,$black,$fontBold,$text);
			}
			while (false);
			if (file_exists($srcImageFile)) {
				unlink($srcImageFile);
			}
		}

		// Footer
		$size = 8;
		$text = __('This prescription was generated by the ClearHealth EMR system');
		$angle = 0;
		$box = imagettfbbox($size,$angle,$font,$text);
		$x = abs(ceil(($pxWidth - $box[2]) / 2));
		$y = $pxHeight - 10;
		imagettftext($im,$size,0,$x,$y,$black,$font,$text);


		// wrap the generated image in the whole paper size
		$srcIm = $im;
		$srcX = 0;
		$srcY = 0;
		$srcHeight = $pxHeight;
		$srcWidth = $pxWidth;
		$pxHeight *= 2;
		$pxWidth *= 2;
		$dstIm = imagecreatetruecolor($pxWidth,$pxHeight);
		if (!$dstIm) {
			throw new Exception(__('Failed to create prescription'));
		}
		imagefilledrectangle($dstIm,0,0,$this->pxPaperSize['width'],$this->pxPaperSize['height'],$white);

		$dstX = 0;
		$dstY = 0;
		imagecopy($dstIm,$srcIm,$dstX,$dstY,$srcX,$srcY,$srcWidth,$srcHeight);

		$tmpFile = tempnam('/tmp','ch30_');
		$imFile = $tmpFile.'.png';
		rename($tmpFile,$imFile);
		$this->imageFile = $imFile;
		imagepng($dstIm,$imFile);

		imagedestroy($im);
		imagedestroy($dstIm);
	}

	protected function _createImageText(&$x,&$y,$data,$ttfData,$widths,$useLabelMaxWidth = false,Array $multiline = null) {
		$im = $ttfData['im'];
		$size = $ttfData['size'];
		$angle = $ttfData['angle'];
		$color = $ttfData['color'];
		$font = $ttfData['font'];
		$label = $data['label'].''; // force label to string by concatenating with an empty string
		$width = $data['labelWidth'];
		$tmpX = $x;
		$tmpY = $y;
		if (isset($label{0})) {
			imagettftext($im,$size,$angle,$tmpX,$tmpY,$color,$font,$label);
			if ($useLabelMaxWidth > 0) {
				$width = $widths['labelMaxWidth'];
			}
			$tmpX += ($width + 10); // +10 allowance
		}
		$text = $data['text'].''; // force text to string by concatenating with an empty string
		if (isset($text{0})) {
			$textWidth = $data['textWidth'];
			$pxWidth = $widths['pxWidth'];
			// compute the allowable width to place the text
			$allowableWidth = $pxWidth - $tmpX;
			if ($multiline !== null) {
				$offset = $multiline['offset'];
				$length = $multiline['length'];
				$allowableWidth = $pxWidth - (($pxWidth * $offset) + $width + 10);
			}
			$lines = abs(ceil($textWidth / $allowableWidth));
			if ($lines <= 0) {
				$lines = 1;
			}
			$textLen = strlen($text);
			$textPerLine = abs(ceil($textLen / $lines));
			$textLines = explode("[<br />\n]",wordwrap($text,$textPerLine,"[<br />\n]"));
			$lineCtr = count($textLines);
			if ($lineCtr > 0) {
				$longLine = '';
				for ($i = 0; $i < $lineCtr; $i++) {
					$line = $textLines[$i];
					if (strlen($line) > strlen($longLine)) {
						$longLine = $line;
					}
					imagettftext($im,$size,$angle,$tmpX,$tmpY,$color,$font,$line);
					$tmpY += 20;
				}
				$tmpY -= 20;
				$box = imagettfbbox($size,$angle,$font,$longLine);
				$textWidth = abs(ceil($box[2] - $box[0]));
			}
			else {
				imagettftext($im,$size,$angle,$tmpX,$tmpY,$color,$font,$text);
			}
			$tmpX += ($textWidth + 20); // +10 allowance
		}
		$x = $tmpX;
		$y = $tmpY;
	}

}
