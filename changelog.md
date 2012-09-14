# MODX Vapor Changelog

- Fix realpath missing trailing slash on extension_packages

## 1.0.0-beta-2 (August 23, 2012)

- Use realpath for extension_packages path only if not absolute
- Log execution to file for easier analysis
- Use set_time_limit(0) repetitively to avoid max_execution_time errors

## 1.0.0-beta-1 (July 19, 2012)

- Initial release of MODX Vapor.