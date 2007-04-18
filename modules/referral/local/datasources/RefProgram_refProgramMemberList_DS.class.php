<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Creates a list
 *
 * First letter is capitalized for {@link DatasourceFileLoader}
 */
class RefProgram_refProgramMemberList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'RefProgram_refProgramMemberList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	
	/**
	 * Handle initialization of DS
	 */
	function RefProgram_refProgramMemberList_DS($refprogram_id) {
		settype($refprogram_id,'int');

		$provider =& Celini::newORDO('refProvider');
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => '
					IF(
						prac.name IS NULL,
						' . $provider->fullNameSQL() . ',
						prac.name) AS name
				',
				'from' => '
					refprogram AS prog
					INNER JOIN refprogram_member AS tie USING(refprogram_id)
					LEFT JOIN refpractice AS prac ON(tie.external_id = prac.refpractice_id) 
					LEFT JOIN refprovider AS prov ON(tie.external_id = prov.refprovider_id)',
				'where' => 
					'prog.refprogram_id = "' . $refprogram_id . '"'
			),
			array(
				'name' => 'Practice Name'));
	}
}

