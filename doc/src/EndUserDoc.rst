.. |date| date:: %d/%m/%Y
.. |year| date:: %Y

.. footer::
   .. class:: footertable

   +-------------------------+-------------------------+
   | Stand: |date|           | .. class:: rightalign   |
   |                         |                         |
   |                         | ###Page###/###Total###  |
   +-------------------------+-------------------------+

.. header::
   .. image:: images/dhl.jpg
      :width: 4.5cm
      :height: 1.2cm
      :align: right

.. sectnum::

==================================================
DHL Versenden: Shipping for DHL Business Customers
==================================================

The module *DHL Versenden* (Ship) for Magento® enables merchants with a DHL Business 
Account to create shipments via the DHL Business Customer API and retrieve shipping 
labels. The extension also allows booking additional services and creating the customs 
declaration for international shipping.

.. raw:: pdf

   PageBreak

.. contents:: End user documentation

.. raw:: pdf

   PageBreak

Requirements
============

The following requirements must be met for the smooth operation of the module:

Magento®
--------

The following Magento® versions are supported:

- Community Edition 1.9
- Community Edition 1.8
- Community Edition 1.7

PHP
---

These PHP versions are supported:

- PHP 7.0
- PHP 5.6
- PHP 5.5

To connect to the API, the PHP SOAP extension must be installed and enabled 
on the web server.

Hints for using the module
==========================

Shipping origin and currency
----------------------------

This extension is intended for merchants who are located in Germany or Austria. 
Make sure that the shipment origin address is correct in the three configuration 
sections mentioned in `Module configuration`_

The base currency of the installation is assumed to be Euro. There is no conversion 
from other currencies.

Language support
----------------

The module support the locales ``en_US`` and ``de_DE``. The translations are stored 
in the CSV translation files and can therefore be modified by third-party modules.

.. raw:: pdf

   PageBreak

Installation and configuration
==============================

This explains how to install and configure the module.

Installation
------------

Install the module's files according to your preferred setup / deployment strategy. 
Refresh the configuration cache to apply the changes.

When the module is first executed, these new address attributes are created in 
your system:

- ``dhl_versenden_info``

The attributes are added in the following tables:

- ``sales_flat_quote_address``
- ``sales_flat_order_address``

Module configuration
--------------------

There are three relevant configuration sections for creating shipments:

::

    System → Configuration → General → General → Store-Information
    System → Configuration → Sales → Shipping Settings → Origin
    System → Configuration → Sales → Shipping Methods → DHL Versenden

Make sure that the required fields in the sections Store Information and Origin 
are filled in:

* Store Information

  * Store Name
  * Store Contact Telephone
* Origin

  * Country
  * Region / State
  * ZIP / Postal Code
  * City
  * Street Address

Next, the configuration for the DHL module is explained.

.. admonition:: Note

   The sections *Shipping Methods → DHL* and *Shipping Methods → DHL (deprecated)*
   are core parts of Magento® which connect to the webservice of DHL USA, but not 
   the DHL Versenden Business Shipping in Germany or Austria.


General Settings
~~~~~~~~~~~~~~~~

In the configuration section *General Settings* you can select if you want to run 
the module in *Sandbox Mode* to test the integration, or in production mode.

You can also configure the logging. If the logging is enabled in the DHL module and 
also in *System → Configuration → Advanced → Developer → Log Settings*, the DHL 
webservice messages will be recorded in the file ``var/log/dhl_versenden.log``. 
You can choose between three log levels:

* ``Error`` records communication errors between the shop and the DHL webservice,
* ``Warning`` records communication errors and also errors related to the message 
  content (e.g. address validation, invalid services selected), and
* ``Debug`` records all messages.

.. admonition:: Note

   Make sure to clear or rotate the log files regularly.

Account Data
~~~~~~~~~~~~

The section *Account Data* holds your access credentials for the DHL webservice 
which are required for production mode. Customers with a DHL contract get these 
data from DHL directly.

Shipment Orders
~~~~~~~~~~~~~~~

In the section *Shipment Orders*, the configuration for creating shipments via 
the DHL webservice is made.

* *Print only if codeable*: If this is enabled, only shipments with valid addresses 
  will be accepted. Otherwise, DHL will attempt to correct an invalid address
  automatically, which results in an additional charge (Nachcodierungsentgelt).
* *Weight Unit*: Select if the product weights in your catalog are stored in 
  gramms or kilogramms. If necessary, the weight will be converted to kilogramm 
  during transmission to DHL.
* *Shipping Methods for DHL Versenden*: Select which shipping methods should be
  used for calculating shipping costs in the checkout. All shipping methods that are
  selected here will be handled via DHL Business Customer Shipping when creating
  shipments.
* *Cash On Delivery payment methods for DHL Versenden*: Select which payment methods
  should be treated as Cash On Delivery (COD) payment methods. This is necessary 
  to transmit the additional charge for Cash On Delivery to the DHL webservice.

Additional Services In Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In the configuration section *Additional Services In Checkout* you can choose which 
additional DHL services you want to offer to the customer.

