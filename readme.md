# MODX Vapor

This is MODX Vapor, a PHP script for Extracting a complete snapshot of a MODX site, including it's file and database artifacts for use in importing the site into MODX Cloud.


## Environment Requirements

- PHP 5.2+
- PHP Zip extension
- All requirements for MODX 2.1+


## Installation

- Install the project as an immediate subdirectory of the `MODX_BASE_PATH` for the MODX site you want to extract as a vapor package. This subdirectory is referred to as the VAPOR_DIR for the remainder of this document.


## Usage

MODX Vapor can be run from the CLI or from a web browser. It is recommended that it be run from CLI if possible however, to avoid potential issues with web server and/or PHP-related timeouts. This is especially true for larger MODX sites. In either case, the snapshot will be created in your `MODX_CORE_PATH . 'packages/'` directory with the snapshot name indicated in the output from the script.

### Running via CLI

To create a MODX Cloud-compatible snapshot of your MODX site via CLI, change directory to your `MODX_BASE_PATH` and run `VAPOR_DIR . 'vapor.php'`, e.g.

    php vapor/vapor.php

_NOTE: The `vapor/` subdirectory is the `VAPOR_DIR`_

### Running via Browser

To create a MODX Cloud-compatible snapshot of your MODX site via CLI, navigate to `MODX_SITE_URL . VAPOR_DIR . 'vapor.php'` in your browser and wait for the process to complete, e.g.

    http://localhost/revo/vapor/vapor.php

### Custom Options

There are a few options available for customizing the execution of Vapor. You can define these options by creating a `VAPOR_DIR . 'config.php'` file with the following contents:

```php
<?php
return array(
    'excludeFiles' => array(),
    'excludeExtraTables' => array(),
    'excludeExtraTablePrefix' => array(),
);
```

- __excludeFiles__ &mdash; An array of file/directory names (do not include the trailing / on directory names) to exclude from the `MODX_BASE_PATH`. Normally Vapor would package up any non-core files located within your `MODX_BASE_PATH`. Define specific items you want to skip here.
- __excludeExtraTables__ &mdash; An array of non-core tables to exclude from your database. Normally Vapor will package any non-core tables in the database.
- __excludeExtraTablePrefix__ &mdash; An array of non-core tables to not prepend with your MODX `table_prefix`. Normally, Vapor will keep track of which non-core tables need to have a table_prefix prepended when Injected into the target MODX site, but if the source MODX site does not define a `table_prefix`, Vapor has to assume all of them will need the target's `table_prefix`. Define specific tables that should not get the target's `table_prefix` upon Injection here if your source database does not use a `table_prefix`. _This is not necessary if the source does define a `table_prefix`._


## Troubleshooting

### Review the Vapor log

Each time you run vapor it will log information about it's execution into a file with the same timestamp as the zip file that gets created (i.e. the server time when vapor is run). This file will be in the format `vapor-{timestamp}.log` and located in your logs directory at `{core_path}cache/logs/` (or `{core_path}cache/{MODX_CONFIG_KEY}/logs` if using a custom MODX_CONFIG_KEY value).


## Copyright

MODX Vapor is Copyright 2012 by MODX, LLC.
