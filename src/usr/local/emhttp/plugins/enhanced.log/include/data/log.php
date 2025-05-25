<?php

namespace EnhancedLog;

try {
    require_once dirname(dirname(__FILE__)) . "/common.php";

    $tr = $tr ?? new Translator();

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

    $colors = new Colors();
    $colors->parseConfig($enhanced_log_cfg);

    $log_lines = explode("\n", shell_exec("/usr/bin/cat /var/log/syslog | tail -n " . escapeshellarg(strval($display_lines))) ?: "");
    // $log_lines = array_reverse($log_lines);

    $rows     = "";
    $sequence = 1;

    foreach ($log_lines as $line) {
        if (empty($line)) {
            continue;
        }
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
            $data = explode(" ", $line);

            $service       = $data[4];
            $serviceFilter = str_contains($service, "[") ? substr($service, 0, strpos($service, "[") ?: null) : trim($service, ":");

            $service       = htmlspecialchars($service);
            $serviceFilter = htmlspecialchars($serviceFilter);

            $date   = htmlspecialchars(implode(" ", array_slice($data, 0, 3)));
            $source = htmlspecialchars($data[3]);

            $message = htmlspecialchars(implode(" ", array_slice($data, 5)));

            $matchType = isset($match[$i][1]) ? $colors->getColorName($match[$i][1], $tr) : "";

            $color = isset($match[$i][1]) ? $colors->getColor($match[$i][1]) : "";
            $rows .= <<<EOT
                <tr style='background-color:{$color}'>
                    <td data-index="{$sequence}">{$date}</td>
                    <td>{$source}</td>
                    <td data-text="{$serviceFilter}">{$service}</td>
                    <td>{$message}</td>
                    <td>{$matchType}</td>
                </tr>
                EOT;

            $sequence++;
        }
    }

    $output = <<<EOT
        <table id="logTable" class="unraid logTable">
            <thead>
                <tr>
                    <th>{$tr->tr("date")}</th>
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
