<?php
class C_Session extends Controller {

	function get($value,$default=false,$parent=NULL) {
		if (is_null($parent)) $parent = $_SESSION;
                $vars = split('//',$value);
                if (count($vars) > 0 && !empty($vars[1])) {
				if (is_object($parent)) { $parent=$parent->$vars[0];}
				else {
                                $parent = $parent[$vars[0]];
				}
                                array_shift($vars);
                                return C_Session::get(implode("//",$vars),$default,$parent);
                }
                elseif (is_array($parent) && isset($parent[$value]))  {
                        return $parent[$value];
                }
		elseif (is_object($parent) && isset($parent->$value)) {
                        return $parent->$value;
		}
                return $default;
	}
	
	/*function actionTest($arg="") {
		var_dump( $this->get("frame//me//_objects//user//id"));
	}*/

}
?>
