<?php

namespace EDACerton\EnhancedLog;

use EDACerton\PluginUtils\Translator;

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

if ( ! defined(__NAMESPACE__ . '\PLUGIN_ROOT') || ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
    throw new \RuntimeException("Common file not loaded.");
}

$tr = $tr ?? new Translator(PLUGIN_ROOT);

$logs = Utils::getLogFiles();

$enhanced_log_cfg = Utils::getConfig();
$colors           = new Colors();
$colors->parseConfig($enhanced_log_cfg);

$themeArray = array('white', 'black');
if (in_array($theme ?? "", $themeArray)) {
    $font_size = '10pt';
} else {
    $font_size = '14pt';
}

?>

<script>
const logFiles = <?= json_encode(
    array_combine(array_map('basename', $logs), $logs),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
); ?>;
</script>

<script src="/plugins/enhanced.log/assets/translate.js"></script>
<script>
    const translator = new Translator("/plugins/enhanced.log");
</script>

<link type="text/css" rel="stylesheet" href="/plugins/enhanced.log/assets/style.css">
<script src="/plugins/enhanced.log/assets/datatables.min.js"></script>
<script src="/plugins/enhanced.log/assets/luxon.min.js"></script>
<script src="/plugins/enhanced.log/assets/flatpickr.min.js"></script>
<link rel="stylesheet" href="/plugins/enhanced.log/assets/flatpickr.min.css">

<script src="/plugins/enhanced.log/assets/enhancedlog.js"></script>
<link type="text/css" rel="stylesheet" href="/plugins/enhanced.log/assets/datatables.min.css">

<table id='logTable' class="stripe compact">
    <thead>
        <tr>
            <th><strong><?= $tr->tr("date"); ?></strong></th>
            <th><strong><?= $tr->tr("source"); ?></strong></th>
            <th><strong><?= $tr->tr("service"); ?></strong></th>
            <th><strong><?= $tr->tr("message"); ?></strong></th>
            <th><strong><?= $tr->tr("match"); ?></strong></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
    </tfoot>
</table>

<script>
$(document).ready( async function () {
    await translator.init();
    $('#logTable').DataTable(getDatatableConfig('/plugins/enhanced.log/data.php/log'));
    $('#summaryTable').DataTable(getSummaryConfig('/plugins/enhanced.log/data.php/summary'));
} );

<?php foreach ($colors->getColors() as $color) { ?>
$('.tabs').append("<?= $colors->getColorTag($color, $font_size, $tr); ?>");
<?php } ?>

</script>

