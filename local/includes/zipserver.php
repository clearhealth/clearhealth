<?php

class zipcode{
	var $zip, $city, $state, $lat, $lon, $tz_offset, $dst, $country;
	
	function zipcode(
		$zip = '', 
		$city = '',
		$state = '',
		$lat = '',
		$lon = '',
		$tz_offset = '',
		$dst = '',
		$country = ''
	){
		$this->zip = $zip;
		$this->city = $city;
		$this->state = $state;
		$this->lat = $lat;
		$this->lon = $lon;
		$this->tz_offset = $tz_offset;
		$this->dst = $dst;
		$this->country = $country;
	}

	// grab the zip, return the data	
	function getData($id,$addressid){
		$db=&new clniDB();

		$result = $db->execute("SELECT  zip,city,state,lat,lon,tz_offset,dst,country"
			." FROM zipcodes WHERE zip = ".$db->quote($id));

		if($result && !$result->EOF){
			$res = $result->fields;
			$res['addressid'] = $addressid;
			return $res;
		} else {
			return false;
		}
	}
}

?>