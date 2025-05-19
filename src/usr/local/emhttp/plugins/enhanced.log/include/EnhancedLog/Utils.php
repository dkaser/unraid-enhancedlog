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

class Utils
{
    public static function logmsg(string $message): void
    {
        if ( ! defined(__NAMESPACE__ . "\PLUGIN_NAME")) {
            throw new \RuntimeException("PLUGIN_NAME not defined");
        }

        $timestamp = date('Y/m/d H:i:s');
        $filename  = basename(is_string($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : "");
        file_put_contents("/var/log/" . PLUGIN_NAME . ".log", "{$timestamp} {$filename}: {$message}" . PHP_EOL, FILE_APPEND);
    }

    public static function auto_v(string $file): string
    {
        global $docroot;
        $path = $docroot . $file;
        clearstatcache(true, $path);
        $time    = file_exists($path) ? filemtime($path) : 'autov_fileDoesntExist';
        $newFile = "{$file}?v=" . $time;

        return $newFile;
    }

    public static function make_option(bool|string $selected, string $value, string $text, string $extra = ""): string
    {
        if (is_string($selected)) {
            $selected = filter_var($selected, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        return "<option value='{$value}'" . ($selected ? " selected" : "") . (strlen($extra) ? " {$extra}" : "") . ">{$text}</option>";
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
