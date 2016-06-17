Dhl Versenden Extension
=======================

The DHL Versenden extension for Magento 1 integrates the DHL business customer
shipping API into the order processing workflow.

Facts
-----
- version: 0.1.0
- extension key: Dhl_Versenden
- [extension on Magento Connect](http://www.magentocommerce.com/magento-connect/dhl-versenden-1234.html)
- Magento Connect 2.0 extension key: http://connect20.magentocommerce.com/community/Dhl_Versenden
- [extension on GitLab](https://git.netresearch.de/dhl/versenden-m1)
- [direct download link](http://connect.magentocommerce.com/community/get/Dhl_Versenden-0.1.0.tgz)

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
- PHP >= 5.5.0

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
2. Remove the database columns from the shipping address entity (t.b.d.)

Support
-------
If you have any issues with this extension, contact the support (t.b.d.)

Developer
---------
Christoph Aßmann | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@mam08ixo](https://twitter.com/mam08ixo)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2016 DHL Paket GmbH
