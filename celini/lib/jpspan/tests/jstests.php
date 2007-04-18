<?php
/**
* Unit Tests using the JsUnit framework:
* @see http://www.edwardh.com/jsunit/
* @package JPSpan
* @subpackage Tests
* @version $Id: jstests.php,v 1.5 2004/11/22 10:18:13 harryf Exp $
*/
require_once 'config.php';
?>
<html>
 <head>
  <title>JPSpan Javascript Tests</title>
  <?php jsunit_drawHeader(); ?>

    <script language="JavaScript" type="text/javascript">
    <!--

    function JPSpanClientTestSuite() {
      var newsuite = new top.jsUnitTestSuite();
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/httpclient.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/encode_php.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/encode_xml.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/remoteobject.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/request.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/request_get.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/request_post.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/request_rawpost.test.php");
      newsuite.addTestPage( "<?php echo JPSPAN_TESTS;?>/js/serialize.test.php");
      return newsuite;
    }

    function suite() {
      var newsuite = new top.jsUnitTestSuite();
      newsuite.addTestSuite(JPSpanClientTestSuite());
      return newsuite;
    }

    -->
    </script>


 </head>
 <body>
    <h1>JPSpan Javascript Tests</h1>
    <p>
    <ul>
      <li><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php 
      echo $_SERVER['PHP_SELF'];
      ?>" target=\"_blank\">Run All Tests</a></li>
    <?php
    $dir = './js/';
    if (is_dir($dir)) {
      if ( $d = opendir($dir) ) {
        while (($file = readdir($d)) !== false) {
          if ( is_file($dir.$file) ) {
            $farray = explode('.',$file);
            if ( $farray[1] == 'test' ) {
              echo "      <li><a href=\"".JSUNIT."/testRunner.html?testpage=".
                JPSPAN_TESTS."/js/$file\" target=\"_blank\">Run {$farray[0]} Test</a></li>";
            }
          }
        }
        closedir($d);
      }
    }
    ?>
    </ul>
    <p>Powered by <a href="http://www.edwardh.com/jsunit/">JsUnit</a></p>
    <p>Running the tests:
      <ol>
        <li>Requires PHP (tested with PHP 4.3.x and 5.x+)</li>
        <li>Place <a href="http://www.edwardh.com/jsunit/">JsUnit</a> somewhere under under your web root so it's available like http://localhost/jsunit (configurable).</li>
        <li>Edit the 'config.php' script in this directory.</li>
      </ol>
    </p>
 </body>
</html>
