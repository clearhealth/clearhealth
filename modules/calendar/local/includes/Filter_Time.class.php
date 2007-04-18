<?php

$GLOBALS['loader']->requireOnce('includes/FilterBase.class.php');

class Filter_Time extends FilterBase{
	var $params = array('increment'=>1,'showseconds'=>false);

	function Filter_Time($name, $label, $value = null, $params = null){
		parent::FilterBase($name, $label, $value, $params);
	}
	
	function getHTML($options){
		$x=1;
		$value = $this->getValue();
		$out = '<div><a style="font-weight: bold;" class="filterToggle">'.$this->label.'</a><div class="filterBody"><select name="Filter['.$this->name.'][hour]">';
		while ($x < 13) {
			$out.='<option value='.$x;
			if($value['hour'] == $x) {
				$out.=' selected';
			}
			$out.='>'.$x.'</option>';
			$x++;
		}
		$out.='</select>';
		$x = 0;
		$out .= '<select name="Filter['.$this->name.'][minute]">';
		while ($x < 60) {
			$min = $x;
			if(strlen($x) == 1) {
				$min = '0'.$x;
			}
			$out.='<option value="'.$min.'"';
			if($value['minute'] == $min) {
				$out.=' selected';
			}
			$out.='>';
			$out.=$x < 10 ? '0'.$x : $x;
			$out.='</option>';
			$x+=$this->params['increment'];
		}
		$out.='</select>';
		if($this->params['showseconds'] == true) {
			$x = 0;
			$out .= '<select name="Filter['.$this->name.'][second]">';
			while ($x < 60) {
				$sec = $x;
				if(strlen($x) == 1) {
					$sec = '0'.$x;
				}
				$out.='<option value="'.$sec.'"';
				if($value['second'] == $sec) {
					$out.=' selected';
				}
				$out.='>';
				$out.=$x < 10 ? "0$x" : $x;
				$out.='</option>';
				$x+=$this->params['increment'];
			}
		} else {
			$out.='<input name="Filter['.$this->name.'][second]" value="00" type="hidden">';
		}
		$out.='<select name="Filter['.$this->name.'][ap]">';
		$out.='<option value="AM"';
		if($value['ap'] == 'AM') {
			$out.=' selected';
		}
		$out.='>AM</option>';
		$out.='<option value="PM"';
		if($value['ap'] == 'PM') {
			$out.=' selected';
		}
		$out.='>PM</option></select></div></div>';

		return $out;
	}
	
	function getSettingsHTML(){
		$date = 'Not Set';
		if(!empty($this->value)){
			$date = $this->value;
		}

		$out= "<DIV ID='filter_{$this->name}'>{$this->label}: ".$date['hour'].':'.$date['minute'];
		if($this->params['showseconds'] == true) {
			$out.=':'.$date['second'];
		}
		$out.="</DIV>";
		return $out;
	}
	
}
?>