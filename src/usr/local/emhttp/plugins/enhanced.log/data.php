<?php

namespace EDACerton\EnhancedLog;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use EDACerton\PluginUtils\Translator;

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

require_once dirname(__FILE__) . "/include/common.php";

$prefix = "/plugins/enhanced.log/data.php";

if ( ! defined(__NAMESPACE__ . '\PLUGIN_ROOT') || ! defined(__NAMESPACE__ . '\PLUGIN_NAME')) {
    throw new \RuntimeException("Common file not loaded.");
}
$tr = $tr ?? new Translator(PLUGIN_ROOT);

$logger = new Logger('enhanced.log');
$logger->pushHandler(new StreamHandler('/var/log/enhanced.log.slim.log', Level::Debug));

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(false, true, true, $logger);

$app->get("{$prefix}/files", function (Request $request, Response $response, $args) {
    $logs    = Utils::getLogFiles();
    $payload = json_encode($logs) ?: "{}";
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/pluginFiles", function (Request $request, Response $response, $args) {
    $plugins = new Plugins();
    $logs    = $plugins->getFiles();
    $payload = json_encode($logs) ?: "[]";
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/pluginLog", function (Request $request, Response $response, $args) {
    // Get the log file name from the query parameters
    $body = (array) $request->getQueryParams();

    $payload = array();

    // Get the log file via Plugins class
    $plugins = new Plugins();
    $logData = $plugins->getFile($body['log'] ?? '');

    // Split the log data into lines, then add each line to the payload with its sequence number
    if ($logData !== null) {
        $lines = explode("\n", $logData);
        foreach ($lines as $sequence => $line) {
            if (trim($line) !== '') { // Skip empty lines
                $payload[] = [
                    'sequence' => $sequence,
                    'line'     => trim($line)
                ];
            }
        }
    }
    $response->getBody()->write(json_encode($payload) ?: "[]");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/log", function (Request $request, Response $response, $args) use ($logger) {
    $body = (array) $request->getQueryParams();

    $enhanced_log_cfg = Utils::getConfig();

    if ( ! empty($body['log']) && in_array($body['log'], Utils::getLogFiles(), true)) {
        $logFile = $body['log'];
    } else {
        $logFile = "/var/log/syslog";
    }

    $maxLines = intval(isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);
    if ($maxLines < 1) {
        $maxLines = 1000;
    }

    $colors = new Colors($logger);
    $colors->parseConfig($enhanced_log_cfg);

    $tr = new Translator(PLUGIN_ROOT);

    $logReader = new LogReader($logFile, $maxLines);
    $payload   = array();

    foreach ($logReader->getLogLines() as $sequence => $line) {
        $match = strtolower($line->getMatch());

        // Skip lines with a custom match of "skip"
        if ($match === "skip") {
            continue;
        }

        $color     = empty($match) ? "" : $colors->getColor($match);
        $textColor = empty($match) ? "" : $colors->getTextColor($match);

        if (strtolower($color) === "skip" || ! preg_match("/\w+/", $line->getMessage())) {
            continue;
        }

        $matchType = empty($match) ? "" : $colors->getColorName($match, $tr);

        $payload[] = [
            'sequence'      => $sequence,
            'date'          => $line->getDate(),
            'service'       => $line->getService(),
            'serviceFilter' => $line->getServiceFilter(),
            'source'        => $line->getSource(),
            'message'       => $line->getMessage(),
            'matchType'     => $matchType,
            'color'         => $color,
            'textColor'     => $textColor
        ];
    }

    $response->getBody()->write(json_encode($payload) ?: "{}");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/summary", function (Request $request, Response $response, $args) use ($logger) {
    $body = (array) $request->getQueryParams();

    $enhanced_log_cfg = Utils::getConfig();

    if ( ! empty($body['log']) && in_array($body['log'], Utils::getLogFiles(), true)) {
        $logFile = $body['log'];
    } else {
        $logFile = "/var/log/syslog";
    }

    $maxLines = intval(isset($enhanced_log_cfg['LINES']) && $enhanced_log_cfg['LINES'] != "" ? $enhanced_log_cfg['LINES'] : 1000);
    if ($maxLines < 1) {
        $maxLines = 1000;
    }

    $colors = new Colors($logger);
    $colors->parseConfig($enhanced_log_cfg);

    $tr = new Translator(PLUGIN_ROOT);

    $logReader = new LogReader($logFile, $maxLines);
    $payload   = array();

    foreach ($logReader->getLogSummary() as $line) {
        $match = strtolower($line->getMatch());

        // Skip lines with a custom match of "skip"
        if ($match === "skip") {
            continue;
        }

        $color     = empty($match) ? "" : $colors->getColor($match);
        $textColor = empty($match) ? "" : $colors->getTextColor($match);

        if (strtolower($color) === "skip" || ! preg_match("/\w+/", $line->getMessage())) {
            continue;
        }

        $matchType = empty($match) ? "" : $colors->getColorName($match, $tr);

        $payload[] = [
            'count'     => $line->getCount(),
            'service'   => $line->getService(),
            'source'    => $line->getSource(),
            'message'   => $line->getMessage(),
            'matchType' => $matchType,
            'color'     => $color,
            'textColor' => $textColor
        ];
    }

    $response->getBody()->write(json_encode($payload) ?: "{}");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/locales", function (Request $request, Response $response, $args) {
    // Get the list of supported locales from /locales (each locale is a JSON file)
    $localesDir = PLUGIN_ROOT . '/locales';
    $files      = scandir($localesDir);

    foreach ($files as $key => $file) {
        if ( ! is_file($localesDir . '/' . $file) || pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
            unset($files[$key]);
        } else {
            $files[$key] = basename($file, '.json'); // Store only the locale name without extension
        }
    }

    $response->getBody()->write(json_encode($files) ?: "[]");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->run();
