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

$enhanced_log_cfg = Utils::getConfig();
?>

<table class="tablesorter shift ups">
<thead><tr><th><?= $tr->tr("colors.syslog_colors"); ?></th></tr></thead>
</table>

<div style="width: 100%; float:left">
	<form markdown="1" name="enhanced_log_settings" method="POST" action="/update.php" target="progressFrame">
	<input type="hidden" name="#file" value="enhanced.log/enhanced.log.cfg">
	<span style="float:right;margin-right:10px"><a href="http://www.w3schools.com/colors/colors_names.asp" target="_blank" title="Color Selection Chart"> <u><?= $tr->tr("colors.color_chart"); ?></u></a></span>

    <dl>
        <dt><?= $tr->tr("colors.error"); ?></dt>
        <dd>
            <input type="text" name="ERRORS_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['ERRORS_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Error** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.minor"); ?></dt>
        <dd>
            <input type="text" name="MINOR_ISSUES_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['MINOR_ISSUES_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Minor Issues** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.limetech"); ?></dt>
        <dd>
            <input type="text" name="LIME_TECH_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['LIME_TECH_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Lime Tech** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.array"); ?></dt>
        <dd>
            <input type="text" name="ARRAY_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['ARRAY_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Array** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.system"); ?></dt>
        <dd>
            <input type="text" name="SYSTEM_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['SYSTEM_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **System** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.file"); ?></dt>
        <dd>
            <input type="text" name="FILE_SYSTEM_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['FILE_SYSTEM_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **File System** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.drive"); ?></dt>
        <dd>
            <input type="text" name="DRIVE_RELATED_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['DRIVE_RELATED_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Drive Related** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.network"); ?></dt>
        <dd>
            <input type="text" name="NETWORK_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['NETWORK_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Network** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.login"); ?></dt>
        <dd>
            <input type="text" name="LOGIN_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['LOGIN_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Login** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.emhttp"); ?></dt>
        <dd>
            <input type="text" name="EMHTTP_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['EMHTTP_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **emhttp** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.other"); ?></dt>
        <dd>
            <input type="text" name="OTHER_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($enhanced_log_cfg['OTHER_CLR']);?>">
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Other** highlighting.  You can use the color name or number.  The color number begins with a '#'.</blockquote>

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
