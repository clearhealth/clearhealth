<?php

class refReportingLinkBuilder
{
	/**
	 * @access private
	 */
	var $_nameMapping = array(
		'requested' => 1,
		'canceled' => 8,
		'apptPending' => 3,
		'eligPending' => 2,
		'apptConfirmed' => 4
	);
	
	
	/**
	 * Returns a link into the refReporting page.
	 *
	 * @param  string  Either: requested, canceled, apptPending, eligPending, 
	 *                         apptConfirmed
	 * @return string
	 */
	function link($name) {
		$timestamp = $this->_determineLatestTimestamp($name);
		return Celini::link('list', 'refreporting', 'main') . $timestamp . '#' . $name;
	}
	
	
	/**
	 * Returns a boolean value as to whether a given refReporting link is to an
	 * empty report.
	 *
	 * @param  string  Either: requested, canceled, apptPending, eligPending, 
	 *                         apptConfirmed
	 * @return boolean
	 */
	function isEmpty($name) {
		$sql = 'SELECT COUNT(*) AS total FROM refRequest AS r WHERE refStatus = ' . $this->_nameMapping[$name];
		$db =& new clniDB();
		$result = $db->execute($sql);
		return ($result->fields['total'] <= 0);
	}
	
	
	/**
	 * Returns the timestamp of the latest entry in a given DS/grid.
	 *
	 * @param  string
	 * @access private
	 */
	function _determineLatestTimestamp($name) {
		$sql = '
			SELECT 
				UNIX_TIMESTAMP(`date`) AS \'value\'
			FROM
				refRequest AS r
			WHERE
				refStatus = ' . $this->_nameMapping[$name] . '
			ORDER BY `date` DESC LIMIT 1';
		$db =& new clniDB();
		$result =& $db->execute($sql);
		return $result->fields['value'];
	}
}

