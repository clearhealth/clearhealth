<?php
$loader->requireOnce('/lib/PEAR/HTML/AJAX/Serializer/JSON.php');
$loader->requireOnce('includes/chlClinicArray.class.php');

/**
 * Class the defines the default enumeration type
 */
class EnumType_FPLPerProgram extends EnumType_Default {

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
				'key' 	=> array('type' => 'hidden'), 
				'value' => array('label'=>'FPL','size'=>5),
				'extra1' => array(
					'label' => 'Visit Fee&nbsp;',
					'size'  => 5
				),
				'extra2' => array(
					'label' => 'Proc. Fee&nbsp;',
					'size'  =>5
				),
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean')
			);
	
	var $programId = -1;
	var $editingProgramId = -1;
	var $editing = false;
	var $enumerationId = false;

	function EnumType_FPLPerProgram() {
		$session =& Celini::sessionInstance();
		$get =& Celini::filteredGet();
		
		$this->programId = $session->get('referral:currentProgramId', '-1');
		if ($get->exists('editingProgramId')) {
			$this->editingProgramId = $get->getTyped('editingProgramId', 'int');
			$this->programId = $this->editingProgramId;

			$enumerationId = EnforceType::int($_GET[0]);

			if (isset($_GET['copyData'])) {
				$db = new clniDB();
				$sql = "
					SELECT
						*
					FROM
						{$this->table} AS ev 
						INNER JOIN enumeration_value_refprogram AS evp ON(ev.enumeration_value_id = evp.enumeration_value_id)
					WHERE
						ev.enumeration_id = $enumerationId AND
						evp.refprogram_id = $this->editingProgramId 
					ORDER BY
						sort,
						ev.enumeration_value_id";
				$res = $db->execute($sql);
				// only do a copy action if there are now ev entries
				if ($res->EOF) {
					$sql = "
						SELECT
							ev.enumeration_value_id 
						FROM
							enumeration_value AS ev
							LEFT JOIN enumeration_value_refprogram AS evp USING(enumeration_value_id)
						WHERE
							ev.enumeration_id = $enumerationId AND
							evp.enumeration_value_id IS NULL";
					$res = $db->execute($sql);
					if ($_GET['copyData'] === 'true') {
						while($res && !$res->EOF) {
							$ev =& Celini::newOrdo('EnumerationValue',$res->fields['enumeration_value_id']);
							$ev->set('enumeration_value_id',0);
							$ev->persist();

							$evp =& Celini::newOrdo('EnumerationValueRefProgram');
							$evp->set('refprogram_id',$this->editingProgramId);
							$evp->set('enumeration_value_id',$ev->get('id'));
							$evp->persist();

							$res->MoveNext();
						}
					}
					else {
						$ev =& Celini::newOrdo('EnumerationValue');
						$ev->set('enumeration_id',$enumerationId);
						$ev->persist();

						$evp =& Celini::newOrdo('EnumerationValueRefProgram');
						$evp->set('refprogram_id',$this->editingProgramId);
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
		$this->enumerationId = EnforceType::int($enumerationId);
		
		$programId = EnforceType::int($this->programId);
		if ($this->editing) {
			$programId = EnforceType::int($this->editingProgramId);
		}

		$sql = "
			SELECT
				*
			FROM
				{$this->table} AS ev 
				INNER JOIN enumeration_value_refprogram AS evp ON(ev.enumeration_value_id = evp.enumeration_value_id)
			WHERE
				ev.enumeration_id = $enumerationId AND
				evp.refprogram_id = $programId 
			ORDER BY
				sort,
				ev.enumeration_value_id";

		if ($programId === -1) {
			$sql = "
				SELECT
					*,
					ev.enumeration_value_id 
				FROM
					{$this->table} AS ev
					LEFT JOIN enumeration_value_refprogram AS evp USING(enumeration_value_id)
				WHERE
					ev.enumeration_id = $enumerationId AND
					evp.enumeration_value_id IS NULL
				ORDER BY
					sort,
					ev.enumeration_value_id";
		}
		$db = new clniDB();
		$res = $db->execute($sql);

		if (!$this->editing) {
			if ($res->EOF) {
				$sql = "
					SELECT
						*
					FROM
						{$this->table} AS ev
						LEFT JOIN enumeration_value_refprogram AS evp USING(enumeration_value_id)
					WHERE 
						ev.enumeration_id = $enumerationId AND 
						evp.enumeration_value_id IS NULL
					ORDER BY
						sort,
						ev.enumeration_value_id";
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
		$programId = EnforceType::int($this->editingProgramId);

		$ev =& Celini::newORDO($this->ordo,$id);
		$ev->populate_array($data);
		$ev->persist();
		
		if ($programId > 0) {
			$evp =& Celini::newOrdo('EnumerationValueRefProgram');
			$evp->set('refprogram_id',$this->editingProgramId);
			$evp->set('enumeration_value_id',$ev->get('id'));
			$evp->persist();
		}
	}

	function widget() {
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('clniConfirmLink', 'clniPopup');
		$json = new HTML_AJAX_Serializer_JSON();

		$programId = $this->editingProgramId;
		$enumerationId = $this->enumerationId;
		
		$db = new clniDB();
		$sql = "
			SELECT
				refprogram_id 
			FROM
				enumeration_value_refprogram AS evp
				INNER JOIN enumeration_value AS ev ON(evp.enumeration_value_id = ev.enumeration_value_id)
			WHERE
				enumeration_id = $enumerationId";
		$res = $db->execute($sql);

		$list = array(-1=>-1);
		while($res && !$res->EOF) {
			$list[$res->fields['refprogram_id']] = $res->fields['refprogram_id'];
			$res->MoveNext();
		}

		$selected = "";
		if ($programId === false) {
			$selected = ' selected="false"';
		}
		$ret = '
		<div style="display:none;" id="addNewRefProgramEnum">
			<p><strong>This program doesn\'t have a a program-specific FPL table</strong></p>
			
			<p>
				Would you like to copy the default <abbr title="Federal Poverty Level">FPL</abbr>
				values into a program-specific FPL?</p>
		
			<ul class="menu centered">
				<li><a href="javascript:confirmLinkManager.submit()" onclick="confirmLinkManager._linkObj = confirmLinkManager._linkObj + \'true\'"><p>Yes</p>Populate the FPL table with the default values.</a></li>
				<li><a href="javascript:confirmLinkManager.submit()" onclick="confirmLinkManager._linkObj = confirmLinkManager._linkObj + \'0\'"><p>No</p>Create a blank FPL table.</a></li>
				<li><a href="javascript:confirmLinkManager.cancel()"><p>Cancel</p>Do not create a program-specific FPL table.</a></li>
			</ul>
		</div>
		<script type="text/javascript">
		var confirmLinkManager = new clniConfirmLink();
		function selectProgram(select) {
			var inited = '.$json->serialize($list).';

			var copy = 0;
			if (!inited[select.value]) {
				link = "' . Celini::link('edit',true,true,$this->enumerationId) . 'editingProgramId="+select.value+"&copyData=";
				confirmLinkManager.confirmLink(link, "addNewRefProgramEnum");
			}
			else {
				window.location = "'.Celini::link('edit',true,true,$this->enumerationId).'editingProgramId="+select.value;
			}
		}
		</script>';
		$ret .= "<div>Select a program to edit FPL for: <select name='editingProgramId' onchange='return selectProgram(this);'>"
				."<option value='-1' $selected>Default</option>";
		
		$refProgram =& Celini::newORDO('refProgram');
		foreach ($refProgram->valueList() as $program_id => $program_name) {
			$selected = ($program_id == $programId) ? ' selected="selected"' : '';
			$ret .= '<option value="' . $program_id . '"' . $selected . '>' . $program_name . "</option>\n";
		}
		$ret .= "</select></div>";
		return $ret;
	}
}
?>
