# enhanced.log – Enhanced Syslog Plugin for Unraid

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)
[![GitHub Releases](https://img.shields.io/github/v/release/dkaser/unraid-enhancedlog)](https://github.com/dkaser/unraid-enhancedlog/releases)
[![Last Commit](https://img.shields.io/github/last-commit/dkaser/unraid-enhancedlog)](https://github.com/dkaser/unraid-enhancedlog/commits/main/)
[![Code Style: PHP-CS-Fixer](https://img.shields.io/badge/code%20style-php--cs--fixer-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
![GitHub Downloads (all assets, all releases)](https://img.shields.io/github/downloads/dkaser/unraid-enhancedlog/total)
![GitHub Downloads (all assets, latest release)](https://img.shields.io/github/downloads/dkaser/unraid-enhancedlog/latest/total)

## Overview

**enhanced.log** is a powerful syslog enhancement plugin for Unraid, providing advanced log filtering, colorization, categorization, and analytics. It helps users quickly identify important system events, troubleshoot issues, and customize log viewing to their needs.

## Features

- **Advanced Filtering:** Filter syslog entries by category, keyword, or custom rules.
- **Colorization:** Assign colors to log lines based on severity or content for easier scanning.
- **Categorization:** Automatically group log entries (e.g., drive-related, file system, network).
- **Custom Match Rules:** Define your own patterns for highlighting or categorizing log lines.
- **Log Analytics:** View counts and trends for recurring log messages.
- **Persistent Settings:** All configuration is saved and restored across reboots.

## Configuration

Configuration files are stored in `/boot/config/plugins/enhanced.log/`.  

## Development

### Requirements

- PHP 7.4+ (Unraid built-in)
- [Composer](https://getcomposer.org/) for dependency management

### Testing

1. Clone the repository.
2. Run `./composer install` to install dependencies.

### Release 

1. Use the provided GitHub Actions workflow for release automation.

## Contributing

Pull requests and issues are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines, including code checks, commit message conventions, and licensing. You can also open an issue to discuss your idea.

## License

This project is licensed under the [GNU General Public License v3.0](LICENSE).

> Portions copyright (C) 2015-2025 Dan Landon  
> Copyright (C) 2025 Derek Kaser

See [License.txt](License.txt) and [LICENSE](LICENSE) for details.

## Acknowledgements

- Based on original work by Dan Landon.
- Inspired by the Unraid syslog viewer and community feedback.

---

For more information, see the [Unraid forums thread](https://forums.unraid.net/topic/43115-enhanced-log-view-with-lines-highlighted-in-color/) or open an issue on GitHub.