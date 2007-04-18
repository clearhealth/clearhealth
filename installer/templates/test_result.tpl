{if $test->getResult() == $smarty.const.INSTALLER_TEST_SUCCESS }
<img src='images/green_check.gif'>
{elseif $test->getResult() == $smarty.const.INSTALLER_TEST_WARNING }
<img src='images/yellow_check.gif'>
{else}
<img src='images/red_check.gif'>
{/if}
{$test->getResultMessage()}<BR>
