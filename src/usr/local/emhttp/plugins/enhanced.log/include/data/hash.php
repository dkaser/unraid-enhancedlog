<?php

namespace EDACerton\EnhancedLog;

use EDACerton\PluginUtils\Translator;

try {
    require_once dirname(dirname(__FILE__)) . "/common.php";

    if ( ! defined(__NAMESPACE__ . '\PLUGIN_ROOT') || ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
        throw new \RuntimeException("Common file not loaded.");
    }

    $tr = $tr ?? new Translator(PLUGIN_ROOT);

    $enhanced_log_cfg = Utils::getConfig();
    $colors           = new Colors();
    $colors->parseConfig($enhanced_log_cfg);

    $maxLines = intval(isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);

    $logFile  = isset($_POST['log'])      && is_string($_POST['log']) ? $_POST['log'] : "/var/log/syslog";
    $maxLines = isset($_POST['maxLines']) && is_numeric($_POST['maxLines']) ? intval($_POST['maxLines']) : $maxLines;

    $logReader = $logReader ?? new LogReader($logFile, $maxLines);

    $rows = "";

    foreach ($logReader->getLogSummary() as $line) {
        $color = empty($line->getMatch()) ? "" : $colors->getColor($line->getMatch());

        if (strtolower($color) === "skip" || ! preg_match("/\w+/", $line->getMessage())) {
            continue;
        }

        $service = htmlspecialchars($line->getService());
        $count   = htmlspecialchars($line->getCount());
        $source  = htmlspecialchars($line->getSource());
        $message = htmlspecialchars($line->getMessage());

        $matchType = empty($line->getMatch()) ? "" : $colors->getColorName($line->getMatch(), $tr);

        $rows .= <<<EOT
            <tr style='background-color:{$color}'>
                <td>{$count}</td>
                <td>{$source}</td>
                <td>{$service}</td>
                <td>{$message}</td>
                <td>{$matchType}</td>
            </tr>
            EOT;
    }

    $output = <<<EOT
        <table id="hashTable" class="unraid hashTable">
            <thead>
                <tr>
                    <th class="filter-false">{$tr->tr("count")}</th>
                    <th class="filter-onlyAvail">{$tr->tr("source")}</th>
                    <th class="filter-onlyAvail">{$tr->tr("service")}</th>
                    <th>{$tr->tr("message")}</th>
                    <th class="filter-onlyAvail">{$tr->tr("match")}</th>
                </tr>
            </thead>
            <tbody>
                {$rows}
            </tbody>
        </table>
        EOT;

    $rtn         = array();
    $rtn['html'] = $output;
    echo json_encode($rtn);
} catch (\Throwable $e) {
    file_put_contents("/var/log/enhanced.log-error.log", print_r($e, true) . PHP_EOL, FILE_APPEND);
    echo "{}";
}
