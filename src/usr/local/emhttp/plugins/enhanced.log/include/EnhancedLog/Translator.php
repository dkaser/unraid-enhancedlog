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

class Translator
{
    /** @var array<string, string> $lang */
    private array $lang;

    public function __construct()
    {
        global $login_locale;

        if ( ! defined(__NAMESPACE__ . "\PLUGIN_ROOT")) {
            throw new \RuntimeException("PLUGIN_ROOT not defined");
        }

        $dynamix = parse_ini_file('/boot/config/plugins/dynamix/dynamix.cfg', true) ?: array();

        $locale        = $_SESSION['locale'] ?? ($login_locale ?? ($dynamix['display']['locale'] ?? "none"));
        $plugin_locale = (array) json_decode(file_get_contents(PLUGIN_ROOT . "/locales/en_US.json") ?: "{}", true);

        if (file_exists(PLUGIN_ROOT . "/locales/{$locale}.json")) {
            $current_locale = (array) json_decode(file_get_contents(PLUGIN_ROOT . "/locales/{$locale}.json") ?: "{}", true);
            $plugin_locale  = array_replace_recursive($plugin_locale, $current_locale);
        }

        $ritit = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($plugin_locale));
        $lang  = array();
        foreach ($ritit as $leafValue) {
            $keys = array();
            foreach (range(0, $ritit->getDepth()) as $depth) {
                $keys[] = $ritit->getSubIterator($depth)->key();
            }
            if (is_string($leafValue)) {
                $lang[ strtolower(join('.', $keys)) ] = $leafValue;
            }
        }

        $this->lang = $lang;
    }

    public function tr(string $message): string
    {
        return $this->lang[strtolower($message)];
    }
}
