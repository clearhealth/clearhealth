<?php
/**
* @package JPSpan
* @subpackage Script
* @version $Id: Script.php,v 1.1 2004/11/10 16:04:46 harryf Exp $
*/

/**
* Utilities for managing Javascript
* @package JPSpan
* @subpackage Script
* @access public
*/
class JPSpan_Script {

    /**
    * "Compress" Javascript
    * Scheduled to be replaced eventually with something using the Lexer
    * @see http://pupius.co.uk/download/php_scripts/preview/script-compress.phps/
    * @param string Javascript
    * @return string Javascript (compressed)
    * @access public
    * @static
    */
    function compress($script) {
        //remove windows cariage returns
        $script = str_replace("\r",'',$script);
        
        //array to store replaced literal strings
        $literal_strings = array();
        
        //explode the string into lines
        $lines = explode("\n",$script);
        
        //loop through all the lines, building a new string at the same time as removing literal strings
        $clean = '';
        $inComment = false;
        $literal = '';
        $inQuote = false;
        $escaped = false;
        $quoteChar = '';
        
        for($i=0;$i<count($lines);$i++) {
            $line = $lines[$i];
        
            $inNormalComment = false;
        
            //loop through line's characters and take out any literal strings, replace them with ___i___ where i is the index of this string
            for($j=0;$j<strlen($line);$j++) {
                $c = substr($line,$j,1);
                $d = substr($line,$j,2);
        
                //look for start of quote
                if(!$inQuote && !$inComment) {
                    //is this character a quote or a comment
                    if(($c=='"' || $c=="'") && !$inComment && !$inNormalComment) {
                        $inQuote = true;
                        $inComment = false;
                        $escaped = false;
                        $quoteChar = $c;
                        $literal = $c;
        
                    } else if($d=='/*' && !$inNormalComment) {
                        $inQuote = false;
                        $inComment = true;
                        $escaped = false;
                        $quoteChar = $d;
                        $literal = $d;
        
                        $j++;
        
                    //ignore string markers that are found inside comments
                    } else if($d=='//') {
                        $inNormalComment = true;
                        $clean .= $c;
                    } else {
                        $clean .= $c;
                    }
        
        
                //already in a string so find end quote
                } else {
                    if($c == $quoteChar && !$escaped && !$inComment) {
                        $inQuote = false;
                        $literal .= $c;
        
                        //subsitute in a marker for the string
                        $clean .= '___' . count($literal_strings) . '___';
        
                        //push the string onto our array
                        array_push($literal_strings,$literal);
        
                    } else if($inComment && $d=="*/") {
                        $inComment = false;
                        $literal .= $d;
        
                        //subsitute in a marker for the string
                        $clean .= '___' . count($literal_strings) . '___';
        
                        //push the string onto our array
                        array_push($literal_strings,$literal);
        
                        $j++;
        
                    } else if($c == "\\" && !$escaped) $escaped = true;
                    else $escaped = false;
        
                    $literal .= $c;
                }
            }
        
            if($inComment) $literal .= "\n";
            $clean .= "\n";
        }
        
        //explode the clean string into lines again
        $lines = explode("\n",$clean);
        
        //now process each line at a time
        for($i=0;$i<count($lines);$i++) {
            $line = $lines[$i];
        
            //remove comments
            $line = preg_replace("/\/\/(.*)/",'',$line);
        
            //strip leading and trailing whitespace
            $line = trim($line);
        
            //remove all whitespace with a single space
            $line = preg_replace("/\s+/",' ',$line);
        
            //remove any whitespace that occurs after/before an operator
            $line = preg_replace("/\s*([!\}\{;,&=\|\-\+\*\/\)\(:])\s*/","\\1",$line);
        
            $lines[$i] = $line;
        }
        
        //implode the lines
        $script = implode("\n",$lines);
        
        //make sure there is a max of 1 \n after each line
        $script = preg_replace("/[\n]+/","\n",$script);
        
        //strip out line breaks that immediately follow a semi-colon
        $script = preg_replace("/;\n/",';',$script);
        
        //curly brackets aren't on their own
        $script = preg_replace("/[\n]*\{[\n]*/","{",$script);
        
        //finally loop through and replace all the literal strings:
        for($i=0;$i<count($literal_strings);$i++) {
            $script = str_replace('___'.$i.'___',$literal_strings[$i],$script);
        }
        
        return $script;
    }
}
