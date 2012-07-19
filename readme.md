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


## Copyright

MODX Vapor is Copyright 2012 by MODX, LLC.
