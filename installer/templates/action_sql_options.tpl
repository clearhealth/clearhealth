{if $loop != 3}
{foreach name=sqloptions from=$files key=name item=file}
<input type="radio" name="optfile" value="{$file}"
{if $smarty.foreach.sqloptions.first} checked{/if}
>&nbsp;&nbsp;&nbsp;{$name} <br />
{/foreach}
<br />

<input type="submit" name="install_sql" value="Install File"> &nbsp;&nbsp;
<input type="submit" name="install_sql_done" value="Done">
<BR />
{else if $loop == 3}
<div style="padding: 0 0 0 60px;">
<p>The database file is being installed.</p>
<img src="images/animated_progress.gif" />
</div>
{/if}

