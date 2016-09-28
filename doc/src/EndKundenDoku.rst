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

Im Folgenden werden die Konfigurationsabschnitte für *DHL Versenden* beschrieben.

Allgemeine Einstellungen
~~~~~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Allgemeine Einstellungen* wird festgelegt, ob der
*Sandbox-Modus* zum Testen der Integration verwendet oder die
Extension produktiv betrieben werden soll.

Darüber hinaus wird die Protokollierung konfiguriert. Wenn die Protokollierung
der *DHL Versenden* Extension sowie das allgemeine Logging
(*System → Konfiguration → Erweitert → Entwickleroptionen → Log Einstellungen*)
aktiviert sind, werden Webservice-Nachrichten in der Datei ``var/log/dhl_versenden.log``
aufgezeichnet. Dabei haben Sie die Auswahl zwischen drei Protokollstufen

* ``Error`` zeichnet Fehler in der Kommunikation zwischen Shop und DHL Webservice auf.
* ``Warning`` zeichnet Kommunikationsfehler sowie Fehler, die auf den Inhalt der
  Nachrichten zurückgehen (bspw. Adressvalidierung, ungültige Service-Auswahl), auf.
* ``Debug`` zeichnet sämtliche Nachrichten auf.

**Hinweis**: Stellen Sie sicher, dass die Log-Dateien regelmäßig bereinigt bzw. rotiert werden.

Stammdaten
~~~~~~~~~~

Im Konfigurationsbereich *Stammdaten* werden Ihre Zugangsdaten für den DHL Webservice
hinterlegt, die für den Produktivmodus erforderlich sind. Die Zugangsdaten erhalten
DHL Vertragskunden über den Vertrieb DHL Paket.

Versandaufträge
~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Versandaufträge* werden Einstellungen vorgenommen, die
für die Erteilung von Versandaufträgen über den DHL Webservice erforderlich sind.

* *Nur leitkodierbare Versandaufträge erteilen*: Ist diese Einstellung aktiviert,
  so werden nur Labels für seitens DHL validierte Lieferadressen erzeugt. Andernfalls
  wird DHL im Rahmen der Zustellung versuchen, fehlerhafte Lieferadressen korrekt
  zuzuordnen, wobei ein Nachkodierungsentgelt erhoben wird.
* *Gewichtseinheit*: Legen Sie fest, ob die Gewichtsangaben in Ihrem Katalog in
  Gramm oder Kilogramm gepflegt sind. Bei Bedarf wird das Gewicht während der
  Übertragung an den DHL Webservice auf Kilogramm umgerechnet.
* *Versandarten für DHL Versenden*: Legen Sie fest, welche Versandarten für die
  Versandkostenberechnung im Checkout verwendet werden sollen. Die hier ausgewählten
  Versandarten werden in der nachgelagerten Lieferscheinerstellung über den
  DHL Geschäftskundenversand abgewickelt.
* *Nachnahme-Zahlarten für DHL Versenden*: Legen Sie fest, bei welche Zahlarten
  es sich um Nachnahme-Zahlarten handelt. Diese Information wird benötigt, um
  bei Bedarf den Nachnahmebetrag an den DHL Webservice zu übertragen.

DHL Zusatzleistungen im Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *DHL Zusatzleistungen im Checkout* legen Sie fest,
welche im Rahmen des DHL Geschäftskundenversand zubuchbaren Services dem Kunden
angeboten werden.

* *Wunschort*: Der Kunde wählt einen alternativen Ablageort für seine Sendung,
  falls er nicht angetroffen wird.
* *Wunschnachbar*: Der Kunde wählt eine alternative Adresse in der Nachbarschaft
  für die Abgabe der Sendung, falls er nicht angetroffen wird.
* *Paketankündigung*: Der Kunde wird per E-Mail von DHL über den jeweiligen
  Status seiner Sendung informiert. Wählen Sie hier aus folgenden Optionen:

  * *Ja*: Der Service wird hinzugebucht.
  * *Optional*: Der Kunde bestimmt im Checkout, ob er den Service in Anspruch nehmen möchte.
  * *Nein*: Der Service wird nicht hinzugebucht.

Automatische Sendungserstellung
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Automatische Sendungserstellung* legen Sie fest, ob
automatisch Lieferscheine erzeugt und Paketaufkleber abgerufen werden sollen.

Darüber hinaus können Sie bestimmen, welchen Bestell-Status eine Bestellung haben
soll, um während der automatischen Sendungserstellung berücksichtigt zu werden
und welche Services dabei standardmäßig hinzugebucht werden sollen.

Kontaktinformationen
~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Kontaktinformationen* legen Sie fest, welche Absenderdaten
während der Erstellung von Versandaufträgen übertragen werden sollen.

Bankverbindung
~~~~~~~~~~~~~~

Im Konfigurationsbereich *Bankverbindung* legen Sie fest, welche Bankdaten im
Rahmen von Nachnahme-Versandaufträgen an den DHL Webservice übertragen werden.
Der vom Kunden erhobene Nachnahmebetrag wird auf dieses Konto transferiert.

Retourenbeileger
~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Retourenbeileger* legen Sie fest, welche Empfängeradresse
auf das Retoure-Label gedruckt werden soll, wenn dieser Service gebucht wird.

Hinweise zur Verwendung des Moduls
==================================

Versandursprung und Währung
---------------------------

Die Extension *DHL Versenden* für Magento® wendet sich an Händler mit Sitz in
Deutschland und Österreich. Stellen Sie sicher, dass die Absenderadressen in den
drei im Abschnitt Modulkonfiguration_ genannten Bereichen korrekt ist.

Die Basiswährung der Installation wird als Euro angenommen. Es findet keine
Konvertierung aus anderen Währungen statt.

Sprachunterstützung
-------------------

Das Modul unterstützt die Lokalisierungen ``en_US`` und ``de_DE``. Die Übersetzungen sind in den
CSV-Übersetzungsdateien gepflegt und somit auch durch Dritt-Module anpassbar.

Ablaufbeschreibung und Features
===============================

Annahme einer Bestellung
------------------------

Checkout
~~~~~~~~

Admin Order
~~~~~~~~~~~

DHL Lieferadressen (Packstationen, Postfilialen, Paket-Shops)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Erstellen eines Versandauftrags
-------------------------------

Nationale Sendungen
~~~~~~~~~~~~~~~~~~~

Internationale Sendungen
~~~~~~~~~~~~~~~~~~~~~~~~

Service-Auswahl
~~~~~~~~~~~~~~~

Stornieren eines Versandauftrags
--------------------------------

Automatische Sendungserstellung
-------------------------------

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

Technischer Support
===================

Wenn Sie Fragen haben oder auf Probleme stoßen, werfen Sie bitte zuerst einen Blick in das
Support-Portal (FAQ): http://dhl.support.netresearch.de/

Sollte sich das Problem damit nicht beheben lassen, können Sie das Supportteam über das o.g.
Portal oder per Mail unter dhl.support@netresearch.de kontaktieren.
