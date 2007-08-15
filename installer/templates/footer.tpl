		<div id="install_block">
		<A style="margin-right:500px;" HREF="{$smarty.server.PHP_SELF}?restart_installer=true">Restart Installation</A>&nbsp;&nbsp;

		{if $CAN_CONTINUE}
			<A HREF="{$smarty.server.PHP_SELF}?next_step=true">Continue...</A>&nbsp;&nbsp;
		{/if}
		</div>
	</div>

	<div id="footer">
		Copyright <a href="http://www.clear-health.com">ClearHealth Inc.</a> 2003-2007<br />
	</div>
</body>
</html>
