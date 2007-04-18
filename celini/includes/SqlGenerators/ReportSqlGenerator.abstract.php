<?php
/**
 * Provides the interface for ReportSqlGenerators
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @todo move into phreporting module
 * @abstract
 */
class ReportSqlGenerator
{
	/**
	 * @param  array
	 * @return string
	 */
	function sql($parameters = array()) {
		die(get_class($this) . ' did not properly implement the ReportSqlGenerator');
	}
}
