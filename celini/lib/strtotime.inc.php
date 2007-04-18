<?php
/**
 * Celini implementation of strtotime that supports dates before 1970 and after 2038
 *
 * @package	com.uversainc.celini
 */

/**
 * bring in adodb-time
 */
if (!function_exists('adodb_date_test_date')) {
	require_once CELINI_ROOT ."/lib/adodb/adodb-time.inc.php";
}

/** 
 * strtotime implementation that uses adodb-time to support 64bit timestamps
 */
function Celini_strtotime($string) {
	$strtotime = new CeliniStrToTime();
	$strtotime->parse($string);

	// if we successfully parsed a timestamp return it
	if ($strtotime->time) {
		return $strtotime->time;
	}
	else {
		// fallback to php strtotime
		return strtotime($string);
	}
}

class CeliniStrToTime {
	var $time = false;

	function parse($string) {
		$string = trim($string);
		$methods = get_class_methods(get_class($this));
		foreach($methods as $method) {
			if (strstr($method,'_check')) {
				if ($ret = $this->$method($string)) {
					$this->time = $ret;
					break;
				}
			}
		}
	}

	// check for today or now
	function _checkNow($string) {
		if (preg_match('/^[tod|now]/i',$string)) {
			return time();
		}
	}

	// Tomorrow
	function _checkTomorrow($string) {   
		if (preg_match('/^tom/i',$string)) {
			return time() + (60*24*24);
		}
	}
	// Yesterday
	function _checkYesterday($string) {
		if (preg_match('/^yes/i',$string)) {
			return time() - (60*24*24);
		}
        }
	// 4th
	// todo: finish me
	function _check4th($string) {
		if (preg_match('/^(\d{1,2})(st|nd|rd|th)?$/i',$string,$match)) {
		}
      //      var d = new Date();
        //    d.setDate(parseInt(bits[1], 10));
        //    return d;
	}

	// 4th Jan
	// todo: finish me
	function _check4thJan($string) {
		/*
    {   re: /^(\d{1,2})(?:st|nd|rd|th)? (\w+)$/i, 
        handler: function(bits) {
            var d = new Date();
            d.setDate(parseInt(bits[1], 10));
            d.setMonth(parseMonth(bits[2]));
            return d;*/
        }
	/*
    // 4th Jan 2003
    {   re: /^(\d{1,2})(?:st|nd|rd|th)? (\w+),? (\d{4})$/i,
        handler: function(bits) {
            var d = new Date();
            d.setDate(parseInt(bits[1], 10));
            d.setMonth(parseMonth(bits[2]));
            d.setYear(bits[3]);
            return d;
        }
    },
    // Jan 4th
    {   re: /^(\w+) (\d{1,2})(?:st|nd|rd|th)?$/i, 
        handler: function(bits) {
            var d = new Date();
            d.setDate(parseInt(bits[2], 10));
            d.setMonth(parseMonth(bits[1]));
            return d;
        }
    },
    // Jan 4th 2003
    {   re: /^(\w+) (\d{1,2})(?:st|nd|rd|th)?,? (\d{4})$/i,
        handler: function(bits) {
            var d = new Date();
            d.setDate(parseInt(bits[2], 10));
            d.setMonth(parseMonth(bits[1]));
            d.setYear(bits[3]);
            return d;
        }
    },
    // next Tuesday - this is suspect due to weird meaning of "next"
    {   re: /^next (\w+)$/i,
        handler: function(bits) {
            var d = new Date();
            var day = d.getDay();
            var newDay = parseWeekday(bits[1]);
            var addDays = newDay - day;
            if (newDay <= day) {
                addDays += 7;
            }
            d.setDate(d.getDate() + addDays);
            return d;
        }
    },
    // last Tuesday
    {   re: /^last (\w+)$/i,
        handler: function(bits) {
            throw new Error("Not yet implemented");
        }
    },*/

	// yyyy-mm-dd or yyyy/mm/dd (ISO style)
	function _checkIso($string) {
		if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/',$string,$match)) {
			// ($hr, $min, $sec, $month, $day, $year)
			return adodb_mktime(0,0,0,$match[2],$match[3],$match[1]);
		}
        }

	// mm/dd/yyyy or mm-dd-yyyy (American style)
	function _checkAmerican($string) {
		if (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/',$string,$match)) {
			// ($hr, $min, $sec, $month, $day, $year)
			return adodb_mktime(0,0,0,$match[1],$match[2],$match[3]);
		}
        }

	// mm/dd/yy or mm-dd-yy
	function _checkMMDDYY($string) {
		if (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{2})$/',$string,$match)) {
			if ($match[3] > 20) {
				$match[3] = '19' . $match[3];
			}
			else {
				$match[3] = '20' . $match[3];
			}
			// ($hr, $min, $sec, $month, $day, $year)
			return adodb_mktime(0,0,0,$match[1],$match[2],$match[3]);
		}
        }

	// mm/dd or mm-dd assume current year
	function _checkMMDD($string) {
		if (preg_match('/^(\d{1,2})[\/-](\d{1,2}$)/',$string,$match)) {
			var_dump($match);
			
		}
		/*
            var d = new Date();
	    var now = new Date();
            d.setYear(now.getFullYear());
            d.setDate(parseInt(bits[2], 10));
            d.setMonth(parseInt(bits[1], 10) - 1); // Because months indexed from 0
            return d;
	    */
        }

}
?>
