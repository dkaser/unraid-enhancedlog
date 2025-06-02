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

class LogSummary
{
    private int $count = 1;
    private string $source;
    private string $service;
    private string $message;
    private string $match;

    public function __construct(string $source, string $service, string $message, string $match)
    {
        $this->source  = $source;
        $this->service = $service;
        $this->message = $message;
        $this->match   = $match;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function incrementCount(): void
    {
        $this->count++;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMatch(): string
    {
        return $this->match;
    }
}
