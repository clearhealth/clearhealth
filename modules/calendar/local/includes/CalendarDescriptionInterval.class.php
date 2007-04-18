<?php

class CalendarDescriptionInterval {
	var $intervalStart;
	var $length;

	var $parent;

	function CalendarDescriptionInterval($start,$length) {
		$this->intervalStart = $start;
		$this->length = $length;
	}

	function setParent(&$p) {
		$this->parent =& $p;
	}

	/**
	 * Redo this to make sure we don't have events that start in another event 
	 * but not at the same time.
	 *
	 * @return unknown
	 */
	function hasMultiple() {
		if (isset($this->parent->parent->events[$this->intervalStart]) && count($this->parent->parent->events[$this->intervalStart]) > 1) {
			return true;
		}
		return false;
	}

	/**
	 * Get all the intervals with the same startign time as this interval
	 */
	function getEventsHTML() {
		$ret = array();
		$cdesc =& $this->parent->getCalendarDescription();
		$events =& $cdesc->events;
		if($events != null) {
		$e =& $events->current();

		while ($events->valid()) {
			$sts = $e->get('start_ts');
			$ets = $e->get('end_ts');
			if ($sts < $this->intervalStart) {
				$e =& $events->next();
			}else if ($sts >= $this->intervalStart && $ets < ($this->intervalStart + $this->length)) {
				$html = $cdesc->render($e);
				$ret[$e->get('event_id')] = $html;
				$e =& $events->next();
			}else{
				break;
			}
		}
		$events->rewind();
		}
		return $ret;
	}

	function getTimestamp() {
		return $this->intervalStart;
	}
}

?>
