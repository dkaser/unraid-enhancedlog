<?php

namespace EDACerton\EnhancedLog;

/*
    Copyright 2015-2025, Dan Landon
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

class LogReader
{
    private string $logFile;
    private int $maxLines;
    /** @var array<string, string> $config */
    private array $config;
    private Colors $colors;
    /** @var array<LogMatch> $matches */
    private array $matches;
    /** @var array<int, LogLine> $logLines */
    private array $logLines;
    /** @var array<string, LogSummary> $logSummary */
    private array $logSummary;

    public function __construct(string $logFile, int $maxLines = 1000)
    {
        if ( ! defined(__NAMESPACE__ . '\PLUGIN_ROOT') || ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
            throw new \RuntimeException("Common file not loaded.");
        }
        $utils = new Utils(PLUGIN_NAME);

        $validLogs = Utils::getLogFiles();
        if ( ! in_array($logFile, $validLogs)) {
            throw new \InvalidArgumentException("Invalid log file: {$logFile}");
        }

        $this->logFile = $logFile;

        $this->config = Utils::getConfig();

        $this->maxLines = $maxLines;

        $this->colors = new Colors();
        $this->colors->parseConfig($this->config);

        $matchConfig = file('plugins/enhanced.log/syslog_match.conf') ?: array();
        if ($this->config['OTHER'] == "yes") {
            $custom      = file('/boot/config/plugins/enhanced.log/custom_syslog.conf') ?: array();
            $matchConfig = array_merge($matchConfig, $custom);
        }

        $match = array();
        foreach ($matchConfig as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            $split = str_getcsv($line);
            if (count($split) < 2) {
                $utils->logmsg("Invalid match line: {$line}");
                continue;
            }

            $regex = $split[0];
            $color = $split[1];

            if ( ! empty($regex) && ! empty($color)) {
                $match[] = new LogMatch($regex, $color);
            }
        }

        $this->matches = $match;

        $this->logLines   = $this->readLogLines();
        $this->logSummary = $this->countLogs();
    }

    /**
     * @return array<string>
     */
    private static function readFile(string $filename, int $maxLines): array
    {
        return explode("\n", shell_exec("tail -n " . escapeshellarg(strval($maxLines)) . " " . escapeshellarg($filename)) ?: "");
    }

    /**
     * @return array<int, LogLine>
     */
    private function readLogLines(): array
    {
        $retval     = array();
        $logContent = self::readFile($this->logFile, $this->maxLines);
        $rotateLog  = 1;
        while ((count($logContent) < $this->maxLines) && $rotateLog <= 9) {
            $rotatedFile = $this->logFile . "." . strval($rotateLog);
            if (file_exists($rotatedFile)) {
                $rotatedContent = self::readFile($rotatedFile, $this->maxLines - count($logContent));
                $logContent     = array_merge($rotatedContent, $logContent);
            }
            $rotateLog++;
        }

        foreach ($logContent as $line) {
            if (empty($line)) {
                continue;
            }

            $match = "";
            foreach ($this->matches as $test) {
                $regex = $test->getRegex();
                if (preg_match("/{$regex}/i", $line)) {
                    $match = $test->getMatch();
                    break;
                }
            }

            if ($match === "" && ($this->config['TEXT'] !== "yes")) {
                $match = "skip";
            }

            $retval[] = new LogLine(
                $line,
                $match
            );
        }

        return $retval;
    }

    /**
     * @return array<int, LogLine>
     */
    public function getLogLines(): array
    {
        return $this->logLines;
    }

    /**
     * @return array<string, LogSummary>
     */
    private function countLogs(): array
    {
        $retval = array();

        foreach ($this->logLines as $line) {
            if (array_key_exists($line->getMessageChecksum(), $retval)) {
                $retval[$line->getMessageChecksum()]->incrementCount();
            } else {
                $summary = new LogSummary(
                    $line->getSource(),
                    $line->getServiceHash(),
                    $line->getMessageHash(),
                    $line->getMatch()
                );
                $retval[$line->getMessageChecksum()] = $summary;
            }
        }

        return $retval;
    }

    /**
     * @return array<string, LogSummary>
     */
    public function getLogSummary(): array
    {
        return $this->logSummary;
    }
}
