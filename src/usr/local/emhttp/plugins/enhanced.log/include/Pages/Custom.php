<?php

namespace EDACerton\EnhancedLog;

use EDACerton\PluginUtils\Translator;

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

if ( ! defined(__NAMESPACE__ . '\PLUGIN_ROOT') || ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
    throw new \RuntimeException("Common file not loaded.");
}

$tr = $tr ?? new Translator(PLUGIN_ROOT);

?>

<table class="tablesorter shift ups">
<thead><tr><th><?= $tr->tr('custom_match_strings'); ?></th></tr></thead>
</table>
 
<?php
$file = "/boot/config/plugins/enhanced.log/custom_syslog.conf";
$text = @file_get_contents($file);
?>

<div style="width: 80%; float:left">

    
        <p>Enter any custom events you want highlighted in the log.  Any custom events you enter will be processed after the built in events.
        The events are entered as: "search string", "event"
        Case is ignored in the search string and wild cards are allowed.</p>
        <p>Examples:</p>
        <ol>
        <li>"found.*chip" will match any string with 'found' and 'chip' with any text in between</li>
        <li>"(spinup|spindown)" will match any string with either 'spinup' or 'spindown'. </li>
</ol>
        <p>The search string and event are quoted, and each line contains one match string and event combination.  Errors in the strings can cause php errors.
        Events are: "error", "minor issues", "lime tech", "array", "system", "file system", "drive related", "network", "login", "emhttp", "other".</p>
    

	<form markdown="1" method="POST" action="/update.php" target="progressFrame">
	<input type="hidden" name="#include" value="/webGui/include/update.file.php">
	<input type="hidden" name="#raw_file" value="true">
	<input type="hidden" name="#file" value="<?= $file;?>">

    <dl>
        <dt><?= $tr->tr('custom_match_strings'); ?></dt>
        <dd>
	        <textarea spellcheck="false" cols="80" rows="22" maxlength="2048" name="text" style="font-family:bitstream;width:70%;"><?= htmlspecialchars($text ?: "");?></textarea>
        </dd>
    </dl>

	<dl><dt>&nbsp;</dt><dd>
	<input type="submit" value='<?= $tr->tr('apply'); ?>'><input type="button" value="<?= $tr->tr('done'); ?>" onclick="done()">
	</dd></dl>

	</form>
</div>
