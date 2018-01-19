.. |date| date:: %Y-%m-%d
.. |year| date:: %Y

.. footer::
   .. class:: footertable

   +-------------------------+-------------------------+
   | Last updated: |date|    | .. class:: rightalign   |
   |                         |                         |
   |                         | ###Page###/###Total###  |
   +-------------------------+-------------------------+

.. header::
   .. image:: images/dhl.jpg
      :width: 4.5cm
      :height: 1.2cm
      :align: right

.. sectnum::

==============================
DHL Business Customer Shipping
==============================

ChangeLog
=========

.. list-table::
   :header-rows: 1
   :widths: 2 2 10

   * - **Revision**
     - **Date**
     - **Description**

   * - 1.3.0
     - 19.01.2018
     - Features:

       * Display shipping label status in orders grid

       Bugfixes:

       * Code style improvements for Magento Marketplace

   * - 1.2.0
     - 15.12.2017
     - Features:

       * Create shipping labels via order grid mass action
       * Encrypt API password in database
       * Send shipment confirmation email during cron autocreate

       Bugfixes:

       * Remove receiver email address from request if parcel announcement service is disabled
       * Fall back to order email address if it is not available at shipping address
       * Improve address split for Austrian street numbers
       * Re-calculate service fee on shipping method or service selection changes in checkout
       * Consider Sundays in preferred day service options calculation
       * Log webservice errors during cron autocreate

   * - 1.1.1
     - 27.09.2017
     - Bugfixes:

       * Improve autoloading of namespaced classes
       * No longer terminate cron on shipment validation errors, continue processing
       * Apply correct unit of measure for item weight in export declarations
       * Display service validation errors in checkout that remained hidden under certain circumstances

   * - 1.1.0
     - 10.05.2017
     - Features:

       * Demand fees for Preferred Day / Preferred Time checkout services

       Bugfixes:

       * Missing array key for preferred day
       * Fix authentication errors not being shown
       * Fix label creation for partial shipments
       * Make participation number required
       * DB prefix will now be recognized

   * - 1.0.0
     - 17.10.2016
     - Initial Release
