<?php

namespace EnhancedLog;

try {
    require_once dirname(dirname(__FILE__)) . "/common.php";

    $tr = $tr ?? new Translator();

    $enhanced_log_cfg = Utils::getConfig();
    $colors           = new Colors();
    $colors->parseConfig($enhanced_log_cfg);

    $logReader = $logReader ?? new LogReader("/var/log/syslog");

    $rows = "";

    foreach ($logReader->getLogSummary() as $line) {
        $color = empty($line->getMatch()) ? "" : $colors->getColor($line->getMatch());

        if (strtolower($color) !== "skip") {
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
