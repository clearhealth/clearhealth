<?php

class EDIHelper
{
	/**
	 * Returns the number of segments within a given result
	 *
	 * {@internal each tilde represents a segment, count them up}
	 * @param string 
	 *    The result of a ElectronicClaimRenderer
	 * @return int
	 */
	function postfilterSegmentCount($text) {
		// this is a simple count of every tilde in the file.s 
		$count = substr_count($text,'~');	
		return $count;
	}
	
	
	/**
	 * Replaces out the POSTFILTER_SEGEMENT_COUNT text with the real postfilter
	 * segment count.
	 *
	 * We know that the count will be off by 4. There are three segments that
	 * are not between the ST and the SE segments:
	 *    ISA does not get counted
	 *    GS does not get counted
	 *    ST counts
	 *    everything in between counts
	 *    SE does not count
	 *    GE does not count
	 *    so we subtract 4 from the count of segments
	 *
	 * @param int
	 *    Total number of segments
	 * @param string
	 *    The result of a ElectronicClaimRenderer
	 * @return string
	 *    The results of the string replacement
	 */
	function postfilterSEReplacement($total, $text) {

		$segments_between_ST_and_SE = $total - 4;	
		//put the count in place of the POSTFILTER_SEGEMENT_COUNT token 
		return preg_replace("/POSTFILTER_SEGEMENT_COUNT/",$segments_between_ST_and_SE,$text);	
	}
}
