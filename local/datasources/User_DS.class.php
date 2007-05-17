<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * A datasource of all of the users of the system.
 */
class User_DS extends Datasource_sql 
{
	/**#@+
	 * @access private
	 */
	var $_internalName = 'User_DS';
	var $_type = 'html';
	var $_typeCache = array();	
	/**##@-*/
	
	/**
	 * Handle instantiation
	 */
	function User_DS() {
		$db =& Celini::dbInstance();
		$this->setup(
			$db,
			array(
				'cols' => '
					p.person_id,
					p.last_name,
					p.first_name,
					pt.person_type,
					IF(p.inactive = 0, "Yes", "No") AS active,
					u.username, u.user_id',
				'from' => '
					person AS p
					INNER JOIN person_type AS pt USING(person_id)
					INNER JOIN user AS u USING(person_id)'
				//'orderby' => 'p.last_name, p.first_name'
			),
			array(
				'last_name' => 'Last Name',
				'first_name' => 'First Name',
				'person_type' => 'Type',
				'username' => 'Username',
				'active' => 'Active'
			)
		);
		
		$this->registerFilter('person_type',array(&$this, '_lookupType'));
		$this->registerFilter('last_name', array(&$this, '_actionEditLink'));
	}
	
	
	/**
	 * Looks up person type enum value
	 *
	 * @param  string
	 * @return string
	 * @access private
	 */
	function _lookupType($value) {
		if (!isset($this->_typeCache[$value])) {
			$em =& Celini::enumManagerInstance();
			$this->_typeCache[$value] = $em->lookup('person_type', $value);
		}
		return $this->_typeCache[$value];
	}
	
	
	/**
	 * Formats link to edit a given user
	 *
	 * @param  string
	 * @return return
	 * @access private
	 */
	function _actionEditLink($value, $rowValues) {
		$url = Celini::link('edit', 'User') . 'id=' . $rowValues['person_id'];
		return '<a href="' . $url . '">' . $value . '</a>';
	}
}

?>
