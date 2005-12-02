<?php
$loader->requireOnce('/lib/PEAR/HTML/AJAX/Serializer/JSON.php');

/**
 * Class the defines the default enumeration type
 */
class EnumType_PerPractice extends EnumType_Default {

	/**
	 * Sql table the data is stored in, also the name of the ordo used for updating
	 */
	var $table = 'enumeration_value';

	/**
	 * Name of the ORDO used to update
	 */
	var $ordo = 'EnumerationValue';

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>15),
				'extra1' => false,
				'extra2' => false,
				'sort' => array('label'=>'Order','type'=>'order'),
				'status' => array('label'=>'Enabled?','type'=>'boolean')
			);
	
	var $practiceId = -1;
	var $editingPracticeId = -1;
	var $editing = false;
	var $enumerationId = false;

	function EnumType_PerPractice() {
		$this->practiceId = $_SESSION['defaultpractice'];
		if (isset($_GET['practiceId'])) {
			$this->editingPracticeId = EnforceType::int($_GET['practiceId']);

			$enumerationId = EnforceType::int($_GET[0]);

			if (isset($_GET['copy'])) {
				$db = new clniDB();
				$sql = "select * from {$this->table} ev inner join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id where ev.enumeration_id = $enumerationId and evp.practice_id = $this->editingPracticeId order by sort, ev.enumeration_value_id";
				$res = $db->execute($sql);
				// only do a copy action if there are now ev entries
				if ($res->EOF) {
					$sql = "select ev.enumeration_value_id from enumeration_value ev left join enumeration_value_practice evp using(enumeration_value_id) where ev.enumeration_id = $enumerationId and evp.enumeration_value_id is null";
					$res = $db->execute($sql);
					if ($_GET['copy'] === 'true') {
						while($res && !$res->EOF) {
							$ev =& Celini::newOrdo('EnumerationValue',$res->fields['enumeration_value_id']);
							$ev->set('enumeration_value_id',0);
							$ev->persist();

							$evp =& Celini::newOrdo('EnumerationValuePractice');
							$evp->set('practice_id',$this->editingPracticeId);
							$evp->set('enumeration_value_id',$ev->get('id'));
							$evp->persist();

							$res->MoveNext();
						}
					}
					else {
						$ev =& Celini::newOrdo('EnumerationValue');
						$ev->set('enumeration_id',$enumerationId);
						$ev->persist();

						$evp =& Celini::newOrdo('EnumerationValuePractice');
						$evp->set('practice_id',$this->editingPracticeId);
						$evp->set('enumeration_value_id',$ev->get('id'));
						$evp->persist();
					}
				}
			}
		}
	}

	/**
	 * Get an array of enum data
	 *
	 * @param  int $enumerationId
	 * @return array
	 */
	function enumData($enumerationId) {
		EnforceType::int($enumerationId);
		$this->enumerationId = $enumerationId;
		
		$practiceId = EnforceType::int($this->practiceId);
		if ($this->editing) {
			$practiceId = EnforceType::int($this->editingPracticeId);
		}

		$sql = "select * from {$this->table} ev inner join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id where ev.enumeration_id = $enumerationId and evp.practice_id = $practiceId order by sort, ev.enumeration_value_id";

		if ($practiceId === -1) {
			$sql = "select * from {$this->table} ev left join enumeration_value_practice evp using(enumeration_value_id) where ev.enumeration_id = $enumerationId and evp.enumeration_value_id is null order by sort, ev.enumeration_value_id";
		}
		$db = new clniDB();
		$res = $db->execute($sql);

		if (!$this->editing) {
			if ($res->EOF) {
				$sql = "select * from {$this->table} ev left join enumeration_value_practice evp using(enumeration_value_id) where ev.enumeration_id = $enumerationId and evp.enumeration_value_id is null order by sort, ev.enumeration_value_id";
				$res = $db->execute($sql);
			}
		}


		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->moveNext();
		}
		return $ret;
	}

	/**
	 * Update an enum value with an array of data
	 *
	 * @param	array	$data
	 */
	function update($data) {
		$id = 0;
		if (isset($data['enumeration_value_id'])) {
			$id = $data['enumeration_value_id'];
		}
		$ev =& ORDataObject::Factory($this->ordo,$id);
		$ev->populate_array($data);
		$ev->persist();
	}

	function widget() {
		ORDataObject::factory_include('Practice');
		$practices = Practice::practices_factory();
		$enumerationId = $this->enumerationId;

		$db = new clniDB();
		$sql = "select practice_id from enumeration_value_practice evp inner join enumeration_value ev on evp.enumeration_value_id = ev.enumeration_value_id where enumeration_id = $enumerationId";
		$res = $db->execute($sql);

		$list = array(-1=>-1);
		while($res && !$res->EOF) {
			$list[$res->fields['practice_id']] = $res->fields['practice_id'];
			$res->MoveNext();
		}

		$json = new HTML_AJAX_Serializer_JSON();

		$practiceId = $this->editingPracticeId;

		$selected = "";
		if ($practiceId === false) {
			$selected = ' selected';
		}
		$ret = '
		<script type="text/javascript">
		function selectPractice(select) {
			var inited = '.$json->serialize($list).';

			var copy = 0;
			if (!inited[select.value]) {
				copy = confirm("The practice \""+select.options[select.selectedIndex].text+"\" has no custom enumeration values, would you like to copy the default values to it?\n(OK will select the practice copying the values, Cancel will select the practice WITHOUT copying the values.)");
			}
			window.location = "'.Celini::link('edit',true,true,$this->enumerationId).'practiceId="+select.value+"&copy="+copy;
		}
		</script>';
		$ret .= "<div>Select a Practice to edit Enums for: <select name='practiceId' onchange='return selectPractice(this);'>"
				."<option value='-1' $selected>Default</option>";
		foreach($practices as $practice) {
			$selected = "";
			if ($practice->get('id') == $practiceId) {
				$selected = ' selected';
			}
			$ret .= '<option value="'.$practice->get('id').'"'.$selected.'>'.$practice->get('name').'</option>';
		}
		$ret .= "</select></div>";
		return $ret;
	}
}
?>
