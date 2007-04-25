<?php

/**
 * Provides the interface that all X12 readers will need to implement
 *
 * @abstract
 */
class X12Reader {
	/**
	 * Returns the contents of this reader as a string
	 *
	 * @return string
	 * @abstract
	 */
	function readContents() {
		trigger_error(get_class($this) . ' did not properly implement X12Reader');
	}
}

?>
