<?php
/*****************************************************************************
*       Medication.php
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

 
class Medication extends WebVista_Model_ORM implements Document {
 
	protected $medicationId;
	protected $hipaaNDC;
	protected $type = 'OPM';
	protected $personId;
	protected $patient;
	protected $patientReported;
	protected $substitution;
	protected $dateBegan;
	protected $datePrescribed;
	protected $description;
	protected $comment;
	protected $directions;
	protected $prescriberPersonId;
	protected $provider;
	protected $quantity;
	protected $quantityQualifier;
	protected $dose;
	protected $route;
	protected $priority;
	protected $schedule;
	protected $prn;
	protected $transmit;
	protected $dateTransmitted;
	protected $pharmacyId;
	protected $pharmacy;
	protected $daysSupply;
	protected $strength;
	protected $unit;
	protected $refills;
	protected $rxnorm;
	protected $eSignatureId;
	protected $pkey;
	protected $dateDiscontinued;
	protected $refillRequestId;
	protected $_table = 'medications';
	protected $_primaryKeys = array('medicationId');
	protected $_cascadePopulate = false;
	protected $_cascadePersist = false;

	const ENUM_PARENT_NAME = 'Medication Preferences';
	const ENUM_ADMIN_SCHED = 'Administration Schedules';

	public function __construct() {
		parent::__construct();
		$this->patient = new Patient();
		$this->patient->_cascadePersist = false;
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		$this->pharmacy = new Pharmacy();
		$this->pharmacy->_cascadePersist = false;
	}

	public function populate() {
		$ret = parent::populate();
		$this->patient->populate();
		$this->provider->populate();
		$this->pharmacy->populate();
		return $ret;
	}

	public function setPersonId($value) {
		$this->personId = (int)$value;
		$this->patient->personId = $this->personId;
	}

	public function setPrescriberPersonId($value) {
		$this->prescriberPersonId = (int)$value;
		$this->provider->personId = $this->prescriberPersonId;
	}

	public function setPharmacyId($value) {
		$this->pharmacyId = $value;
		$this->pharmacy->pharmacyId = $this->pharmacyId;
	}

	public function getDisplayStatus() {
		$status = __("Unsigned");
		if ($this->eSignatureId > 0) {
			$status = __("Signed");
			switch ($this->transmit) {
				case 'ePrescribe':
					if ($this->dateTransmitted == '0000-00-00 00:00:00') { $status =  __("Pending ePrescription") ; }
					else { $status = __("ePrescribed " . date('Y-m-d',strtotime($this->dateTransmitted))); }
					break;
				case 'print':
					if ($this->dateTransmitted == '0000-00-00 00:00:00') { $status =  __("Not Yet Printed") ; }
					else { $status = __("Printed " . date('Y-m-d',strtotime($this->dateTransmitted))); }
					break;
				case 'fax':
					if ($this->dateTransmitted == '0000-00-00 00:00:00') { $status = __("Not Yet Faxed"); }
					else { $status = __("Faxed " . date('Y-m-d',strtotime($this->dateTransmitted))); }
					break;
				default:
					$status = "Signed " . date('Y-m-d',strtotime($this->datePrescribed)) . " Print Only";
					break;
			}
		}
		if ($this->daysSupply == -1 && $this->dateDiscontinued != '0000-00-00 00:00:00') {
			$status = __('Discontinued');
		}
		if ($this->patientReported) {
			$status = __('Patient Reported');
		}
		$messaging = new Messaging();
		$messaging->messagingId = $this->medicationId;
		$messaging->populate();
		if ($messaging->status == 'Fax Sent') {
			$status = __('Active');
		}
		return $status;
	}

	public function getDisplayAction() {
		$action = '';
		switch (strtolower($this->transmit)) {
			case 'fax':
				if ($this->eSignatureId > 0) {
					$action = 'Faxing';
					$messaging = new Messaging();
					$messaging->messagingId = $this->medicationId;
					$messaging->populate();
					if (strlen($messaging->status) > 0) {
						$action = $messaging->status;
					}
				}
				else {
					$action = 'Pending';
				}
				break;
		}
		return $action;
	}

	function getRefillsRemaining() {
		return $this->getRefills();
	}
	function getSummary() {
                return $this->description;
        }

        function getDocumentId() {
                return $this->medicationId;
        }
        function setDocumentId($id) {
                $this->medicationId = (int)$id;
        }

        function getContent() {
                return "";
        }

        static function getPrettyName() {
                return "Medications";
        }

        function setSigned($eSignatureId) {
                $this->eSignatureId = (int)$eSignatureId;
                $this->persist();
        }

	public static function getControllerName() {
		return "MedicationsController";
	}

	public function getExpires() {
		return date('Y-m-d H:i:s',strtotime('+'.$this->daysSupply.' days',strtotime($this->dateBegan)));
	}

	public function signatureNeeded() {
		$ret = true;
		if ($this->patientReported || (!(strlen($this->provider->deaNumber) > 0) && $this->isScheduled())) {
			$ret = false;
		}
		return $ret;
	}

	public function getScheduled() {
		$db = Zend_Registry::get('dbAdapter');
		$pkey = $this->pkey;
		$sqlSelect = $db->select()
				->from('chmed.basemed24','schedule')
				->where('pkey = ?',(string)$pkey);
		$ret = 0;
		$schedules = array('','I','II','III','IV','V');
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = (int)$row['schedule'];
		}
		return $schedules[$ret];
	}

	public function isScheduled() {
		$db = Zend_Registry::get('dbAdapter');
		$pkey = $this->pkey;
		$sqlSelect = $db->select()
				->from('chmed.basemed24','schedule')
				->where('pkey = ?',(string)$pkey);
		$ret = false;
		$row = $db->fetchRow($sqlSelect);
		if ($row && (int)$row['schedule'] > 0) {
			$ret = true;
		}
		return $ret;
	}

	public function isFreeForm() {
		$db = Zend_Registry::get('dbAdapter');
		$pkey = $this->pkey;
		$sqlSelect = $db->select()
				->from('chmed.basemed24','tradename')
				->where('pkey = ?',(string)$pkey);
		$ret = false;
		$row = $db->fetchRow($sqlSelect);
		if ($row && strtolower($row['tradename']) == 'freeform') {
			$ret = true;
		}
		return $ret;
	}

	public static function listQuantityQualifiersMapping() {
		$qualifiers = array(
			'AEROSOL'=>'UN',
			'AEROSOL, FOAM'=>'UN',
			'AEROSOL, METERED'=>'UN',
			'AEROSOL, POWDER'=>'UN',
			'AEROSOL, SPRAY'=>'UN',
			'BAG'=>'BG',
			'BEAD'=>'EA',
			'BEAD, IMPLANT, EXTENDED RELEASE'=>'EA',
			'BLOCK'=>'EA',
			'BOTTLE'=>'BO',
			'BOX'=>'BX',
			'CAPSULE'=>'AV',
			'CAPSULE, COATED'=>'AV',
			'CAPSULE, COATED PELLETS'=>'AV',
			'CAPSULE, COATED, EXTENDED RELEASE'=>'AV',
			'CAPSULE, DELAYED RELEASE'=>'AV ',
			'CAPSULE, DELAYED RELEASE PELLETS'=>'AV',
			'CAPSULE, EXTENDED RELEASE'=>'AV',
			'CAPSULE, GELATIN COATED'=>'AV',
			'CAPSULE, LIQUID FILLED'=>'AV',
			'CARTRIDGE'=>'CQ',
			'CIGARETTE'=>'EA',
			'CONTAINER'=>'CH',
			'CREAM'=>'UN',
			'CRYSTAL'=>'UN',
			'DISC'=>'EA',
			'DROP'=>'X4',
			'EACH'=>'EA',
			'ELIXIR'=>'UN',
			'EMULSION'=>'UN',
			'ENEMA'=>'EA',
			'EXTRACT'=>'UN',
			'FILM'=>'EA',
			'FILM, EXTENDED RELEASE'=>'EA',
			'FLUID OUNCE'=>'FO',
			'FOR SUSPENSION'=>'UN',
			'GAS'=>'UN',
			'GEL'=>'UN',
			'GEL, DENTIFRICE'=>'UN',
			'GLOBULE'=>'UN',
			'GRAM'=>'GR',
			'GRANULE'=>'UN',
			'GRANULE, DELAYED RELEASE'=>'UN',
			'GRANULE, EFFERVESCENT'=>'UN',
			'GRANULE, FOR SOLUTION'=>'UN',
			'GRANULE, FOR SUSPENSION'=>'UN',
			'GRANULE, FOR SUSPENSION, EXTENDED RELEASE'=>'UN',
			'GUM'=>'Y7',
			'IMPLANT'=>'EA',
			'INHALANT'=>'IH',
			'INHALER'=>'IH',
			'INJECTION'=>'SZ',
			'INJECTION, EMULSION'=>'UN',
			'INJECTION, POWDER, FOR SOLUTION'=>'UN',
			'INJECTION, POWDER, FOR SUSPENSION'=>'UN',
			'INJECTION, POWDER, FOR SUSPENSION, EXTENDED RELEAS...'=>'UN',
			'INJECTION, POWDER, LYOPHILIZED, FOR LIPOSOMAL SUSP...'=>'UN',
			'INJECTION, POWDER, LYOPHILIZED, FOR SOLUTION'=>'UN',
			'INJECTION, POWDER, LYOPHILIZED, FOR SUSPENSION'=>'UN',
			'INJECTION, POWDER, LYOPHILIZED, FOR SUSPENSION, EX...'=>'UN',
			'INJECTION, SOLUTION'=>'UN',
			'INJECTION, SOLUTION, CONCENTRATE'=>'UN',
			'INJECTION, SUSPENSION'=>'UN',
			'INJECTION, SUSPENSION, EXTENDED RELEASE'=>'UN',
			'INJECTION, SUSPENSION, LIPOSOMAL'=>'UN',
			'INSERT, EXTENDED RELEASE'=>'EA',
			'INTERNATIONAL UNITE'=>'F2',
			'INTRAUTERINE DEVICE'=>'EA',
			'IRRIGANT'=>'UN',
			'JELLY'=>'UN',
			'KIT'=>'KT',
			'LIPSTICK'=>'EA',
			'LIQUID'=>'UN',
			'LITER'=>'LT',
			'LOTION'=>'UN',
			'LOZENGE'=>'UU',
			'MILLIGRAM'=>'ME',
			'MILLILITER'=>'ML',
			'MILLION UNITS'=>'UM',
			'MOUTHWASH'=>'UN',
			'MUTUALLY DEFINED'=>'ZZ',
			'NOT SPECIFIED'=>'00',
			'OIL'=>'UN',
			'OINTMENT'=>'UN',
			'PACK'=>'PH',
			'PACKET'=>'12',
			'PASTE'=>'UN',
			'PASTE, DENTIFRICE'=>'UN',
			'PATCH'=>'FG',
			'PATCH, EXTENDED RELEASE'=>'FG',
			'PATCH, EXTENDED RELEASE, ELECTRICALLY CONTROLLED'=>'FG',
			'PELLET'=>'EA',
			'PINT'=>'PT',
			'POWDER'=>'UN',
			'POWDER, DENTIFRICE'=>'UN',
			'POWDER, FOR RECONSTITUTION'=>'UN',
			'POWDER, FOR SOLUTION'=>'UN',
			'POWDER, FOR SUSPENSION'=>'UN',
			'RINSE'=>'UN',
			'SHAMPOO'=>'UN',
			'SOAP'=>'UN',
			'SOLUTION'=>'UN',
			'SOLUTION, CONCENTRATE'=>'UN',
			'SOLUTION, FOR SLUSH'=>'UN',
			'SPRAY'=>'EA',
			'SPRAY, METERED'=>'EA',
			'SPRAY, METERED PUMP'=>'EA',
			'SPRAY, SUSPENSION'=>'UN',
			'STICK'=>'EA',
			'STRIP'=>'EA',
			'SUPPOSITORY'=>'AR',
			'SUSPENSION'=>'UN',
			'SUSPENSION, EXTENDED RELEASE'=>'UN',
			'SWAB'=>'EA',
			'SYRINGE'=>'SZ',
			'SYRUP'=>'UN',
			'TABLESPOON'=>'Y2',
			'TABLET'=>'U2',
			'TABLET, CHEWABLE'=>'U2',
			'TABLET, COATED'=>'U2',
			'TABLET, DELAYED RELEASE'=>'U2',
			'TABLET, DELAYED RELEASE PARTICLES'=>'U2',
			'TABLET, EFFERVESCENT'=>'U2',
			'TABLET, EXTENDED RELEASE'=>'U2',
			'TABLET, FILM COATED'=>'U2',
			'TABLET, FILM COATED, EXTENDED RELEASE'=>'U2',
			'TABLET, MULTILAYER'=>'U2',
			'TABLET, MULTILAYER, EXTENDED RELEASE'=>'U2',
			'TABLET, ORALLY DISINTEGRATING'=>'U2',
			'TABLET, SOLUBLE'=>'U2',
			'TABLET, SUGAR COATED'=>'U2',
			'TAPE'=>'EA',
			'TEASPOON'=>'Y3',
			'TINCTURE'=>'UN',
			'TRANSDERMAL PATCH'=>'FG',
			'TROCHE'=>'EA',
			'TUBE'=>'TB',
			'UNASSIGNED'=>'00',
			'UNIT'=>'UN',
			'VIAL'=>'VI',
			'WAFER'=>'EA',
		);
		return $qualifiers;
	}

	public static function getAuditId($medicationId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','auditId')
				->where('objectClass = ?','Medication')
				->where('objectId = ?',(int)$medicationId);
		$auditId = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$auditId = $row['auditId'];
		}
		return $auditId;
	}

	public function getQuantity() {
		return $this->_formatDecimal($this->quantity);
	}

	public function getRefills() {
		return $this->_formatDecimal($this->refills);
	}

	protected function _formatDecimal($value) {
		return $value;
		$ret = $value;
		$val = (int)$value;
		if ($value == $val) {
			$ret = $val;
		}
		return $ret;
	}

	public function getRxReferenceNumber() {
		$ret = '';
		if (strlen($this->refillRequestId) > 0) {
			$messaging = new Messaging();
			$messaging->messagingId = $this->refillRequestId;
			$messaging->populate();
			$rawMessage = $messaging->rawMessage;
			if (strlen($rawMessage) > 0) {
				$xml = new SimpleXMLElement($rawMessage);
				$ret = (string)$xml->Body->RefillRequest->RxReferenceNumber;
			}
		}
		return $ret;
	}

	public function getChmedDose() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
			->from('chmed.basemed24','dose')
			->where('pkey = ?',$this->pkey);
		$ret = '';
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = $row['dose'];
		}
		return $ret;
	}

	public function ssCheck() {
		$ret = array();
		//if ($this->transmit != 'ePrescribe' || $this->isScheduled()) {
		//	return $ret;
		//}

		$refills = (int)$this->refills;
		if (strcmp($refills,$this->refills) !== 0) {
			$ret[] = 'Refills must be whole number.';
		}
		else if ($this->prn && $this->refills > 0) {
			$ret[] = 'Refills must be set to 0 when PRN is selected.';
		}

		$daysSupply= (int)$this->daysSupply;
		if (strcmp($daysSupply,$this->daysSupply) !== 0) {
			$ret[] = 'Days Supply must be whole number.';
		}

		$description = trim($this->description);
		$descLen = strlen($description);
		if (strpos($description,"\n") !== false) {
			$ret[] = 'Description cannot contain multiple lines';
		}
		else if ($descLen < 4 || $descLen > 105) {
			$ret[] = 'Description must be between 4 and 105 characters long.';
		}
		//$ret[] = 'Description length is: '.$descLen.' limit is 105';

		$directions = trim($this->directions);
		$dirLen = strlen($directions);
		if (strpos($directions,"\n") !== false) {
			$ret[] = 'Directions cannot contain multiple lines';
		}
		else if ($dirLen < 4 || $dirLen > 140) {
			//$ret[] = 'Directions must be between 4 and 140 characters long.';
			//$ret[] = 'Directions field must be supplied.';
			$ret[] = 'Directions field must be supplied and not more than 140 characters.';
		}
		//$ret[] = 'Directions length is: '.$dirLen.' limit is 140';

		$comment = trim($this->comment);
		$noteLen = strlen($comment);
		if (strpos($comment,"\n") !== false) {
			$ret[] = 'Comment cannot contain multiple lines';
		}
		else if ($noteLen > 0 && $noteLen > 210) {
			$ret[] = 'Comment cannot be more than 210 characters in length.';
		}
		//$ret[] = 'Comment length is: '.$noteLen.' limit is 210';

		$pharmacyId = (int)$this->pharmacyId;
		if (!$pharmacyId > 0) {
			$ret[] = 'No pharmacy selected.';
		}

		$isnum = true;
		$x = explode('.',$this->quantity);
		$x0 = (int)$x[0];
		if (isset($x[1])) {
			$x1 = (int)$x[1];
		}
		if (strcmp($x0,$x[0]) !== 0) {
			$isnum = false;
		}
		else if (isset($x[1]) && !ctype_digit($x[1])) {
			$isnum = false;
		}
		$qtyLen = strlen($this->quantity);
		if (!$qtyLen > 0) {
			$ret[] = 'Quantity field must be supplied.';
		}
		else if (!$this->quantity > 0) {
			$ret[] = 'Quantity field must be greater than 0.';
		}
		else if ($qtyLen > 15) {
			$ret[] = 'Quantity cannot be more than 15 characters in length.';
		}
		else if (!$isnum || isset($x[2])) {
			$ret[] = 'Quantity field is invalid.';
		}

		$qualifiers = self::listQuantityQualifiersMapping();
		if (!isset($qualifiers[$this->quantityQualifier])) {
			$ret[] = 'Quantity qualifier \''.$this->quantityQualifier.'\' is invalid.';
		}

		return $ret;
	}

	public function getBaseMed24() {
		static $baseMed24List = array();
		if (isset($baseMed24List[$this->pkey])) return $baseMed24List[$this->pkey];
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('chmed.basemed24')
				->where('chmed.basemed24.pkey = ?',$this->pkey)
				->limit(1);
		$baseMed24 = new BaseMed24();
		if ($row = $db->fetchRow($sqlSelect)) {
			$baseMed24->populateWithArray($row);
		}
		$baseMed24List[$this->pkey] = $baseMed24;
		return $baseMed24;
	}

}