* *Enable Preferred Day*: The customer chooses a specific day on which the shipment
  should arrive.
* *Enable Preferred Time*: The customer chooses a timeframe within which the 
  shipment should arrive.
* *Enable Preferred Location*: The customer selects an alternative location where 
  the shipment can be placed in case they are not at home.
* *Enable Preferred Neighbor*: The customer selects an alternative address in the 
  neighborhood for the shipment in case they are not at home.
* *Enable Parcel announcement*: The customer gets notified by email about the status 
  of the shipment. Select one of the following options:

  * *Yes*: The service will be booked.
  * *Optional*: The customer can decide in the checkout if the service should be 
  booked.
  * *No*: The service will not be booked.

.. admonition:: Note

   Please note that the services Preferred Day and Preferred Time will result in 
   additional charges from DHL Paket GmbH to you during invoicing of the shipments.
   If you want to forward the additional cost to your customers, you need to do so 
   in the configuration of your shipping costs in System → Configuration → Shipping Methods.

Automatic Shipment Creation
~~~~~~~~~~~~~~~~~~~~~~~~~~~

The section *Automatic Shipment Creation* lets you choose if shipments should be 
created and package labels retrieved automatically.

You can also configure which order status an order must have to be processed 
automatically.

Also, the services which should be booked by default can be choosen here.

Contact Data
~~~~~~~~~~~~

In the section *Contact Data* you configure the shipper (sender) data which should 
be used when creating shipments with DHL.

Bank Data
~~~~~~~~~

In the section *Bank Data* you configure the bank account to be used for Cash On 
Delivery (COD) shipments with DHL. The Cash On Delivery amount from the customer 
will be transferred to this bank account.

Return Shipment
~~~~~~~~~~~~~~~

In the section *Return Shipment* you configure the receiver address to be printed 
on the Return Label, if that service was booked.

.. raw:: pdf

   PageBreak

Workflow and features
=====================

Creating an order
-----------------

The following section describes how the extension integrates itself into the order 
process.

Checkout
~~~~~~~~

In the `module configuration`_ the shipping methods have been selected for which DHL 
shipments and labels should be created. If the customer now selects one of those 
shipping methods in the checkout, the configured additional services are offered.

.. image:: images/en/checkout_services.png
   :scale: 180 %

In the checkout step *Payment information* the Cash On Delivery payment methods 
will be disabled if Cash On Delivery is not available for the selected delivery 
address.

The customer can also click on the link "Or as an alternative choose a shipment 
to a Parcelstation or a Post Office". This will lead the customer back to the 
second checkout step (shipping address) to select a DHL location, in case the 
customer didn't know this possibility existed. If the module "DHL Locationfinder" 
is installed, the checkbox for using the Location Finder will be activated.

.. raw:: pdf

   PageBreak

Admin Order
~~~~~~~~~~~

When creating orders via the Admin Panel, no additional services can be booked. 
The Cash On Delivery payment methods will be disabled if Cash On Delivery is not 
available for the delivery address (same as in the checkout).


DHL Locationfinder (Packing Stations, Post Offices, Parcel Stations)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The extension *DHL Versenden* only offers limited support for DHL delivery 
addresses in the checkout:

* The format *Packstation 123* in the field *Street* will be recognized.
* The format *Postfiliale 123* in the field *Street* will be recognized.
* A numerical value in the field *Company* will be recognized as Post Number.

A more comprehensive support for creating shipments to DHL addresses via the 
DHL webservice is offered by the separate extension `DHL Location Finder`_ 
starting from version 1.0.2:

* Interactive map for selecting the DHL delivery address.
* Validation of customer input.
* Support for Parcel Stations (Paketshops).

.. _DHL Location Finder: https://www.magentocommerce.com/magento-connect/dhl-location-finder-standortsuche.html

Creating a shipment
-------------------

The following section explains how to create a shipment for an order and how 
to retrieve the shipping label.

National shipments
~~~~~~~~~~~~~~~~~~

In the Admin Panel, select an order whose shipping method is linked to DHL. 
Then click the button *Ship* on the top of the page.

.. image:: images/en/button_ship.png

You will get to the page *New shipment for order*. Activate the checkbox 
*Create shipping label* and click the button *Submit shipment...*.

.. image:: images/en/button_submit_shipment.png
   :scale: 75 %

Now a popup window for selecting the articles in this package will open. Click 
the button *Add products*, select the products, and confirm by clicking 
*Add selected product(s) to package*. The package dimensions are optional.

.. admonition:: Note

   Splitting the products / items into multiple packages is currently not supported 
   by the DHL webservice. As an alternative, you can create several shipments for 
   one order (partial shipment).

The button *OK* in the popup window is now enabled. When clicking it, the shipment 
will be transmitted to DHL and (if the transmission was successful) a shipping 
label will be retrieved. If there was an error, the message from the DHL webservice 
will be displayed, and you can correct the data accordingly, see also Troubleshooting_.

International shipments
~~~~~~~~~~~~~~~~~~~~~~~

