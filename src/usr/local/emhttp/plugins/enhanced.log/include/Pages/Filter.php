<?php

namespace EnhancedLog;

/*
    Copyright 2015-2025, Dan Landon
    Copyright 2025  Derek Kaser

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

$tr = $tr ?? new Translator();

$file = "/boot/config/plugins/enhanced.log/syslog_filter.conf";
$text = @file_get_contents($file);
?>

<table class="tablesorter shift ups">
<thead><tr><th><?= $tr->tr('syslog_filter_strings'); ?></th></tr></thead>
</table>

<div style="width: 80%; float:left">

<p>
You can filter out log entries to keep your Syslog from being overwhelmed with debug or redundant log entries.  You should only filter out log entries that you know are unnecessary or redundant.
Because the Syslog is very important for debugging issues, don't filter out entries unnecessarily.
</p>

<p>Enter the log strings you want to filtered from the Syslog.  Each entry is on a separate line with leading and trailing quotes.</p>

	<form markdown="1" method="POST" action="/update.php" target="progressFrame">
	<input type="hidden" name="#include" value="/webGui/include/update.file.php">
	<input type="hidden" name="#raw_file" value="true">
	<input type="hidden" name="#file" value="<?= $file;?>">

	<dl>
		<dt><?= $tr->tr('syslog_filter_strings'); ?></dt>
		<dd>
			<textarea spellcheck="false" cols="80" rows="22" maxlength="2048" name="text" style="font-family:bitstream;width:70%;"><?= htmlspecialchars($text ?: "");?></textarea>
		</dd>
	</dl>

	<dl><dt>&nbsp;</dt><dd>
		<form name="apply_syslog_filter" method="POST" action="/update.php" target="progressFrame">
			<input type="hidden" name="#command" value="/plugins/enhanced.log/scripts/rc.enhanced.log">
			<input type="submit" value="<?= $tr->tr('apply'); ?>"><input type="button" value="<?= $tr->tr('done'); ?>" onclick="done()">
		</form>
	</dd></dl>

	</form>
</div>
