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

class LogLine
{
    private string $raw;
    private string $match;
    private string $service;
    private string $serviceFilter;
    private string $date;
    private string $source;
    private string $message;

    private string $serviceHash;
    private string $messageHash;
    private string $messageChecksum;

    public function __construct(string $raw, string $match = "")
    {
        $this->raw   = $raw;
        $this->match = $match;

        $data = (array) preg_split('/ +/', $raw);

        $this->service       = $data[4] ?: "";
        $this->serviceFilter = str_contains($this->service, "[") ? substr($this->service, 0, strpos($this->service, "[") ?: null) : trim($this->service, ":");

        $this->date   = implode(" ", array_slice($data, 0, 3));
        $this->source = $data[3] ?: "";

        $this->message     = implode(" ", array_slice($data, 5));
        $this->messageHash = self::hashValue($this->message);
        $this->serviceHash = self::hashValue($this->service);

        $this->messageChecksum = md5($this->source . $this->messageHash . $this->serviceHash);
    }

    private static function hashValue(string $value): string
    {
        $filters = [
            '/(?<=^|\W)(\d+)(?=\W|$)/',
            '/(?<=br-|eth)([a-f0-9]+)/'
        ];

        $value = preg_replace($filters, "#", $value);

        return $value ?? "";
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function getMatch(): string
    {
        return $this->match;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getServiceFilter(): string
    {
        return $this->serviceFilter;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMessageHash(): string
    {
        return $this->messageHash;
    }

    public function getServiceHash(): string
    {
        return $this->serviceHash;
    }

    public function getMessageChecksum(): string
    {
        return $this->messageChecksum;
    }
}
