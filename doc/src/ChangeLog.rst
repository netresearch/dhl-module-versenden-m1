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

   * - 1.1.0
     - 10.05.2017
     - Features:

       * possibility to add service fee's for preffered Day & Time

     - Bugfixes:

       * missing array key for preferred day
       * fix authentication errors not be shown
       * fix label creation for partial shipments
       * make participation number required
       * db prefix will now be recognized


   * - 1.0.0
     - 17.10.2016
     - Initial Release
