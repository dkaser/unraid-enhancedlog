<?php

namespace EDACerton\EnhancedLog;

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

class Utils extends \EDACerton\PluginUtils\Utils
{
    /**
     * @return array<string>
     */
    public static function getLogFiles(): array
    {
        $logs = ['/var/log/syslog'];

        if (file_exists('/boot/logs/syslog-previous')) {
            // add syslog-previous to front of logs array
            $logs[] = '/boot/logs/syslog-previous';
        }

        if (file_exists('/boot/config/rsyslog.cfg')) {
            $syslog = parse_ini_file('/boot/config/rsyslog.cfg');
            if ( ! empty($syslog['local_server']) && ! empty($syslog['server_folder']) && $arrayLogs = glob($syslog['server_folder'] . '/syslog-*.log', GLOB_NOSORT)) {
                natsort($arrayLogs);
                $logs = array_merge($logs, $arrayLogs);
            }
        }

        return $logs;
    }

    /**
     * @return array<string, string>
     */
    public static function getConfig(): array
    {
        // Assign events and colors from configuration file.
        if ( ! defined(__NAMESPACE__ . "\PLUGIN_ROOT")) {
            throw new \RuntimeException("PLUGIN_ROOT not defined");
        }

        $user_log_cfg       = parse_ini_file("/boot/config/plugins/enhanced.log/enhanced.log.cfg", false, INI_SCANNER_RAW) ?: array();
        $default_log_config = parse_ini_file(PLUGIN_ROOT . "/default/enhanced.log.cfg", false, INI_SCANNER_RAW) ?: array();
        $enhanced_log_cfg   = array_merge($default_log_config, $user_log_cfg);

        return $enhanced_log_cfg;
    }
}
