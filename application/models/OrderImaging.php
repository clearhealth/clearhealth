<?php
/*****************************************************************************
*       OrderImaging.php
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


class OrderImaging extends WebVista_Model_ORM {

	protected $orderId;
	protected $order;
	protected $imagingType;
	protected $procedure;
	protected $historyAndReason;
	protected $category;
	protected $orderingLocation;
	protected $dateRequested;
	protected $transportMode;
	protected $isolation;
	protected $pregnant;
	protected $urgency;
	protected $datePreOp;
	protected $submitTo;
	protected $modifiers;
	protected $_table = 'orderImaging';
	protected $_primaryKeys = array('orderId');

	const IMAGING_ENUM_NAME = 'Imaging Preferences';
	const IMAGING_ENUM_KEY = 'IMAGING';
	const IMAGING_TYPES_ENUM_NAME = 'Imaging Types';
	const IMAGING_TYPES_ENUM_KEY = 'TYPES';
	const IMAGING_CATEGORIES_ENUM_NAME = 'Categories';
	const IMAGING_CATEGORIES_ENUM_KEY = 'CATEGORIES';
	const IMAGING_URGENCIES_ENUM_NAME = 'Urgencies';
	const IMAGING_URGENCIES_ENUM_KEY = 'URGENCIES';
	const IMAGING_TRANSPORTS_ENUM_NAME = 'Transports';
	const IMAGING_TRANSPORTS_ENUM_KEY = 'TRANSPORTS';
	const IMAGING_PREGNANTS_ENUM_NAME = 'Pregnants';
	const IMAGING_PREGNANTS_ENUM_KEY = 'PREGNANTS';

	public function __construct() {
		$this->order = new Order();
		$this->order->type = Order::TYPE_IMAGING;
	}

	public function getDisplayIsolation() {
		return ($this->isolation)?'Yes':'No';
	}

	public function getDisplayPregnant() {
		return ($this->pregnant)?'Yes':'No';
	}

	public function getDisplayOrder() {
		$content = $this->order->displayOrder;
		$content .= PHP_EOL;
		$labels = array(
			'procedure'=>__('Procedure'),
			'historyAndReason'=>__('History and Reason'),
			'category'=>__('Category'),
			'dateRequested'=>__('Date Requested'),
			'transportMode'=>__('Transport Mode'),
			'isolation'=>__('Isolation'),
			'pregnant'=>__('Pregnant'),
			'urgency'=>__('Urgency'),
			'datePreOp'=>__('Date Pre-Op Schedule'),
		);
		$padLength = 0;
		foreach ($labels as $key=>$label) {
			$label .= ':';
			$labels[$key] = $label;
			$labelLen = strlen($label);
			if ($labelLen > $padLength) {
				$padLength = $labelLen;
			}
		}
		$padLength += 2;
		$content .= PHP_EOL.str_pad($labels['procedure'],$padLength).$this->procedure;
		$content .= PHP_EOL.str_pad($labels['historyAndReason'],$padLength).$this->historyAndReason;
		$content .= PHP_EOL.str_pad($labels['category'],$padLength).$this->category;
		$content .= PHP_EOL.str_pad($labels['dateRequested'],$padLength).$this->dateRequested;
		$content .= PHP_EOL.str_pad($labels['transportMode'],$padLength).$this->transportMode;
		$content .= PHP_EOL.str_pad($labels['isolation'],$padLength).$this->getDisplayIsolation();
		$content .= PHP_EOL.str_pad($labels['pregnant'],$padLength).$this->getDisplayPregnant();
		$content .= PHP_EOL.str_pad($labels['urgency'],$padLength).$this->urgency;
		$content .= PHP_EOL.str_pad($labels['datePreOp'],$padLength).$this->datePreOp;
		return $content;
	}

	public function setOrderId($id) {
		$this->order->orderId = (int)$id;
		$this->orderId = $this->order->orderId;
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->order->ORMFields())) {
			return $this->order->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->order->__get($key))) {
			return $this->order->__get($key);
		}
		return parent::__get($key);
	}

}
