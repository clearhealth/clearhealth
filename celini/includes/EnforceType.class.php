<?php

/**
 * This object enforces that a given value is a particular a given type.
 *
 * This utilizes assert() for debugging messages.  In order to turn them on,
 * insure that assert_options(ASSERT_ACTIVE, 1) has been called prior to 
 * calling any of these methods.
 * 
 * @todo Add other types to enforce.
 */
class EnforceType
{
	/**
	 * Insures that <i>$value</i> is an integer.
	 *
	 * This returns an integer type for <i>$value</i> if it is capable of 
	 * being an integer, or 0 otherwise.
	 *
	 * The following values types will return 0: booleans, floats, arrays, and
	 * non-numeric strings.
	 *
	 * @param mixed
	 * @return int
	 */
	function int($value) {
		assert('is_int($value) || (is_numeric($value) && !is_float($value))');
		
		if (is_int($value) || (is_numeric($value) && !is_float($value))) {
			$value = (int)$value;
		}
		else {
			$value = 0;
		}
		
		return $value;
	}
	
	
	/**
	 * Takes a string, numeric value, or array and makes its contents safe for
	 * HTML output.
	 *
	 * This will return any non-string, non-numeric, or non-array values without
	 * performing any sort of escaping on them.  If ASSERT_ACTIVE is on, an 
	 * assert error will be generated if one of these values is passed in.
	 *
	 * @param  string|int|array
	 * @return string|int|array
	 */
	function htmlsafe($value) {
		assert('is_numeric($value) || is_string($value) || is_array($value)');
		
		if (is_string($value) || is_numeric($value)) {
			return htmlspecialchars($value);
		}
		elseif (is_array($value)) {
			foreach ($value as $key => $arrayValue) {
				$value[$key] = $this->htmlsafe($arrayValue);
			}
			return $value;
		}
		
		return $value;
	}
}

