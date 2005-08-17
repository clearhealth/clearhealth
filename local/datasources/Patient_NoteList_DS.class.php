<?php

class Patient_NoteList_DS extends Datasource_sql
{
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Patient_NoteList_DS';
	
	
	function Patient_NoteList_DS($patient_id) {
		settype($patient_id, 'integer');
		
		$labels = array(
			'deprecated' => '<span title="Deprecated">Dep</span>',
			'priority'   => 'P',
			'note_date'  => 'Date',
			'username'   => 'User',
			'note'       => 'Note');

		$this->setup(Cellini::dbInstance(),
			array(
				'cols' 	=> "priority, DATE_FORMAT(note_date, '%m/%d/%Y %H:%i:%s') AS note_date, note, username, patient_note_id, if(deprecated,'Yes','No') deprecated",
				'from' 	=> "patient_note n left join user u on u.user_id = n.user_id",
				'where' => " patient_id = $patient_id",
				'orderby' => "deprecated ASC",
			),
			$labels
		);

		$this->addOrderRule('priority',  'DESC', 0);
		$this->addOrderRule('note_date', 'DESC', 1);

		$this->registerFilter('note',     array($this, 'multiLineFilter'));
		$this->registerFilter('priority', array($this, 'colorLineFilter'));
		//$this->template['deprecated'] = "<a href='".Cellini::managerLink('depnote',$patient_id)."pnote_id={\$patient_note_id}&current={\$deprecated}&process=true'>{\$deprecated}</a>";
	}
	
	
	function multiLineFilter($content) {
		if (strstr($content,"\n")) {
			$pos = strpos($content,"\n");
			$line1 = trim(substr($content,0,$pos));
			$rest = trim(substr($content,($pos+1)));
			return "<pre><span style='border-bottom: dotted 1px blue;' onmouseover=\"this.parentNode.getElementsByTagName('div').item(0).style.display = 'block'; this.style.borderBottom = '';\">$line1</span><div style='display:none;'>$rest</div></pre>";
		}
		return $content;
	}

	function colorLineFilter($content) {
		switch($content) {
			case 5:
				$color = "red";
				break;
			case 4: 
				$color = "yellow";
				break;
			default:
				$color = "transparent";
				break;
		}

		return "<div style='background-color: $color; font-weight: bold; margin-left: -5px; text-align:  center;'>$content</div>";
	}
}

