<?php
$GLOBALS['loader']->requireOnce('ordo/Document.class.php');

/**
 * Controller Clearhealth Patient actions
 */
class C_WebCam extends Controller {

	function actionUpload($categoryId=0) {
		$this->view->assign('categoryId',(int)$categoryId);
		$this->view->assign('patientId',$this->get('patient_id','c_patient'));
		$this->view->assign('encode_base_dir',urlencode(Celini::getBaseDir()));
		return $this->view->render('upload.html');
	
	}

	function actionSaveImage() {
		$this->_config = $GLOBALS['config']['document_manager'];
		$w = (int)$_POST['width'];
		$h = (int)$_POST['height'];
		$patientId = (int)$_POST['patientId'];
		$categoryId = (int)$_POST['categoryId'];

		// create the image with desired width and height

		$img = imagecreatetruecolor($w, $h);

		// now fill the image with blank color
		// do you remember i wont pass the 0xFFFFFF pixels 
		// from flash?
		imagefill($img, 0, 0, 0xFFFFFF);

		$rows = 0;
		$cols = 0;

		for($rows = 0; $rows < $h; $rows++){
    			// convert the string into an array of n elements
    			$c_row = explode(",", $_POST['px' . $rows]);
    			for($cols = 0; $cols < $w; $cols++){
        			// get the single pixel color value
        			$value = $c_row[$cols];
        			// if value is not empty (empty values are the blank pixels)
        			if($value != ""){
            				// get the hexadecimal string (must be 6 chars length)
            				// so add the missing chars if needed
            				$hex = $value;
            				while(strlen($hex) < 6){ $hex = "0" . $hex; }
            				// convert value from HEX to RGB
            				$r = hexdec(substr($hex, 0, 2));
            				$g = hexdec(substr($hex, 2, 2));
            				$b = hexdec(substr($hex, 4, 2));
            				// allocate the new color
            				// N.B. teorically if a color was already allocated 
            				// we dont need to allocate another time
            				// but this is only an example
            				$test = imagecolorallocate($img, $r, $g, $b);
            				// and paste that color into the image
            				// at the correct position
            				imagesetpixel($img, $cols, $rows, $test);
        			}
    			}			
		}
		$file = array();
		$file['name'] = "pat_pic" . date('Y-m-d H:i:s') . ".jpg";
		$file['path'] = $this->_config['repository'];
                $d = new Document();
		$file['path'] = $file['path'] . $patientId . "/";
		$d->set('foreign_id',$patientId);
		if (!file_exists($file['path'])) {
			mkdir($file['path']);
		}
		ImageJPEG($img,$file['path'].$patientId."_".$file['name']);
		
                $d->url = "file://" .$file['path'].$patientId."_".$file['name'];
                $d->size = filesize($file['path'].$patientId."_".$file['name']);
                $d->type = 'file_url';
                $d->mimetype = 'image/jpeg';
                $d->persist();
                $d->populate();

                if (is_numeric($d->get_id()) ) {
                	$sql = "REPLACE INTO category_to_document set category_id = " . $categoryId . ", document_id = '" . $d->get_id() . "'";
                	$d->_db->Execute($sql);
                }
		$this->messages->addMessage('Image uploaded successfully.');
    		//Output image and clean
    		//header( "Content-type: image/jpeg" );
    		//ImageJPEG( $img );
    		//imagedestroy( $img );        
	}
/*	function actionBatch() {
		 $this->_config = $GLOBALS['config']['document_manager'];
		$dir = opendir($this->_config['repository']);
                        //read each entry in the directory
		$loop = 0;
                        while (($file = readdir($dir)) !== false) {
				if ($file == '.' || $file == '..' || !is_file($this->_config['repository'].$file)) continue;
				echo "file: " . $file . "<br/>";
                                //concat the filename and path
                                $file = $this->_config['repository'] .$file;
                                $file_info = array();
                                //if the filename is a file get its info and put into a tmp array
                                if (is_file($file) && strpos(basename($file),".") !== 0) {
					preg_match("/^([0-9]+)_/",basename($file),$patient_match);
        				$patientId = $patient_match[1];        
					@mkdir($this->_config['repository'].$patientId,0700);
                        		rename($file,$this->_config['repository'].$patientId."/".basename($file)); 
                                        $d = Document::document_factory_url("file://" . $this->_config['repository'].$patientId."/".basename($file));

		$sql = "REPLACE INTO category_to_document set category_id = '79183606', document_id = '" . $d->get_id() . "'";
                $d->_db->Execute($sql);
		echo $d->toString();
		echo "loop:" . $loop . "<br/>";
		$loop++;
		}
                }
	}*/
}
?>
