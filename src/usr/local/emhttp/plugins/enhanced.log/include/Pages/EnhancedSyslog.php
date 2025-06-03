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
$maxLines         = intval(isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);
$colors           = new Colors();
$colors->parseConfig($enhanced_log_cfg);

$themeArray = array('white', 'black');
if (in_array($theme ?? "", $themeArray)) {
    $font_size = '10pt';
} else {
    $font_size = '14pt';
}

?>
<link type="text/css" rel="stylesheet" href="/plugins/enhanced.log/assets/style.css">
<script src="/webGui/javascript/jquery.tablesorter.widgets.js"></script>

<!-- Select2 code -->
<link href="/plugins/enhanced.log/assets/select2.min.css" rel="stylesheet">
<script src="/plugins/enhanced.log/assets/select2.min.js"></script>
<script src="/plugins/enhanced.log/assets/widget-filter-formatter-select2.js"></script>

<div>
<select name="logLog" onchange='showLog()'>
<?php foreach ($logs as $file) {
    echo Utils::make_option(false, $file, basename($file));
} ?>
</select>
<?= $tr->tr("max_lines"); ?>:
<input type="number" name="logMax" value="" placeholder="<?= $maxLines; ?>">
<input type="button" value="<?= $tr->tr("refresh"); ?>" onclick="showLog()">
<input type="button" class="reset" value="<?= $tr->tr("reset"); ?>">
</div>

<table id='logTable' class="unraid logTable tablesorter"><tr><td><div class="spinner"></div></td></tr></table><br>

<script>
function showLog() {
  var log = $('select[name="logLog"]').val();
  var maxLines = $('input[name="logMax"]').val();
  $.post('/plugins/enhanced.log/include/data/log.php', {log: log, maxLines: maxLines}, function(data){
    clearTimeout(timers.refresh);
    $("#logTable").trigger("destroy");
    $('#logTable').html(data.html);
    $('#logTable').tablesorter({
      widthFixed : true,
      sortList: [[0,1]],
      widgets: ['stickyHeaders','filter','zebra'],
      headers: {
        0: { sorter: 'data' }, // date
      },
      widgetOptions: {
        // on black and white, offset is height of #menu
        // on azure and gray, offset is height of #header
        stickyHeaders_offset: ($('#menu').height() < 50) ? $('#menu').height() : $('#header').height(),
        filter_columnFilters: true,
        filter_reset: '.reset',
        filter_liveSearch: true,
        filter_formatter : {
            // Alphanumeric (match)
            1: function($cell, indx) {
            return $.tablesorter.filterFormatter.select2( $cell, indx, {
                match : true,         // adds "filter-match" to header
                multiple: true,      // allow multiple selections
                width: '166px',
                allowClear: true,
                dropdownAutoWidth: true,
            });
            },
            2: function($cell, indx) {
            return $.tablesorter.filterFormatter.select2( $cell, indx, {
                match : false,         // adds "filter-match" to header
                multiple: true,      // allow multiple selections
                width: '166px',
                allowClear: true,
                dropdownAutoWidth: true,
            });
            },
            4: function($cell, indx) {
            return $.tablesorter.filterFormatter.select2( $cell, indx, {
                match : true,         // adds "filter-match" to header
                multiple: true,      // allow multiple selections
                width: '166px',
                allowClear: true,
                dropdownAutoWidth: true,
            });
            },
        },
        zebra: ["normal-row","alt-row"]
      },

    });
    $('div.spinner.fixed').hide('fast');
    //controlsDisabled(false);
  }, "json");
}

$(function() {
  $.tablesorter.addParser({
    // set a unique id
    id: 'data',
    is: function(s, table, cell, $cell) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      var $cell = $(cell);
      return $cell.attr('data-index') || s;
    },
    // flag for filter widget (true = ALWAYS search parsed values; false = search cell text)
    parsed: false,
    // set type, either numeric or text
    type: 'numeric'
  });

  showLog();
});

<?php foreach ($colors->getColors() as $color) { ?>
$('.tabs').append("<?= $colors->getColorTag($color, $font_size, $tr); ?>");
<?php } ?>

</script>

