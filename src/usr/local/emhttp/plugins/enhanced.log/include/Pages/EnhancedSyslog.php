<?php

namespace EnhancedLog;

/*
    Copyright 2015-2016, Lime Technology
    Copyright 2015-2016, Bergware International.
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

?>
<div>
<input type="button" value="<?= $tr->tr("download"); ?>" onclick="getlog()">
<input type="button" value="<?= $tr->tr("refresh"); ?>" onclick="refresh()">
<button type="button" onclick="done()"><?= $tr->tr("done"); ?></button>
</div>
<?php

$zip = str_replace(' ', '_', strtolower($varName ?? "")) . "-syslog-" . date('Ymd-Hi') . ".zip";

$enhanced_log_cfg = Utils::getConfig();

$display_lines = (isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);

// Array of strings and highlight colors - "match string", "event".
$pre_defined = array_map('str_getcsv', file('plugins/enhanced.log/syslog_match.conf') ?: array());
if ($enhanced_log_cfg['OTHER'] == "yes") {
    $custom = array_map('str_getcsv', file('/boot/config/plugins/enhanced.log/custom_syslog.conf') ?: array());
    $match  = array_merge($pre_defined, $custom);
} else {
    $match = array_merge($pre_defined);
}
unset($pre_defined);
unset($custom);

$themeArray = array('white', 'black');
if (in_array($theme ?? "", $themeArray)) {
    $font_size = '10pt';
} else {
    $font_size = '14pt';
}

if ($enhanced_log_cfg['ERRORS'] == "yes") {
    $errors_color = $enhanced_log_cfg['ERRORS_CLR'];
    $errors_tag   = "<span style='background-color:{$errors_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.error") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $errors_color = "";
    $errors_tag   = "";
}

if ($enhanced_log_cfg['MINOR_ISSUES'] == "yes") {
    $minor_issues_color = $enhanced_log_cfg['MINOR_ISSUES_CLR'];
    $minor_issues_tag   = "<span style='background-color:{$minor_issues_color}; font-size: {$font_size}'>&nbsp" . $tr->tr("colors.minor") . "&nbsp</span>&nbsp;&nbsp;&nbsp;";
} else {
    $minor_issues_color = "";
    $minor_issues_tag   = "";
}

if ($enhanced_log_cfg['LIME_TECH'] == "yes") {
    $lime_tech_color = $enhanced_log_cfg['LIME_TECH_CLR'];
    $lime_tech_tag   = "<span style='background-color:{$lime_tech_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.limetech") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $lime_tech_color = "";
    $lime_tech_tag   = "";
}

if ($enhanced_log_cfg['ARRAY'] == "yes") {
    $array_color = $enhanced_log_cfg['ARRAY_CLR'];
    $array_tag   = "<span style='background-color:{$array_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.array") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $array_color = "";
    $array_tag   = "";
}

if ($enhanced_log_cfg['SYSTEM'] == "yes") {
    $system_color = $enhanced_log_cfg['SYSTEM_CLR'];
    $system_tag   = "<span style='background-color:{$system_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.system") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $system_color = "";
    $system_tag   = "";
}

if ($enhanced_log_cfg['FILE_SYSTEM'] == "yes") {
    $file_system_color = $enhanced_log_cfg['FILE_SYSTEM_CLR'];
    $file_system_tag   = "<span style='background-color:{$file_system_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.file") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $file_system_color = "";
    $file_system_tag   = "";
}

if ($enhanced_log_cfg['DRIVE_RELATED'] == "yes") {
    $drive_related_color = $enhanced_log_cfg['DRIVE_RELATED_CLR'];
    $drive_related_tag   = "<span style='background-color:{$drive_related_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.drive") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $drive_related_color = "";
    $drive_related_tag   = "";
}

if ($enhanced_log_cfg['NETWORK'] == "yes") {
    $network_color = $enhanced_log_cfg['NETWORK_CLR'];
    $network_tag   = "<span style='background-color:{$network_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.network") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $network_color = "";
    $network_tag   = "";
}

if ($enhanced_log_cfg['LOGIN'] == "yes") {
    $login_color = $enhanced_log_cfg['LOGIN_CLR'];
    $login_tag   = "<span style='background-color:{$login_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.login") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $login_color = "";
    $login_tag   = "";
}

if ($enhanced_log_cfg['EMHTTP'] == "yes") {
    $emhttp_color = $enhanced_log_cfg['EMHTTP_CLR'];
    $emhttp_tag   = "<span style='background-color:{$emhttp_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.emhttp") . "&nbsp;</span>&nbsp;&nbsp;&nbsp;";
} else {
    $emhttp_color = "";
    $emhttp_tag   = "";
}

if ($enhanced_log_cfg['OTHER'] == "yes") {
    $other_color = $enhanced_log_cfg['OTHER_CLR'];
    $other_tag   = "<span style='background-color:{$other_color}; font-size: {$font_size}'>&nbsp;" . $tr->tr("colors.other") . "&nbsp;</span>";
} else {
    $other_color = "";
    $other_tag   = "";
}

// Adjust array colors.
$i = 0;
foreach ($match as $s) {
    switch ($s[1]) {
        case ("error"):
            $match[$i][1] = $errors_color;
            break;
        case ("minor issue"):
            $match[$i][1] = $minor_issues_color;
            break;
        case ("lime tech"):
            $match[$i][1] = $lime_tech_color;
            break;
        case ("array"):
            $match[$i][1] = $array_color;
            break;
        case ("system"):
            $match[$i][1] = $system_color;
            break;
        case ("file system"):
            $match[$i][1] = $file_system_color;
            break;
        case ("drive related"):
            $match[$i][1] = $drive_related_color;
            break;
        case ("network"):
            $match[$i][1] = $network_color;
            break;
        case ("login"):
            $match[$i][1] = $login_color;
            break;
        case ("emhttp"):
            $match[$i][1] = $emhttp_color;
            break;
        case ("other"):
            $match[$i][1] = $other_color;
            break;
        default:
            break;
    }
    $i++;
}

echo ($resize ?? false) ? "<pre style='display:none'>" : "<pre>";
$log_lines = explode("\n", shell_exec("/usr/bin/cat /var/log/syslog | tail -n " . escapeshellarg(strval($display_lines))) ?: "");
$log_lines = array_reverse($log_lines);

foreach ($log_lines as $line) {
    $line .= "\n";
    $found_it = false;
    $skip     = false;
    $i        = 0;
    foreach ($match as $s) {
        if (($s[1] !== "") && (preg_match("/{$s[0]}/i", $line))) {
            $found_it = true;
            $skip     = $s[1] == "skip";
            break;
        }
        $i++;
    }

    if (($found_it) && ( ! $skip) || ($enhanced_log_cfg['TEXT'] == "yes")) {
        $line = htmlspecialchars($line);
        echo "<span style='background-color:" . (isset($match[$i][1]) ? $match[$i][1] : "") . "';>{$line}</span>";
    }
}
echo "</pre>";
?>

<script>
function cleanUp() {
	if (document.hasFocus()) {
		$('input[value="Downloading..."]').val('Download').prop('disabled',false);
		$.post('/webGui/include/Download.php',{cmd:'delete',file:'<?= $zip;?>'});
	} else {
		setTimeout(cleanUp,4000);
	}
}  

function getlog() {
	$('input[value="Download"]').val('Downloading...').prop('disabled',true);
	$.post('/webGui/include/Download.php',{cmd:'save',source:'/var/log/syslog',file:'<?= $zip;?>'},function(zip) {
		location = zip;
		setTimeout(cleanUp,4000);
	});
}

window.addEventListener("load", function() {
	var logContainer = document.querySelector("pre.up");
	if (logContainer) {
		logContainer.scrollTop = logContainer.scrollHeight;
	}
});

$(function() {
	if ( typeof caPluginUpdateCheck === "function" ) {
		caPluginUpdateCheck("enhanced.log.plg");
	}

	<?if ($resize ?? false) { ?>
	function resize() {
	  $('pre.up').height(Math.max(window.innerHeight-330,370)).show();
	}

	resize();
	$(window).bind('resize',function(){resize();});
	<?}?>
});

<?if ($other_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $other_tag;?></span>");
<?}?>
<?if ($emhttp_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $emhttp_tag;?></span>");
<?}?>
<?if ($login_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $login_tag;?></span>");
<?}?>
<?if ($network_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $network_tag;?></span>");
<?}?>
<?if ($drive_related_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $drive_related_tag;?></span>");
<?}?>
<?if ($file_system_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $file_system_tag;?></span>");
<?}?>
<?if ($system_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $system_tag;?></span>");
<?}?>
<?if ($array_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $array_tag;?></span>");
<?}?>
<?if ($lime_tech_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $lime_tech_tag;?></span>");
<?}?>
<?if ($minor_issues_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $minor_issues_tag;?></span>");
<?}?>
<?if ($errors_color != "") { ?>
	$('.tabs').append("<span class='status'><?= $errors_tag;?></span>");
<?}?>
</script>

