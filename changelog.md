# MODX Vapor Changelog

## 1.0.0-pl (October 30, 2012)

- Fix missing table truncation script when no extension_packages setting exists
- [#8975](http://tracker.modx.com/issues/8975) Fix case in SQL for selecting non-core tables

## 1.0.0-beta-5 (October 9, 2012)

- [#8871](http://tracker.modx.com/issues/8871) Fix drop table statement in vaporVehicles to respect table_prefix properly

## 1.0.0-beta-4 (October 8, 2012)

- [#8817](http://tracker.modx.com/issues/8817) Fix media source import via resolvers
- Fix invalid reference from $options to $fileMeta in resolvers
- [#8871](http://tracker.modx.com/issues/8871) Fix import of extra tables without a table_prefix
- Add excludeExtraTablePrefix to avoid prepending table_prefix for specific tables
- Add excludeExtraTables option for excluding specific extra non-core tables
- Add excludeFiles option for excluding specific files/dirs from base_path
- Add ability to provide vaporOptions from config file

## 1.0.0-beta-3 (September 26, 2012)

- Fix PHP warning when table_prefix is empty
- Fix realpath missing trailing slash on extension_packages

## 1.0.0-beta-2 (August 23, 2012)

- Use realpath for extension_packages path only if not absolute
- Log execution to file for easier analysis
- Use set_time_limit(0) repetitively to avoid max_execution_time errors

## 1.0.0-beta-1 (July 19, 2012)

- Initial release of MODX Vapor.