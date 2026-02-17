Dhl Versenden Extension
=======================

The DHL Versenden extension for Magento 1 integrates the
DHL Parcel DE Shipping API (REST) into the order processing workflow.

Facts
-----
- extension key: Dhl_Versenden
- [extension on GitHub](https://github.com/netresearch/dhl-module-versenden-m1)

Description
-----------
This extension enables merchants to request shipping labels for incoming orders
via the DHL Parcel DE Shipping API (REST).

Features:

* Request shipping labels for national and international shipments.
* Select additional shipping services.
* Request additional documents such as export documents or return labels.

Requirements
------------
- PHP ^8.1

Compatibility
-------------
- OpenMage LTS 20.x

Installation Instructions
-------------------------

Install the extension via composer:

    composer require dhl/module-versenden-m1

Clear the cache, logout from the admin panel and then login again.

More information on configuration and integration into custom themes can be found
in the documentation.

Uninstallation
--------------
1. Remove all extension files from your Magento installation
2. Clean up the database.


    ALTER TABLE `sales_flat_quote_address` DROP COLUMN `dhl_versenden_info`;

    ALTER TABLE `sales_flat_order_address` DROP COLUMN `dhl_versenden_info`;

    DELETE FROM `core_config_data` WHERE `path` LIKE 'carriers/dhlversenden/%';
    
    DELETE FROM `core_resource` WHERE `code` = 'dhl_versenden_setup';

Support
-------
In case of questions or problems, please have a look at the
[Support Portal (FAQ)](https://dhl.support.netresearch.de/) first.

If the issue cannot be resolved, you can contact the support team via the
[Support Portal](https://dhl.support.netresearch.de/) or by sending an email
to <dhl.support@netresearch.de>.

Developer
---------
[Netresearch DTT GmbH](https://www.netresearch.de/)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2026 DHL Paket GmbH
