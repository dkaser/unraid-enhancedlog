<?php

namespace EDACerton\EnhancedLog;

use EDACerton\PluginUtils\Translator;
use Tomloprod\Colority\Support\Facades\Colority;

/*
    Copyright 2025  Derek Kaser

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

class Colors
{
    /**
     * @var array<string, string>
     */
    private array $colors = [
        'error'         => '',
        'minor issue'   => '',
        'lime tech'     => '',
        'array'         => '',
        'system'        => '',
        'file system'   => '',
        'drive related' => '',
        'network'       => '',
        'login'         => '',
        'emhttp'        => '',
        'other'         => '',
    ];

    /**
     * @var array<string, string>
     */
    private array $textColors = [
        'error'         => '',
        'minor issue'   => '',
        'lime tech'     => '',
        'array'         => '',
        'system'        => '',
        'file system'   => '',
        'drive related' => '',
        'network'       => '',
        'login'         => '',
        'emhttp'        => '',
        'other'         => '',
    ];

    /**
     * @var array<string, string>
     */
    private array $translations = [
        'error'         => 'colors.error',
        'minor issue'   => 'colors.minor',
        'lime tech'     => 'colors.limetech',
        'array'         => 'colors.array',
        'system'        => 'colors.system',
        'file system'   => 'colors.file',
        'drive related' => 'colors.drive',
        'network'       => 'colors.network',
        'login'         => 'colors.login',
        'emhttp'        => 'colors.emhttp',
        'other'         => 'colors.other',
    ];

    /**
     * @return array<string>
     */
    public function getColors(): array
    {
        return array_keys($this->colors);
    }

    public function getColor(string $colorName): string
    {
        if (array_key_exists($colorName, $this->colors)) {
            return $this->colors[$colorName];
        } else {
            throw new \Exception("Color not found: " . $colorName);
        }
    }

    public function getTextColor(string $colorName): string
    {
        if (array_key_exists($colorName, $this->textColors)) {
            return $this->textColors[$colorName];
        } else {
            throw new \Exception("Color not found: " . $colorName);
        }
    }

    public function setColor(string $colorName, string $color): void
    {
        if (array_key_exists($colorName, $this->colors)) {
            $this->colors[$colorName]     = $this->colorToHex($color);
            $this->textColors[$colorName] = $this->calcTextColor($color);
        } else {
            throw new \Exception("Color not found: " . $colorName);
        }
    }

    public function getColorName(string $colorName, Translator $tr): string
    {
        if (array_key_exists($colorName, $this->colors)) {
            return $tr->tr($this->translations[$colorName]);
        } else {
            throw new \Exception("Color not found: " . $colorName);
        }
    }

    public function getColorTag(string $colorName, string $font_size, Translator $tr): string
    {
        if (array_key_exists($colorName, $this->colors)) {
            if ($this->colors[$colorName] == "") {
                return "";
            }

            return "<span class='status'><span style='background-color:{$this->colors[$colorName]}; color:{$this->textColors[$colorName]}; font-size: {$font_size}'>&nbsp;" . $tr->tr($this->translations[$colorName]) . "&nbsp;</span>&nbsp;&nbsp;&nbsp;</span>";
        } else {
            throw new \Exception("Color not found: " . $colorName);
        }
    }

    /**
     * @param array<string, string> $config
     */
    public function parseConfig(array $config, bool $settings = false): void
    {
        if ($config['ERRORS'] == "yes" || $settings) {
            $this->setColor("error", $config['ERRORS_CLR']);
        }

        if ($config['MINOR_ISSUES'] == "yes" || $settings) {
            $this->setColor("minor issue", $config['MINOR_ISSUES_CLR']);
        }

        if ($config['LIME_TECH'] == "yes" || $settings) {
            $this->setColor("lime tech", $config['LIME_TECH_CLR']);
        }

        if ($config['ARRAY'] == "yes" || $settings) {
            $this->setColor("array", $config['ARRAY_CLR']);
        }

        if ($config['SYSTEM'] == "yes" || $settings) {
            $this->setColor("system", $config['SYSTEM_CLR']);
        }

        if ($config['FILE_SYSTEM'] == "yes" || $settings) {
            $this->setColor("file system", $config['FILE_SYSTEM_CLR']);
        }

        if ($config['DRIVE_RELATED'] == "yes" || $settings) {
            $this->setColor("drive related", $config['DRIVE_RELATED_CLR']);
        }

        if ($config['NETWORK'] == "yes" || $settings) {
            $this->setColor("network", $config['NETWORK_CLR']);
        }

        if ($config['LOGIN'] == "yes" || $settings) {
            $this->setColor("login", $config['LOGIN_CLR']);
        }

        if ($config['EMHTTP'] == "yes" || $settings) {
            $this->setColor("emhttp", $config['EMHTTP_CLR']);
        }

        if ($config['OTHER'] == "yes" || $settings) {
            $this->setColor("other", $config['OTHER_CLR']);
        }
    }

    private function calcTextColor(string $color): string
    {
        $color = $this->colorToHex($color);

        $hexColor = Colority::fromHex($color);

        [$baseH, $baseS, $baseL] = $hexColor->toHsl()->getArrayValueColor();

        $possibleColors = array();

        $possibleColors[] = Colority::fromHsl([$baseH, $baseS, 80]);
        $possibleColors[] = Colority::fromHsl([$baseH, $baseS, 20]);

        return $hexColor->getBestForegroundColor($possibleColors)->toHex()->getValueColor();
    }

    private function colorToHex(string $color): string
    {
        if ( ! str_starts_with($color, '#')) {
            $color = strtolower($color);
            if ( ! array_key_exists($color, CssColors::Colors)) {
                return "#000000";
            }

            $color = CssColors::Colors[$color];
        }
        return $color;
    }
}
