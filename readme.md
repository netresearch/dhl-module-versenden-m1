Dhl Versenden Extension
=======================

The DHL Versenden extension for Magento 1 integrates the DHL business customer
shipping API into the order processing workflow.

Facts
-----
- version: 1.5.1
- extension key: Dhl_Versenden
- [extension on GitLab](https://git.netresearch.de/dhl/versenden-m1)
- [direct download link](https://git.netresearch.de/dhl/versenden-m1/repository/1.5.1/archive.tar.gz)

Description
-----------
This extension enables merchants to request shipping labels for incoming orders
via the DHL business customer shipping API (DHL Geschäftskundenversand-API).

Features:

* Request shipping labels for both national and international shipping.
* Select additional services.
* Request additional documents such as export documents or return forms.

Requirements
------------
- PHP >= 5.4.0

Compatibility
-------------
- Magento CE >= 1.7

Installation Instructions
-------------------------

1. Install the extension via Magento Connect with the key shown above or install
   via composer / modman.
2. Clear the cache, logout from the admin panel and then login again.

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
[Support Portal (FAQ)](http://dhl.support.netresearch.de/) first.

If the issue cannot be resolved, you can contact the support team via the
[Support Portal](http://dhl.support.netresearch.de/) or by sending an email
to <dhl.support@netresearch.de>.

Developer
---------
Christoph Aßmann | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@mam08ixo](https://twitter.com/mam08ixo)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2018 DHL Paket GmbH
