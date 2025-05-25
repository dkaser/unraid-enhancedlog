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

$enhanced_log_cfg = Utils::getConfig();

if ( ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
    throw new \RuntimeException("PLUGIN_NAME not defined");
}

$usage_cfg     = parse_ini_file("/boot/config/plugins/" . PLUGIN_NAME . "/usage.cfg", false, INI_SCANNER_RAW) ?: array();
$usage_allowed = $usage_cfg['usage_allowed'] ?? "yes";
?>

<table class="tablesorter shift ups">
<thead><tr><th><?= $tr->tr("configuration.syslog_config"); ?></th></tr></thead>
</table>

<div style="width: 100%; float:left">
	<form markdown="1" name="enhanced_log_settings" method="POST" action="/update.php" target="progressFrame">
	<input type="hidden" name="#file" value="enhanced.log/enhanced.log.cfg">

	<dl>
        <dt><?= $tr->tr("configuration.show_all_lines"); ?></dt>
        <dd>
			<select name="TEXT" size="1">
				<?= Utils::make_option($enhanced_log_cfg['TEXT'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['TEXT'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to show all lines in the enhanced log.  Set to **No** to show only highlighted lines.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.number_of_lines"); ?></dt>
        <dd>
            <input type="text" name="LINES" class="narrow" maxlength="4" value="<?= htmlspecialchars($enhanced_log_cfg['LINES']);?>" placeholder="1000">
        </dd>
    </dl>
    <blockquote class='inline_help'>Number of lines to display in the Enhanced Syslog.  Default is 1000.</blockquote>

	<dl>
        <dt><?= $tr->tr("configuration.error_highlight"); ?></dt>
        <dd>
            <select name="ERRORS" size="1">
				<?= Utils::make_option($enhanced_log_cfg['ERRORS'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['ERRORS'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Errors** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.minor_highlight"); ?></dt>
        <dd>
            <select name="MINOR_ISSUES" size="1">
				<?= Utils::make_option($enhanced_log_cfg['MINOR_ISSUES'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['MINOR_ISSUES'], "no", $tr->tr("no"));?>
	  		</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Minor Issues** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.lime_highlight"); ?></dt>
        <dd>
            <select name="LIME_TECH" size="1">
				<?= Utils::make_option($enhanced_log_cfg['LIME_TECH'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['LIME_TECH'], "no", $tr->tr("no"));?>
	  		</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Lime Tech** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.array_highlight"); ?></dt>
        <dd>
            <select name="ARRAY" size="1">
				<?= Utils::make_option($enhanced_log_cfg['ARRAY'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['ARRAY'], "no", $tr->tr("no"));?>
	  		</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Array** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.system_highlight"); ?></dt>
        <dd>
            <select name="SYSTEM" size="1">
				<?= Utils::make_option($enhanced_log_cfg['SYSTEM'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['SYSTEM'], "no", $tr->tr("no"));?>
	  		</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **System** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.file_highlight"); ?></dt>
        <dd>
            <select name="FILE_SYSTEM" size="1">
				<?= Utils::make_option($enhanced_log_cfg['FILE_SYSTEM'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['FILE_SYSTEM'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **File System** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.drive_highlight"); ?></dt>
        <dd>
            <select name="DRIVE_RELATED" size="1">
				<?= Utils::make_option($enhanced_log_cfg['DRIVE_RELATED'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['DRIVE_RELATED'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Drive Related** highlighting in the enhanced log.x</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.network_highlight"); ?></dt>
        <dd>
            <select name="NETWORK" size="1">
				<?= Utils::make_option($enhanced_log_cfg['NETWORK'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['NETWORK'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Network** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.login_highlight"); ?></dt>
        <dd>
            <select name="LOGIN" size="1">
				<?= Utils::make_option($enhanced_log_cfg['LOGIN'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['LOGIN'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Login** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.emhttp_highlight"); ?></dt>
        <dd>
            <select name="EMHTTP" size="1">
				<?= Utils::make_option($enhanced_log_cfg['EMHTTP'], "yes", $tr->tr("yes"));?>
				<?= Utils::make_option($enhanced_log_cfg['EMHTTP'], "no", $tr->tr("no"));?>
			</select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **emhttp** highlighting in the enhanced log.</blockquote>

    <dl>
        <dt><?= $tr->tr("configuration.other_highlight"); ?></dt>
        <dd>
            <select name="OTHER" size="1">
		<?= Utils::make_option($enhanced_log_cfg['OTHER'], "yes", $tr->tr("yes"));?>
		<?= Utils::make_option($enhanced_log_cfg['OTHER'], "no", $tr->tr("no"));?>
	  </select>
        </dd>
    </dl>
    <blockquote class='inline_help'>Set to **Yes** to enable **Other** highlighting in the enhanced log.</blockquote>

	<dl>
		<dt>
			<input type="submit" name="#default" value="<?= $tr->tr("default"); ?>">
		</dt>
		<dd>
			<input type="submit" value='<?= $tr->tr("apply"); ?>'>
			<input type="button" value="<?= $tr->tr("done"); ?>" onclick="done()">
		</dd>
	</dl>
    
	</form>
</div>

