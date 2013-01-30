<?php
/*****************************************************************************
*       NQF0038.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class NQF0038 extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0038: gov.cms.nqf.0038 (Alt Core - 3)
	 * Title: Childhood Immunization Status
	 * Description: Percentage of children 2 years of age who had four diphtheria, tetanus and acellular pertussis (DTaP); three polio(IPV), one measles, ,mumps and rubella (MMR); two H influenza type B (HiB); three hepatitis B (Hep B); one chicken pox (VZV); four pneumococcal conjugate (PCV); two hepatitis A (Hep A); two or three rotavirus (RV); and two influenza (flu) vaccines by their second birthday. The measure calculates a rate for each vaccine and nine separate combination rates.
	 * Jay's comments: Just identify the CVX codes for those, I think they are in procedureCodesImmunzation. It is true if they have has all of those in sufficient numbers or false if they have not.
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 1) AND
			((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) <= 2)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";

		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			WHERE {$initialPopulation}";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$denominator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$denominator[$row['patientId']] = $row;
		}

		$numerators = array();
		$i = 0;
		$encephalopathyDiagCodes = array(
			// ICD9 encephalopathy
			'323.51',
			// SNOMED-CT encephalopathy
			'230354009', '81308009',
		);
		$pndDiagCodes = array(
			// SNOMED-CT progressive neurological disorder
			230363006, 292925004, 292927007, 292992006
		);
		$diagCodeList = $this->_formatCodeList(array_merge($encephalopathyDiagCodes,$pndDiagCodes));
		$lookupTables = array(
			array(
				'join'=>'INNER JOIN patientAllergies ON patientAllergies.patientId = patient.person_id',
				'where'=>"patientAllergies.causativeAgent NOT LIKE 'DTaP%'",
			),
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code NOT IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code NOT IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$diagCodeList['generic'])."))",
			),
		);
		// Numerator 1
		$numerators[$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'DTaP%' AND {$lookup['where']}
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 3";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		// Numerator 2
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization = 'IPV' AND
				(
					patientAllergies.causativeAgent != 'IPV' OR
					patientAllergies.causativeAgent NOT LIKE 'neomycin%' OR
					patientAllergies.causativeAgent NOT LIKE 'streptomycin%' OR
					patientAllergies.causativeAgent NOT LIKE 'polymyxin%'
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 2";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerators[++$i] = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerators[$i][$row['patientId']] = $row;
		}

		$clohtDiagCodes = array(
			// ICD9 active/inactive: cancer of lymphoreticular or histiocytic tissue
			'201', '202', '203',
			// SNOMED-CT active/inactive: cancer of lymphoreticular or histiocytic tissue
			'109962001', '109964000', '109965004', '109966003', '109967007', '109968002', '109969005',
			'109970006', '109971005', '109972003', '109975001', '109976000', '109977009', '109978004',
			'109979007', '109982002', '109984001', '109989006', '110006004', '110007008', '118599009',
			'118600007', '118601006', '118602004', '118605002', '118606001', '118607005', '118608000',
			'118609008', '118610003', '118611004', '118617000', '118618005', '123313007', '127220001',
			'128874001', '128875000', '13048006', '15861008', '186723002', '188487008', '188488003',
			'188489006', '188492005', '188493000', '188498009', '188499001', '188500005', '188501009',
			'188502002', '188503007', '188504001', '188505000', '188506004', '188507008', '188509006',
			'188510001', '188511002', '188512009', '188513004', '188514005', '188515006', '188516007',
			'188517003', '188524002', '188526000', '188529007', '188531003', '188533000', '188534006',
			'188536008', '188537004', '188538009', '188541000', '188543002', '188547001', '188548006',
			'188551004', '188554007', '188558005', '188559002', '188561006', '188562004', '188565002',
			'188566001', '188567005', '188568000', '188569008', '188570009', '188572001', '188575004',
			'188576003', '188577007', '188578002', '188579005', '188580008', '188582000', '188585003',
			'188586002', '188587006', '188589009', '188590000', '188591001', '188592008', '188593003',
			'188606007', '188608008', '188609000', '188612002', '188613007', '188627002', '188630009',
			'188631008', '188632001', '188633006', '188634000', '188635004', '188637007', '188672005',
			'188674006', '188675007', '188676008', '188679001', '188680003', '188685008', '188718006',
			'188727007', '20224008', '20447006', '232075002', '236513009', '237865009', '240531002',
			'25050002', '254792006', '255101006', '255102004', '269476000', '274905008', '276811008',
			'276815004', '276836002', '277577000', '277579002', '277580004', '277606000', '277608004',
			'277609007', '277610002', '277611003', '277612005', '277613000', '277614006', '277615007',
			'277616008', '277617004', '277618009', '277622004', '277623009', '277624003', '277625002',
			'277626001', '277627005', '277628000', '277629008', '277632006', '277637000', '277641001',
			'277642008', '277643003', '277651000', '277653002', '277654008', '277664004', '278051002',
			'278052009', '285420006', '285421005', '285422003', '285423008', '285424002', '285426000',
			'285428004', '285430002', '285776004', '302841002', '302842009', '302845006', '302848008',
			'303017006', '303055001', '303056000', '303057009', '307622006', '307623001', '307624007',
			'307625008', '307633009', '307634003', '307635002', '307636001', '307637005', '307646004',
			'307647008', '307649006', '308121000', '31047003', '313427003', '371134001', '373168002',
			'400001003', '400122007', '402881008', '402882001', '404103007', '404104001', '404105000',
			'404106004', '404107008', '404108003', '404109006', '404111002', '404112009', '404113004',
			'404114005', '404115006', '404116007', '404117003', '404119000', '404120006', '404125001',
			'404126000', '404128004', '404130002', '404131003', '404132005', '404133000', '404134006',
			'404135007', '404136008', '404137004', '404138009', '404140004', '404141000', '404142007',
			'404143002', '404144008', '404145009', '404146005', '404147001', '404148006', '404150003',
			'404157000', '413537009', '413587002', '414166008', '414553000', '414780005', '414785000',
			'415110002', '415111003', '415112005', '41615007', '420302007', '420519005', '420788006',
			'421283008', '421418009', '421696004', '421835000', '422052002', '422172005', '422853008',
			'425657001', '426071002', '426248008', '426336007', '426370008', '426885008', '427141003',
			'440422002', '441313008', '441559006', '441962003', '442537007', '51056004', '58961005',
			'60620005', '61493004', '68979007', '92508006', '92512000', '92514004', '92515003',
			'92516002', '93191005', '93192003', '93193008', '93194002', '93195001', '93196000',
			'93197009', '93198004', '93199007', '93487009', '93488004', '93489007', '93492006',
			'93493001', '93494007', '93495008', '93496009', '93497000', '93498005', '93500006',
			'93501005', '93505001', '93506000', '93507009', '93509007', '93510002', '93514006',
			'93515007', '93516008', '93518009', '93519001', '93520007', '93521006', '93522004',
			'93523009', '93524003', '93525002', '93526001', '93527005', '93528000', '93530003',
			'93531004', '93532006', '93533001', '93534007', '93536009', '93537000', '93541001',
			'93542008', '93543003', '93545005', '93546006', '93547002', '93548007', '93549004',
			'93550004', '93551000', '93552007', '93554008', '93555009', '94686001', '94687005',
			'94688000', '94690004', '94704006', '94707004', '94708009', '94709001', '94710006',
			'94711005', '94712003', '94714002', '94715001', '95186006', '95187002', '95188007',
			'95192000', '95193005', '95194004', '95224004', '95225003', '95226002', '95230004',
			'95231000', '95260009', '95261008', '95263006', '95264000', 
		);
		$asymptomaticHIVDiagCodes = array(
			// ICD9 asymptomatic HIV
			'042', 'V08',
			// SNOMED-CT asymptomatic HIV
			'91947003', '91948008',
		);
		$multipleMyelomaDiagCodes = array(
			// ICD9 multiple myeloma
			'203',
			// SNOMED-CT multiple myeloma
			'109989006', '277579002', '277580004', '285420006', '285421005', '285422003', '313427003',
			'413587002', '414553000', '425657001', '440422002', '441313008', '94704006', '95209008',
			'95210003',
		);
		$leukemiaDiagCodes = array(
			// ICD9 leukemia
			'200', '202', '204', '205', '206', '207', '208',
			// SNOMED-CT leukemia
			'109991003', '110002002', '110004001', '110005000', '118613001', '127225006', '188645002',
			'188648000', '188649008', '188651007', '188725004', '188726003', '188728002', '188729005',
			'188732008', '188733003', '188734009', '188736006', '188737002', '188738007', '188740002',
			'188741003', '188744006', '188745007', '188746008', '188748009', '188754005', '188762002',
			'188768003', '188770007', '190030009', '269475001', '277473004', '277474005', '277545003',
			'277549009', '277550009', '277551008', '277567002', '277568007', '277569004', '277570003',
			'277571004', '277572006', '277573001', '277574007', '277575008', '277587001', '277589003',
			'277601005', '277602003', '277604002', '277619001', '278189009', '278453007', '285769009',
			'285839005', '302855005', '302856006', '307341004', '307592006', '307617006', '359631009',
			'359640008', '359648001', '371012000', '372087000', '404122003', '404123008', '404124002',
			'404139001', '404151004', '404152006', '404153001', '404154007', '404155008', '413389003',
			'413441006', '413442004', '413656006', '413842007', '413843002', '415287001', '425688002',
			'425749006', '425869007', '425941003', '426124006', '426217000', '426642002', '427056005',
			'427642009', '427658007', '430338009', '91854005', '91855006', '91856007', '91857003',
			'91858008', '91860005', '91861009', '92811003', '92812005', '92813000', '92814006',
			'92817004', '92818009', '93142004', '93143009', '93144003', '93145002', '93146001',
			'93147005', '93148000', '93149008', '93150008', '93151007', '93152000', '93169003',
			'93451002', '94148006', '94716000', '94718004', '94719007', '95209008', '95210003', 
		);
		$immunodeficiencyDiagCodes = array(
			// ICD9 immunodeficiency
			'279',
			// SNOMED-CT immunodeficiency
			'103077004', '103078009', '103079001', '103080003', '103081004', '105601003', '105602005',
			'111396008', '111584000', '111585004', '111587007', '116133005', '127067009', '129639005',
			'129641006', '129642004', '129643009', '13263004', '14333004', '17182001', '18827005',
			'190979003', '190980000', '190981001', '190993005', '190995003', '190996002', '190997006',
			'190998001', '191001007', '191002000', '191008001', '191018006', '191338000', '191345000',
			'191347008', '191352003', '203592006', '21527007', '22406001', '23238000', '234416002',
			'234423001', '234424007', '234425008', '234426009', '234433009', '234436001', '234437005',
			'234532001', '234533006', '234534000', '234539005', '234540007', '234541006', '234542004',
			'234543009', '234544003', '234546001', '234547005', '234548000', '234549008', '234550008',
			'234551007', '234552000', '234553005', '234554004', '234555003', '234556002', '234557006',
			'234558001', '234559009', '234560004', '234561000', '234562007', '234563002', '234564008',
			'234565009', '234566005', '234570002', '234571003', '234572005', '234573000', '234574006',
			'234576008', '234577004', '234578009', '234579001', '234580003', '234581004', '234582006',
			'234583001', '234584007', '234585008', '234586009', '234587000', '234588005', '234589002',
			'234590006', '234591005', '234593008', '234594002', '234595001', '234596000', '234597009',
			'234598004', '234599007', '234600005', '234601009', '234602002', '234603007', '234604001',
			'234605000', '234607008', '234608003', '234609006', '234611002', '234612009', '234613004',
			'234614005', '234615006', '234616007', '234617003', '234618008', '234619000', '234620006',
			'234621005', '234622003', '234623008', '234624002', '234625001', '234626000', '234627009',
			'234628004', '234629007', '234630002', '234631003', '234632005', '234633000', '234634006',
			'234635007', '234636008', '234637004', '234638009', '234639001', '234640004', '234641000',
			'234642007', '234643002', '234645009', '24181002', '241955009', '24419001', '24743004',
			'247860002', '248693006', '24974008', '25109007', '26252007', '263661007', '267538002',
			'267540007', '267543009', '276628009', '29260007', '29272001', '302874002', '303011007',
			'31323000', '32092008', '33286000', '3439009', '350353007', '351287008', '359791000',
			'36070007', '36138009', '362993009', '363009005', '363040003', '36980009', '37548006',
			'387759001', '3902000', '39674000', '398250003', '40197009', '402483002', '402791005',
			'402792003', '403837005', '409089005', '414850009', '416729007', '417167007', '41814009',
			'421312009', '425229001', '442459007', '4434006', '44940001', '45390000', '45841007',
			'46359005', '47144000', '47318007', '48119005', '49555001', '50926003', '55444004',
			'55602000', '56918001', '58034007', '58606001', '60743005', '62479008', '63484008',
			'65623009', '65880007', '66876008', '68504005', '69624006', '70349007', '71436005',
			'71610005', '71904008', '72050006', '76243000', '77070006', '77121009', '77330006',
			'77358003', '78378009', '7990002', '80369006', '81166004', '82286005', '82317007',
			'82966003', '88714009', '89454001', '89655007', '9893005', 
		);
		$comboDiagCodeList = $this->_formatCodeList(array_merge(
			$clohtDiagCodes,$asymptomaticHIVDiagCodes,$multipleMyelomaDiagCodes,
			$leukemiaDiagCodes,$immunodeficiencyDiagCodes
		));

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN patientAllergies ON patientAllergies.patientId = patient.person_id',
				'where'=>"patientAllergies.causativeAgent != 'MMR'",
			),
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code NOT IN (".implode(',',$comboDiagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code NOT IN (".implode(',',$comboDiagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$comboDiagCodeList['generic'])."))",
			),
		);
		// Numerator 3
		$numerators[++$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization = 'MMR' AND {$lookup['where']}
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 0";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		// Numerator 4
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'HiB%' AND
				(
					patientAllergies.causativeAgent NOT LIKE 'HiB%'
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerators[++$i] = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerators[$i][$row['patientId']] = $row;
		}

		$hepatitisBDiagCodes = array(
			// ICD9 resolved
			'070.2', '070.3', 'V02.61',
			// SNOMED-CT resolved
			'1116000', '111891008', '13265006', '186623005', '186624004', '186626002', '186639003',
			'235864009', '235869004', '26206000', '29062009', '3738000', '38662009', '424099008',
			'424340000', '424460009', '442134007', '442374005', '50167007', '53425008', '60498001',
			'61977001', '66071002', '76795007',
		);
		$diagCodeList = $this->_formatCodeList($hepatitisBDiagCodes);

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$diagCodeList['code']).")"
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$diagCodeList['code']).")"
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagCodeList['generic'])."))"
			),
		);
		// Numerator 5
		$numerators[++$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'Hep B%' OR
				(
					{$lookup['where']}
				) AND
				(
					patientAllergies.causativeAgent NOT LIKE 'Hep B%' OR
					patientAllergies.causativeAgent NOT LIKE 'Baker''s yeast%'
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 2";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		$diagCodeList = $this->_formatCodeList(array(
			// ICD9 VZV resolve
			052, 053
		));

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>array(
					"problemLists.code IN (".implode(',',$diagCodeList['code']).")",
					"problemLists.code NOT IN (".implode(',',$comboDiagCodeList['code']).")"
				),
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>array(
					"patientDiagnosis.code IN (".implode(',',$diagCodeList['code']).")",
					"patientDiagnosis.code NOT IN (".implode(',',$comboDiagCodeList['code']).")"
				),
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>array(
					"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagCodeList['generic'])."))",
					"(genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$comboDiagCodeList['generic'])."))"
				),
			),
		);
		// Numerator 6
		$numerators[++$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization = 'VZV' OR
				(
					(
						{$lookup['where'][0]}
					) AND
					(
						patientAllergies.causativeAgent != 'VZV' OR
						{$lookup['where'][1]}
					)
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 0";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		$diagCodeList = $this->_formatCodeList(array());
		// Numerator 7
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'pneumococcal%' AND
				(
					patientAllergies.causativeAgent NOT LIKE 'pneumococcal%'
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 3";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerators[++$i] = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerators[$i][$row['patientId']] = $row;
		}

		$diagCodeList = $this->_formatCodeList(array(
			// ICD9 Hepatitis A resolved
			'070.0', '070.1',
		));

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagCodeList['generic'])."))",
			),
		);
		// Numerator 8
		$numerators[++$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'Hep A%' OR
				(
					(
						{$lookup['where']}
					) AND
					(
						patientAllergies.causativeAgent NOT LIKE 'Hep A%'
					)
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 1";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		// Numerator 9
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN patientAllergies ON patientAllergies.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'rotavirus%' AND
				(
					patientAllergies.causativeAgent NOT LIKE 'rotavirus%'
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerators[++$i] = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerators[$i][$row['patientId']] = $row;
		}

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN patientAllergies ON patientAllergies.patientId = patient.person_id',
				'where'=>"patientAllergies.causativeAgent NOT LIKE 'influenza%'",
			),
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code NOT IN (".implode(',',$comboDiagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code NOT IN (".implode(',',$comboDiagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$comboDiagCodeList['generic'])."))",
			),
		);
		// Numerator 10
		$numerators[++$i] = array();
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(patientImmunizations.patientId) AS immunizationCount
			FROM patientImmunizations
			INNER JOIN patient ON patient.person_id = patientImmunizations.patientId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			{$lookup['join']}
			WHERE {$initialPopulation} AND
				patientImmunizations.immunization LIKE 'influenza%' AND
				(
					{$lookup['where']}
				)
			GROUP BY patientImmunizations.patientId
			HAVING immunizationCount > 1";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerators[$i][$row['patientId']] = $row;
			}
		}

		// Numerator 11 - combination of numerators 1,2,3,6,5
		$numerators[++$i] = array_intersect_key($numerators[0],$numerators[1],$numerators[2],$numerators[5],$numerators[4]);

		// Numerator 12 - combination of numerators 1,2,3,6,5,7
		$numerators[++$i] = array_intersect_key($numerators[0],$numerators[1],$numerators[2],$numerators[5],$numerators[4],$numerators[6]);

		$dctr = count($denominator);
		$ret = array('D: '.$dctr);

		foreach ($numerators as $key=>$value) {
			$nctr = count($value);
			$percentage = self::calculatePerformanceMeasure($dctr,$nctr);
			$i = $key + 1;
			self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr,'percentage'=>$percentage);
			$ret[] = 'N'.$i.': '.$nctr.' P'.$i.': '.$percentage;
		}
		return implode("<br/>\n",$ret);
	}

}
