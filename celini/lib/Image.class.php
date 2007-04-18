<?php

class Image {
	
	var $imagefile = null;
	var $type = null;
	var $image = null;
	var $width = null;
	var $height = null;
	var $cache_dir = null;
	var $type = "PNG";
	var $render_img = null;
	var $render_img_file = null;
	var $render_type = null;
	var $render_width = null;
	var $render_height = null;
	
	
	function Image($imagefile, $width=null, $height=null, $proportion = false) {
		$this->imagefile = realpath($imagefile);
		$this->cache_dir = realpath(dirname(__FILE__) . "/../tmp");
		if ($width != null && $height !=null && file_exists($this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile))) {
        	$this->render_img_file = $this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile);
        	return;
        }
        
		if (file_exists($this->imagefile)) {
			switch(substr($this->imagefile,-4)) {
				case '.jpg':
					$this->type = "jpeg";
					break;
				case '.png':
					$this->type = "png";
					break;
			}
			$this->render_type = $this->type;
			$func_name = "imagecreatefrom" . $this->type;
			$this->image = $this->image = $func_name($this->imagefile);	
		}
		$img_info = getimagesize($this->imagefile);
		$this->width = $img_info[0];
		$this->height = $img_info[1];
		if ($width != null && $height != null) {
			$this->resize($width,$height,$proportion);	
		}
		
	}	
	
    function resize($width,$height, $proportion=false) {
        $this->render_height = $height;
        $this->render_width = $width;
        //fix proportions
        if ($proportion) {
        	if ($this->width > $this->height) {
        		$factor = $this->height / $this->width;
        		$this->render_width = $width;
        		$this->render_height = $height * $factor;	
        	}
        	else {
        		$factor = $this->width / $this->height;
        		$this->render_height = $height;
        		$this->render_width = $width * $factor;
        	}
        }
        //echo "fac: " . $factor . " w: " . $this->render_width . " h: " . $this->render_height . " wo: "  . $this->width . " ho: " . $this->height . "<br>";
        if (file_exists($this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile))) {
        	$this->render_img_file = $this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile);
        	return true;
        }
        //echo "nwidth: $width nheight: $height owidth: " . $this->width . " oheight:" . $this->height . "<br>";
        //echo "funcname: " . $func_name . "<br>";
        $this->render_img = imagecreatetruecolor($this->render_width,$this->render_height);
        $bg_color = imagecolorallocate($this->render_img, 255, 255, 255);
        imagefilledrectangle($this->render_img, 0, 0, $this->render_width, $this->render_height, $bg_color);
        
        //echo $width ."--" . $height . "--" . $this->width . "--" . $this->height;
        imagecopyresampled($this->render_img,$this->image,0,0,0,0,$this->render_width,$this->render_height,$this->width,$this->height);
        $func_name = "image" . $this->type;
        
        $func_name($this->render_img, $this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile));
        $this->render_img_file = $this->cache_dir . "/" . $width . "x" . $height . "-" . basename($this->imagefile);
    }
    
    function get_image_file() {
    	return $this->render_img_file;
    }
    
    function destroy() {
    	@imagedestroy($this->image);
    	@imagedestroy($this->render_img);	
    }

    
	
}

?>
