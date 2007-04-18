<?php
/**
* Unit Tests using the SimpleTest framework:
* @see http://www.lastcraft.com/simple_test.php
* @package JPSpan
* @subpackage Tests
* @version $Id: phptests.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
*/
require_once 'config.php';
?>
<html>
 <head>
  <title>JPSpan PHP Tests</title>
  <?php jsunit_drawHeader(); ?>
 </head>
 <body>
    <h2>JPSpan PHP Tests</h2>
    <p>
    <ul>
      <li><a href="<?php echo JPSPAN_TESTS; ?>/php/alltests.all.php" target=\"_blank\">Run All Tests</a></li>
    <?php
    $dir = './php/';
    if (is_dir($dir)) {
      if ( $d = opendir($dir) ) {
        while (($file = readdir($d)) !== false) {
          if ( is_file($dir.$file) ) {
            $farray = explode('.',$file);
            if ( $farray[1] == 'group' ) {
              echo "      <li><a href=\"".JPSPAN_TESTS."/php/$file\" target=\"_blank\">Run {$farray[0]} Group Test</a></li>";
            }
          }
        }
        rewinddir($d);
        while (($file = readdir($d)) !== false) {
          if ( is_file($dir.$file) ) {
            $farray = explode('.',$file);
            if ( $farray[1] == 'test' ) {
              echo "      <li><a href=\"".JPSPAN_TESTS."/php/$file\" target=\"_blank\">Run {$farray[0]} Test</a></li>";
            }
          }
        }
        closedir($d);
      }
    }
    ?>
    </ul>
    <p>Powered by <a href="http://www.lastcraft.com/simple_test.php">SimpleTest</a></p>
    <p>Running the tests:
      <ol>
        <li>Requires PHP (tested with PHP 4.3.x and 5.x+)</li>
        <li>Download <a href="http://www.lastcraft.com/simple_test.php">SimpleTest</a> to your filesystem</li>
        <li>Edit the 'config.php' script in this directory.</li>
      </ol>
    </p>
 </body>
</html>