For shipments to addresses outside of the EU, additional fields will be displayed 
in the popup window to define the articles in the package. To get the necessary 
customs declaration, you have to enter at least the customs tariff number and 
the content type.

Everything else is the same as described in the section `National shipments`_.

Service selection
~~~~~~~~~~~~~~~~~

Aside from the services that can be selected by the customer in the checkout, there 
are other services available for merchants in the DHL Business Portal 
(Geschäftskundenportal). The available services for the current delivery address 
are shown in the popup window for selecting the shipment articles.

.. image:: images/en/merchant_services.png
   :scale: 175 %

The services selected by the customer in the checkout will already be pre-selected 
here. Also, the service *Address validation* (Print only if codeable) will be 
pre-selected if enabled in the general `Module configuration`_.

.. raw:: pdf

   PageBreak

Printing a shipping label
-------------------------

The successfully retrieved shipping labels can be opened in several locations 
of the Admin Panel:

* Sales → Orders → Mass action *Print shipping labels*
* Sales → Shipments → Mass action *Print shipping labels*
* Detail page of a shipment → Button *Print shipping label*

Cancelling a shipment
---------------------

As long as a shipment has not been manifested, it can be cancelled via the 
DHL webservice. In the Admin Panel, open the detail page of a shipment and click 
the link *Delete* in the box *Shipping and tracking information* next to the 
tracking number.

.. image:: images/en/shipping_and_tracking.png
   :scale: 75 %

If the shipment could be cancelled successfully, the tracking number and the 
shipping label will be deleted from the system.

Automatic shipment creation
---------------------------

The process for creating shipments manually can be too time-consuming or 
cumbersome for merchants with a high shipment volume. To make this easier, 
you can automate the process for creating shipments and transmitting them to 
DHL. Enable the automatic shipment creation in the `Module configuration`_ and 
select which services should be booked by default (in addition to those selected 
by the customer in the checkout).

.. admonition:: Note

   The automatic shipment creation requires setting up Cron Jobs.

   ::

      # m h dom mon dow user command
      */15 * * * * /bin/sh /absolute/path/to/magento/cron.sh

Every 15 minutes the DHL extension will collect all orders which are ready for 
shipping (according to the configuration), create shipments and transmit them 
to DHL. The automatic mode will not include shipments that require customs 
declarations.

If you want to change the timing for the automatic shipment creation, or you need 
a better monitoring of the execution, you can installl the extension `Aoe_Scheduler`_.

.. _Aoe_Scheduler:  https://github.com/AOEpeople/Aoe_Scheduler

Troubleshooting
---------------

During the transmission of shipments to DHL, errors can occur. These are often 
caused by an invalid address or an invalid combination of additional services.

When creating shipments manually, the error message will be directly visible. 
Errors that occur during automatic shipment creation will be logged as order 
comments. If the logging is enabled in the module configuration, you can also 
check the shipments in the log file.

.. admonition:: Note

   When using the automatic shipment creation, make sure to regularly check 
   the status of your orders to prevent the repeated transmission of invalid 
   shipment requests to DHL.

Erroneous shipment requests can be corrected as follows:

* In the popup window for selecting the package articles, you can disable 
  invalid additional services.
* In the popup window for selecting the package articles, you can disable the 
  address validation. DHL will then attempt to correct an invalid address, which 
  will result in an additional charge.
* On the detail page of the order or shipment, you can edit the receiver address 
  and correct any errors. Use the link *Edit* in the box *Shipping address*.

  .. image:: images/en/edit_address_link.png
     :scale: 75 %

  On this page, you can edit the address fields in the upper part, and the special 
  fields for DHL shipping in the lower part:

  * Street, House number, and address addition
  * Packstation number
  * Postfilial number (Post office)
  * Parcel shop number


.. image:: images/en/edit_address_form.png
   :scale: 175 %

Afterwards, save the address. If the error has been corrected, you can retry 
`Creating a shipment`_.

If a shipment has already been trasmitted successfully via the webservice, but 
you want to make changes afterwards, please cancel the shipment first as described 
in the section `Cancelling a shipment`_. Then click *Create shipping label...* 
inside the same box *Shipping and tracking information*. From here on, the 
process is now the same as described in `Creating a shipment`_.

.. raw:: pdf

   PageBreak

Uninstalling or disabling the module
====================================

To *uninstall* the module, follow these steps:

1. Delete all module files from your file system
2. Remove the address attributes mentioned in the section Installation_
3. Remove the module entry ``dhl_versenden_setup`` from the table ``core_resource``.
4. Remove all module entries ``carriers/dhlversenden/*`` from the table ``core_config_data``.
5. Flush the cache afterwards.

In case you only want to *disable* the module without uninstalling it, set the 
node ``active`` in the file ``app/etc/modules/Dhl_Versenden.xml`` from **true** 
to **false**.


Technical support
=================

In case of questions or problems, please have a look at the Support Portal 
(FAQ) first: http://dhl.support.netresearch.de/

If the problem cannot be resolved, you can contact the support team via the 
Support Portal or by sending an email to dhl.support@netresearch.de
