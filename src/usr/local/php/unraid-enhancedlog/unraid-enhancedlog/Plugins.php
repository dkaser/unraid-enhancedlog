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

class Plugins
{
    /** @var array<string> $files */
    private array $files = [];

    public function __construct()
    {
        $this->files = $this->findLogFiles();
    }

    /**
     * @return array<string>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function getFile(string $file): ?string
    {
        if (in_array($file, $this->files)) {
            // Read the content of the file and return it. If the file ends in .gz, decompress it first.
            if (str_ends_with($file, '.gz')) {
                $content = file_get_contents("compress.zlib://{$file}");
            } else {
                $content = file_get_contents($file);
            }

            if ($content !== false) {
                return $content;
            }
        }

        return null;
    }

    /**
     * @return array<string>
     */
    private function findLogFiles(): array
    {
        $path  = ['/usr/local/emhttp/plugins/','/enhanced-log.json'];
        $files = array();

        foreach (glob("{$path[0]}*{$path[1]}") ?: array() as $file) {
            try {
                $data = (object) json_decode(file_get_contents($file) ?: "{}", false);

                if ( ! isset($data->files)) {
                    continue;
                }

                // Glob each entry in the files array. Add any found files to the list.
                foreach ($data->files as $glob) {
                    $foundFiles = glob($glob) ?: array();
                    foreach ($foundFiles as $foundFile) {
                        if (is_file($foundFile)) {
                            $files[] = $foundFile;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $files;
    }
}
