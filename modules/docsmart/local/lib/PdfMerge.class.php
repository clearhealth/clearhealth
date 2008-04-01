<?php
class PDFMerge {

	var $bin = "/usr/local/bin/pdfMerge";


	/**
	 *  Simple function merges a bunch of pdfs together and send the output to stdOut
	 *  This function expects any headers for browsers etc to be set outside of it
	 *
	 *  @param	array	$files
	 *  @param	array	$filesnames
	 *  @param	array	$titles
	 */ 
	function merge($files,$filenames,$titles) {

		$cmd = $this->bin .' STDOUT STDIN';

		// were going to send a config xml file over STDIN and get a PDF over STDOUT

		// build XML
		$xml = "<pdfMerge>\n";
		foreach($files as $index => $file) {
			$xml .= "<document>".
				"<title>".htmlentities($titles[$index])."</title>".
				"<filename>".htmlentities($filenames[$index])."</filename>".
				"<file>".htmlentities($file)."</file>".
				"</document>\n";
		}
		$xml .= "</pdfMerge>\n";

		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		//	2 => array("file", "/tmp/pdfMerge.err.txt", "w") // stderr to stdout
		);

		$pipes = array();
		$process = proc_open($cmd,$descriptorspec,$pipes);

		if (!is_resource($process)) {
			var_dump(proc_close($process));
			die("Failed to run pdfMerge");
		}

		fwrite($pipes[0],$xml);
		fclose($pipes[0]);

		echo stream_get_contents($pipes[1]);
		fclose($pipes[1]);
	}
}
