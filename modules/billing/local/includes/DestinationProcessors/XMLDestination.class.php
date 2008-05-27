<?php
class XMLDestination extends DestinationProcessor{

	var $_internalName = "XMLDestination";
	var $_label = 'PDF';
	var $_package = null;
	var $_format = null;

	function XMLDestination(){
	}

	function processPackage($package, &$claim, $format) {
		$filename = $format . "/" . $format . ".pdf";
		$this->_format = $format;
		$this->_claim = $claim;
		$chars = md5(uniqid(rand()));
		$uuid  = substr($chars,0,8) . '-' . substr($chars,8,4) . '-' . substr($chars,12,4) . '-' . substr($chars,16,4) . '-'. substr($chars,20,12);
		$header =
'<?xml version="1.0" encoding="UTF-8"?><?xfa generator="XFA2_4" APIVersion="2.6.7116.0"?>
<xdp:xdp xmlns:xdp="http://ns.adobe.com/xdp/" timeStamp="' . date('Y-m-dTs:i:sZ') . '" uuid="'.$uuid.'">
<xfa:datasets xmlns:xfa="http://www.xfa.org/schema/xfa-data/1.0/">
<xfa:data>
<form1>';
$str = $package;
$str .=
'</form1>
</xfa:data>
</xfa:datasets>
<pdf href="'  . Celini::getBaseURI() . 'index.php/Images/'. $format . '.pdf" xmlns="http://ns.adobe.com/xdp/pdf/" />
</xdp:xdp>';
               	header("Content-type: text/xml");
                
		echo $header.$str;exit;
		$this->_package = $header.$str;exit;
		
	}

	function outputResults() {
		return $this->_package;
	}

}

	$dpm =& Celini::dpmInstance();
	$dpm->registerDestinationProcessor('XML', 'XMLDestination');
?>
