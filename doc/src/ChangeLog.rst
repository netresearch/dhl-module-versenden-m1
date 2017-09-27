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
