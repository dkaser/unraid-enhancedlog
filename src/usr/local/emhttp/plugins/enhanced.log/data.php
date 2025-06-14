<?php

namespace EDACerton\EnhancedLog;

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

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(false, true, true);

$app->get("{$prefix}/files", function (Request $request, Response $response, $args) {
    $logs    = Utils::getLogFiles();
    $payload = json_encode($logs) ?: "{}";
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/log", function (Request $request, Response $response, $args) {
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

    $colors = new Colors();
    $colors->parseConfig($enhanced_log_cfg);

    $tr = new Translator(PLUGIN_ROOT);

    $logReader = new LogReader($logFile, $maxLines);
    $payload   = array();

    foreach ($logReader->getLogLines() as $sequence => $line) {
        $color = empty($line->getMatch()) ? "" : $colors->getColor($line->getMatch());

        if (strtolower($color) === "skip" || ! preg_match("/\w+/", $line->getMessage())) {
            continue;
        }

        $matchType = empty($line->getMatch()) ? "" : $colors->getColorName($line->getMatch(), $tr);

        $payload[] = [
            'sequence'      => $sequence,
            'date'          => $line->getDate(),
            'service'       => $line->getService(),
            'serviceFilter' => $line->getServiceFilter(),
            'source'        => $line->getSource(),
            'message'       => $line->getMessage(),
            'matchType'     => $matchType,
            'color'         => $color
        ];
    }

    $response->getBody()->write(json_encode($payload) ?: "{}");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->get("{$prefix}/summary", function (Request $request, Response $response, $args) {
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

    $colors = new Colors();
    $colors->parseConfig($enhanced_log_cfg);

    $tr = new Translator(PLUGIN_ROOT);

    $logReader = new LogReader($logFile, $maxLines);
    $payload   = array();

    foreach ($logReader->getLogSummary() as $line) {
        $color = empty($line->getMatch()) ? "" : $colors->getColor($line->getMatch());

        if (strtolower($color) === "skip" || ! preg_match("/\w+/", $line->getMessage())) {
            continue;
        }

        $matchType = empty($line->getMatch()) ? "" : $colors->getColorName($line->getMatch(), $tr);

        $payload[] = [
            'count'     => $line->getCount(),
            'service'   => $line->getService(),
            'source'    => $line->getSource(),
            'message'   => $line->getMessage(),
            'matchType' => $matchType,
            'color'     => $color
        ];
    }

    $response->getBody()->write(json_encode($payload) ?: "{}");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-store')
        ->withStatus(200);
});

$app->run();
