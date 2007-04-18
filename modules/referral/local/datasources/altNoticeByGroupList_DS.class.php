<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * Retrieves a Celini Datasource of {@link altNotice}s that are linked to a
 * particular ACL group
 *
 * @todo update docs
 * @todo consider renaming class to better reflect current generic status
 */
class altNoticeByGroupList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'altNoticeByGroupList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	
	function altNoticeByGroupList_DS($groupName, $groupType = 'ACL Group') {
		$db =& new clniDB();
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols'  => 'altnotice_id, note,
					DATE_FORMAT(creation_date, "%m/%d/%Y") AS formatted_creation_date,
					external_type, external_id
					',
				'from'  => 'altnotice',
				'where' => 'owner_type = "' . $db->escape($groupType) . '" AND owner_id = "' . $db->escape($groupName) . '"'
			),
			array('note' => 'Note', 'formatted_creation_date' => 'Date')
		);
		
		$this->registerFilter('formatted_creation_date', array(&$this, '_addLink'));
		
		$this->orderHints['formatted_creation_date'] = 'creation_date';
		$this->addDefaultOrderRule('formatted_creation_date', 'DESC');
	}
	
	
	/**
	 * @todo determine if this, or something like it, should be put into some
	 *   sort of helper class.  It keeps resurfacing.
	 */
	function _addLink($value, $rowValues) {
		$passAlong = '';
		if (isset($_GET['u'])) {
			$passAlong = "u=$_GET[u]";
		}
		
		if ($rowValues['external_type'] == 'refrequest') {
			$rowValues['external_type'] = 'referral';
		}
		return '<a target="_top" href="'.Celini::link('view/' . $rowValues['external_id'], $rowValues['external_type'], 'main').$passAlong.'">' . $value . '</a>';
	}
}

