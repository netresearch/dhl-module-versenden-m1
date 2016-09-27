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

===================================================
DHL Versenden: Paketversand für DHL Geschäftskunden
===================================================

Das Modul *DHL_Versenden* für Magento® …

.. contents:: Endbenutzer-Dokumentation

Voraussetzungen
===============

Die nachfolgenden Voraussetzungen müssen für den reibungslosen Betrieb des Moduls erfüllt sein:

Magento®
--------

Folgende Magento®-Versionen werden vom Modul unterstützt:

- Community Edition 1.9
- Community Edition 1.8
- Community Edition 1.7

PHP
---

Folgende PHP-Versionen werden vom Modul unterstützt:

- PHP 7.0
- PHP 5.6
- PHP 5.5

Für die Anbindung der API muss die PHP SOAP Erweiterung auf dem Webserver installiert und aktiviert sein.

Installation und Konfiguration
==============================

Im Folgenden wird beschrieben, wie das Modul installiert wird und welche
Konfigurationseinstellungen vorgenommen werden müssen.

Installation
------------

Installieren Sie die Dateien gemäß Ihrer bevorzugten Installations- und
Deployment-Strategie. Aktualisieren Sie den Konfigurations-Cache, damit die
Änderungen wirksam werden.

Beim ersten Aufruf des Moduls werden diese neuen Adress-Attribute im System angelegt:

- ``dhl_versenden_info``

Die Attribute werden in folgenden Tabellen hinzugefügt:

- ``sales_flat_quote_address``
- ``sales_flat_order_address``

Modulkonfiguration
------------------

Für die Abwicklung von Versandaufträgen relevant sind drei Konfigurationsbereiche:

::

    System → Konfiguration → Allgemein → Allgemein → Store-Information
    System → Konfiguration → Verkäufe → Versandeinstellungen → Herkunft
    System → Konfiguration → Verkäufe → Versandarten → DHL Versenden

**Hinweis**: Die Abschnitte *Versandarten → DHL* und *Versandarten → DHL (veraltet)*
sind Kernbestandteile von Magento und binden die Schnittstelle von DHL USA an,
nicht jedoch den DHL Geschäftskundenversand.

Hinweise zur Verwendung des Moduls
==================================

Sprachunterstützung
-------------------

Das Modul unterstützt die Lokalisierungen ``en_US`` und ``de_DE``. Die Übersetzungen sind in den
CSV-Übersetzungsdateien gepflegt und somit auch durch Dritt-Module anpassbar.

Modul deinstallieren oder deaktivieren
======================================

Gehen Sie wie folgt vor, um das Modul zu *deinstallieren*:

1. Löschen Sie alle Moduldateien aus dem Dateisystem.
2. Entfernen Sie die im Abschnitt `Installation`_ genannten Adressattribute.
3. Entfernen Sie den zum Modul gehörigen Eintrag ``dhl_versenden_setup`` aus der Tabelle ``core_resource``.
4. Entfernen Sie die zum Modul gehörigen Einträge ``carriers/dhlversenden/*`` aus der Tabelle ``core_config_data``.
5. Leeren Sie abschließend den Cache.

Das Modul wird *deaktiviert*, wenn der Knoten ``active`` in der Datei
``app/etc/modules/Dhl_Versenden.xml`` von **true** auf **false** abgeändert wird.
