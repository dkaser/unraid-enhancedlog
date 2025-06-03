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

class LogMatch
{
    private string $regex;
    private string $match;

    public function __construct(string $regex, string $match)
    {
        $this->regex = $regex;
        $this->match = $match;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function getMatch(): string
    {
        return $this->match;
    }
}
