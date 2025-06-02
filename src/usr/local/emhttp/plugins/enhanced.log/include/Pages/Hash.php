<?php

namespace EnhancedLog;

/*
    Copyright (C) 2025  Derek Kaser

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

$logs             = Utils::getLogFiles();
$enhanced_log_cfg = Utils::getConfig();
$maxLines         = intval(isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);

?>

<div>
<select name="hashLog" onchange='showLogHashes()'>
<?php foreach ($logs as $file) {
    echo Utils::make_option(false, $file, basename($file));
} ?>
</select>
<?= $tr->tr("max_lines"); ?>:
<input type="number" name="hashMax" value="" placeholder="<?= $maxLines; ?>">
<input type="button" value="<?= $tr->tr("refresh"); ?>" onclick="showLogHashes()">
<input type="button" class="resetHash" value="<?= $tr->tr("reset"); ?>">
</div>

<table id='hashTable' class="unraid hashTable tablesorter"><tr><td><div class="spinner"></div></td></tr></table><br>

<script>
function showLogHashes() {
  var log = $('select[name="hashLog"]').val();
  var maxLines = $('input[name="hashMax"]').val();
  $.post('/plugins/enhanced.log/include/data/hash.php',{log: log, maxLines: maxLines},function(data){
    clearTimeout(timers.refresh);
    $("#hashTable").trigger("destroy");
    $('#hashTable').html(data.html);
    $('#hashTable').tablesorter({
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
        filter_reset: '.resetHash',
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
  showLogHashes();
});
</script>