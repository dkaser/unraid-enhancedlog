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
$colors           = new Colors();
$colors->parseConfig($enhanced_log_cfg, true);

?>

<script src="/plugins/enhanced.log/assets/jscolor.js"></script>
<script src="/plugins/enhanced.log/assets/ac-colors.min.js"></script>

<script>
// These options apply to all color pickers on the page
jscolor.presets.default = {
	format:'hex', palette:'#32CD32, #F08080', paletteSetsAlpha:false, 
	hideOnPaletteClick:true, alphaChannel:false
};

function updateSample(colorPicker) {
  const color = colorPicker.jscolor.toHEXString();
  const target = document.getElementById(colorPicker.name + '_SAMPLE');

  let colorHsl = new Color({"color": color, "type": "hex"});
  let hsl = colorHsl.hsl;

  if(hsl[2] < 50) {
    hsl[2] = 80;
  } else {
    hsl[2] = 20;
  }

  colorHsl.hsl = hsl;

  target.style.backgroundColor = color;
  target.style.color = colorHsl.hexString;
}

$(document).ready( async function () {
    // Initialize the color samples
    const colorInputs = document.querySelectorAll('input[data-jscolor]');
    colorInputs.forEach(input => {
        const sampleSpan = document.getElementById(input.name + '_SAMPLE');
        if (sampleSpan) {
            updateSample(input);
        }
    });
} );
</script>

<style>
    .sampleText {
        padding-left: 20px;
        padding-right:20px;
    }
</style>

<table class="tablesorter shift ups">
<thead><tr><th><?= $tr->tr("colors.syslog_colors"); ?></th></tr></thead>
</table>

<div style="width: 100%; float:left">
	<form markdown="1" name="enhanced_log_settings" method="POST" action="/update.php" target="progressFrame">
	<input type="hidden" name="#file" value="enhanced.log/enhanced.log.cfg">

    <dl>
        <dt><?= $tr->tr("colors.error"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="ERRORS_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('error')); ?>">
            <span class="sampleText" id="ERRORS_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Error** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.minor"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="MINOR_ISSUES_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('minor issue')); ?>">
            <span class="sampleText" id="MINOR_ISSUES_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Minor Issues** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.limetech"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="LIME_TECH_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('lime tech')); ?>">
            <span class="sampleText" id="LIME_TECH_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Lime Tech** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.array"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="ARRAY_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('array')); ?>">
            <span class="sampleText" id="ARRAY_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Array** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.system"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="SYSTEM_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('system')); ?>">
            <span class="sampleText" id="SYSTEM_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **System** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.file"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="FILE_SYSTEM_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('file system')); ?>">
            <span class="sampleText" id="FILE_SYSTEM_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **File System** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.drive"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="DRIVE_RELATED_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('drive related')); ?>">
            <span class="sampleText" id="DRIVE_RELATED_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Drive Related** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.network"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="NETWORK_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('network')); ?>">
            <span class="sampleText" id="NETWORK_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Network** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.login"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="LOGIN_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('login')); ?>">
            <span class="sampleText" id="LOGIN_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Login** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.emhttp"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="EMHTTP_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('emhttp')); ?>">
            <span class="sampleText" id="EMHTTP_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **emhttp** highlighting.</blockquote>

    <dl>
        <dt><?= $tr->tr("colors.other"); ?></dt>
        <dd>
            <input data-jscolor="" onInput="updateSample(this)" type="text" name="OTHER_CLR" class="narrow" maxlength="20" value="<?= htmlspecialchars($colors->getColor('other')); ?>">
            <span class="sampleText" id="OTHER_CLR_SAMPLE"><?= $tr->tr("sample"); ?></span>
        </dd>
    </dl>
    <blockquote class='inline_help'>Color to use for **Other** highlighting.</blockquote>

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
