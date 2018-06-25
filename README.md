# Magento 2 plugin for Edipost
> Magento 2 plugin for integration with Edipost

[![GitHub release](https://img.shields.io/badge/release-1.0.0-blue.svg)](https://github.com/EDIPost/php-rest-client/releases)
[![Language](https://img.shields.io/badge/language-PHP-brightgreen.svg)](http://www.php.net)

Magento 2 plugin for integration with Edipost. Make it possible to print shipping labes from the order detail page.


## Installation

### Prerequisites

* Magento 2


## Development setup

1. Clone repo
2. Copy <git_repo_dir>/Edipost to <magento2_dir>/app/code.  
   <magento2_dir>/app should look like this:
    ```
    app
    └── code
        └── Edipost
            └── Shipment
                ├── Block
                ...
    ```
3. Run `bin/magento module:enable Edipost_Shipment` to enable module
4. Run `bin/magento module:status` to check if the module is enabled
5. Run `bin/magento setup:upgrade` to update database schema, clear compiled code and cache

### Config
Configuration data is stored in database table `core_config_data`.
```
SELECT * FROM `core_config_data` WHERE path like '%edipost%'
```

## Meta

Vasil M – Skype: vasil.ysbm

Mathias Bjerke – [@mathbje](https://twitter.com/mathbje) – mathias@verida.no