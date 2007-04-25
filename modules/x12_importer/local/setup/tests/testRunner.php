<?php

if (SimpleReporter::inCli()) {
    exit ($test->run(new ColorTextReporter()) ? 0 : 1);
}

$test->run(new HtmlReporter());


?>
