.. |date| date:: %Y-%m-%d
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

Das Modul *DHL Versenden* (Shipping) für OpenMage ermöglicht es Händlern mit einem
DHL Geschäftskundenkonto, Sendungen über die DHL Parcel DE REST API
anzulegen und Versandscheine (Paketaufkleber) abzurufen. Die Extension
ermöglicht dabei auch das Hinzubuchen von Zusatzleistungen sowie den Abruf von
Exportdokumenten für den internationalen Versand.

Diese Dokumentation erklärt die **Installation, Einrichtung und Nutzung des Moduls
in OpenMage**.

.. raw:: pdf

   PageBreak

.. contents:: Endbenutzer-Dokumentation

.. raw:: pdf

   PageBreak

Voraussetzungen
===============

Die nachfolgenden Voraussetzungen müssen für den reibungslosen Betrieb des Moduls erfüllt sein:

OpenMage
--------

Folgende OpenMage-Versionen werden vom Modul unterstützt:

- OpenMage LTS >= 20.x

PHP
---

Folgende PHP-Versionen werden vom Modul unterstützt:

- PHP >= 8.2

Für die Anbindung der DHL REST API muss die PHP cURL Erweiterung auf dem
Webserver installiert und aktiviert sein.

Hinweise zur Verwendung des Moduls
==================================

Versandursprung und Währung
---------------------------

Die Extension *DHL Versenden* für OpenMage wendet sich an Händler mit Sitz in
Deutschland. Stellen Sie sicher, dass Ihre Absenderadressen in
den drei im Abschnitt Modulkonfiguration_ genannten Bereichen korrekt ist.

Die Basiswährung der Installation wird als Euro angenommen. Es findet keine
Konvertierung aus anderen Währungen statt.

.. admonition:: Österreich nicht unterstützt

   Versand aus Österreich (AT) wird nicht mehr unterstützt.

   Bestehende Bestellungen im System können noch abgeschlossen werden, aber es können
   keine neuen Bestellungen mehr über DHL verarbeitet werden, wenn aus Österreich
   verschickt wird.

Sprachunterstützung
-------------------

Das Modul unterstützt die Lokalisierungen ``en_US`` und ``de_DE``. Die Übersetzungen
sind in den CSV-Übersetzungsdateien gepflegt und somit auch durch Dritt-Module anpassbar.

Datenschutz
-----------

Durch das Modul werden personenbezogene Daten an DHL übermittelt, die zur Verarbeitung des Auftrags
erforderlich sind (Namen, Anschriften, Telefonnumern, E-Mail-Adressen, etc.). Der Umfang der
übermittelten Daten hängt von der `Modulkonfiguration`_ sowie den gewählten
`DHL Zusatzleistungen im Checkout`_ ab. Insbesondere wird die E-Mail-Adresse des
Empfängers nur dann an DHL übermittelt, wenn der Service *Automatische Paketankündigung*
aktiv ist (siehe `DHL Zusatzleistungen im Checkout`_).

Der Händler muss sich vom Kunden das Einverständnis zur Verarbeitung der Daten einholen,
beispielsweise über die AGB des Shops und / oder eine Einverständniserklärung im Checkout (OpenMage
Checkout Agreements / Terms and Conditions).

Die an die DHL Parcel DE REST API übermittelten Daten können im Log ``var/log/dhl_versenden.log``
eingesehen werden (siehe `Allgemeine Einstellungen`_ zur Aktivierung).

Für `DHL Zusatzleistungen im Checkout`_ (Paketsteuerung API) werden Daten im Log
``var/log/dhl_service.log`` gespeichert. Der Umfang der protokollierten Daten hängt von der
konfigurierten Protokollstufe ab (siehe `Allgemeine Einstellungen`_).

.. raw:: pdf

   PageBreak

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

Für die Abwicklung von Versandaufträgen sind drei Konfigurationsbereiche relevant:

::

    System → Konfiguration → Allgemein → Allgemein → Store-Information
    System → Konfiguration → Verkäufe → Versandeinstellungen → Herkunft
    System → Konfiguration → Verkäufe → Versandarten → DHL Versenden

Stellen Sie sicher, dass die erforderlichen Felder in folgenden Bereichen
ausgefüllt sind:

* Store-Information

  * Store-Name
  * Store-Kontakttelefon

* Herkunft

  * Land
  * Region/Bundesland
  * Postleitzahl
  * Stadt
  * Straße

* DHL Versenden (Shipping)

  * Kontaktinformationen
  * Bankverbindung

Die Abschnitte *Versandarten → DHL* und *Versandarten → DHL (veraltet)*
sind Kernbestandteile von OpenMage und binden die Schnittstelle von DHL USA an.
Sie sind jedoch nicht relevant für den DHL Geschäftskundenversand (Versenden)
in Deutschland.

**Aktivieren Sie diese Abschnitte nicht, wenn Sie DHL Versenden (Shipping) nutzen!**

.. raw:: pdf

   PageBreak

Allgemeine Einstellungen
~~~~~~~~~~~~~~~~~~~~~~~~

Wählen Sie, ob der **Sandbox-Modus** zum Testen der Integration verwendet, oder die Extension
**produktiv** betrieben werden soll.

Außerdem kann hier die **Protokollierung (Logging)** konfiguriert werden. Wenn die Protokollierung
aktiviert ist, wird die Kommunikation mit der DHL Parcel DE REST API in der Datei
``var/log/dhl_versenden.log`` aufgezeichnet. Dabei haben Sie die Auswahl zwischen
drei Protokollstufen:

* *Error*: Nur Kommunikationsprobleme zwischen Shop und DHL REST API werden geloggt. Wenn
  kein Problem auftritt, wird nichts in das Log geschrieben.
* *Warning*: Kommunikationsprobleme sowie inhaltliche Fehler werden geloggt (z.B. Adressvalidierung,
  ungültige Service-Auswahl).
* *Debug*: Sämtliche Daten, Erfolgsmeldungen, Fehler und übertragenen Inhalte (PDF-Label) werden
  geloggt. **Nur zur Fehlersuche empfohlen.**

.. admonition:: Hinweise zum Logging

   Stellen Sie sicher, dass die Log-Dateien regelmäßig bereinigt bzw. archiviert werden.
   Die Logs werden durch das Modul nicht automatisch gelöscht. Personenbezogene Daten dürfen nur so
   lange vorgehalten bzw. gespeichert werden, wie unbedingt erforderlich.

   Log-Dateien:

   * ``var/log/dhl_versenden.log`` für Label-Erstellung (DHL Parcel DE REST API)
   * ``var/log/dhl_service.log`` für DHL Zusatzservices (Paketsteuerung API)

Stammdaten
~~~~~~~~~~

Im Konfigurationsbereich *Stammdaten* werden Ihre Zugangsdaten für die DHL Parcel DE REST API
hinterlegt, die für den Produktivmodus erforderlich sind. Für die Authentifizierung wird
ein DHL Application Key (API-Token) benötigt. Die Zugangsdaten erhalten
DHL Vertragskunden über den Vertrieb DHL Paket.

Eine detaillierte Anleitung zur Einrichtung der Teilnahmenummern finden Sie in `diesem Artikel
in der Wissensdatenbank <http://dhl.support.netresearch.de/support/solutions/articles/12000024658>`_.

.. raw:: pdf

   PageBreak

Versandaufträge
~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Versandaufträge* werden Einstellungen vorgenommen, die
für die Erteilung von Versandaufträgen über die DHL REST API erforderlich sind.

* *Nur leitkodierbare Versandaufträge erteilen*: Ist diese Einstellung aktiviert,
  wird DHL nur Sendungen akzeptieren, deren Adressen absolut korrekt sind. Ansonsten
  lehnt DHL die Sendung mit einer Fehlermeldung ab. Wenn diese Einstellung abgeschaltet
  ist, wird DHL versuchen, fehlerhafte Lieferadressen automatisch korrekt zuzuordnen,
  wofür ein Nachkodierungsentgelt erhoben wird. Wenn die Adresse überhaupt nicht
  zugeordnet werden kann, wird die Sendung dennoch abgelehnt.
* *Empfänger-Telefonnummer übertragen*: Hiermit kann gesteuert werden, ob die Telefonnummer
  des Käufers bei der Sendungserstellung an DHL übermittelt werden soll. Siehe auch Hinweise
  zum `Datenschutz`_.
* *Gewichtseinheit*: Legen Sie fest, ob die Gewichtsangaben in Ihrem Katalog in
  Gramm oder Kilogramm gepflegt sind. Bei Bedarf wird das Gewicht während der
  Übertragung an DHL auf Kilogramm umgerechnet.
* *Versandarten für DHL Versenden*: Legen Sie fest, welche Versandarten mit DHL
  verknüpft sein sollen. Für die hier ausgewählten Versandarten werden im Checkout die
  verfügbaren DHL Zusatzleistungen angeboten und DHL-Label erzeugt, wenn der Lieferschein
  in OpenMage angelegt wird.
* *Nachnahme-Zahlarten für DHL Versenden*: Legen Sie fest, bei welchen Zahlarten es sich
  um Nachnahme-Zahlarten handelt. Wenn eine dieser Zahlarten verwendet wird, wird ein
  Nachnahme-Label erzeugt.
* *Druckformat*: Wählen Sie das Druckformat für die Paketaufkleber (z.B. A4, 910-300-700).
  Standard: A4.

.. raw:: pdf

   PageBreak

DHL Zusatzleistungen im Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *DHL Zusatzleistungen im Checkout* legen Sie fest,
welche im Rahmen des DHL Geschäftskundenversand zubuchbaren Services Ihren Kunden
angeboten werden.

Beachten Sie bitte auch die Hinweise zur `Buchbarkeit von Zusatzservices`_ sowie die
`Zusatzkosten für Services`_ und die Hinweise zum `Datenschutz`_.

* *Ablageort*: Der Kunde wählt einen alternativen Ablageort für seine Sendung,
  falls er nicht angetroffen wird.
* *Nachbar*: Der Kunde wählt eine alternative Adresse in der Nachbarschaft
  für die Abgabe der Sendung, falls er nicht angetroffen wird.
* *Automatische Paketankündigung*: Der Kunde wird per E-Mail von DHL über den Status seiner
  Sendung informiert. Hierzu wird die E-Mail-Adresse des Kunden an DHL übermittelt (siehe
  Hinweise zum `Datenschutz`_). Wählen Sie hier aus folgenden Optionen:

  * *Ja*: Der Service wird immer hinzugebucht. Im Checkout wird keine Auswahl angezeigt.
  * *Aktivieren auf Kundenwunsch*: Der Kunde kann im Checkout wählen, ob der Service gebucht werden soll.
  * *Nein*: Der Service wird nie hinzugebucht.

  Die E-Mail-Adresse des Empfängers wird nur dann an DHL übermittelt, wenn die
  Paketankündigung gebucht ist. Ist der Service deaktiviert, werden keine
  E-Mail-Daten an DHL gesendet (siehe auch `Datenschutz`_).

* *Liefertag*: Der Kunde wählt einen festgelegten Tag für seine Sendung,
  an welchem die Lieferung ankommen soll. Die verfügbaren Liefertage werden dynamisch
  angezeigt, basierend auf der Empfängeradresse.

* *Liefertag Aufpreis (Serviceaufschlag)*: Dieser Betrag wird zu den Versandkosten
  hinzu addiert, wenn der Zusatzservice verwendet wird. Verwenden Sie Punkt statt Komma
  als Trennzeichen. Der Betrag muss in Brutto angegeben werden (einschl. Steuern).
  Wenn Sie die Zusatzkosten nicht an den Kunden weiterreichen wollen, tragen Sie hier
  ``0`` ein.
* *Liefertag Serviceaufschlag Hinweistext*: Dieser Text wird dem Kunden
  im Checkout angezeigt, wenn der Zusatzservice ausgewählt wird. Sie können den
  Platzhalter ``$1`` im Text verwenden, welcher im Checkout durch den Zusatzbetrag
  und die Währung ersetzt wird.
* *Annahmeschluss*: Legt den Zeitpunkt fest, bis zu dem eingegangene Bestellungen
  noch am selben Tag abgeschickt werden. Bestellungen, die *nach* Annahmeschluss
  eingehen, werden nicht mehr am selben Tag verschickt. Der früheste Liefertag
  verschiebt sich dann um einen Tag.

.. admonition:: Hinweis zu Annahmeschluss

   Damit die Zeitschwelle korrekt berechnet wird, muss die Systemzeit auf Ihrem Server richtig
   gesetzt sein. Achten Sie auf eventuelle Verschiebungen durch Sommer- bzw. Winterzeit oder
   abweichende Zeitzonen. Ändern Sie wenn nötig die Annahmeschluss-Zeit, um dies auszugleichen.

* *Keine Nachbarschaftszustellung aktivieren*: Der Kunde kann veranlassen, dass die
  Sendung nicht an einen Nachbarn zugestellt wird. Wählen Sie *Ja*, um diesen Service
  im Checkout anzubieten.
* *Keine Nachbarschaftszustellung Aufpreis (Serviceaufschlag)*: Dieser Betrag wird zu
  den Versandkosten addiert, wenn der Service gebucht wird. Verwenden Sie Punkt statt
  Komma als Trennzeichen. Der Betrag muss in Brutto angegeben werden (einschl. Steuern).
  Tragen Sie ``0`` ein, wenn kein Aufpreis erhoben werden soll.
* *Keine Nachbarschaftszustellung Serviceaufschlag Hinweistext*: Dieser Text wird dem
  Kunden im Checkout angezeigt, wenn der Service ausgewählt wird. Der Platzhalter ``$1``
  zeigt den Aufpreis und die Währung an.
* *GoGreen Plus aktivieren*: Der Kunde kann klimaneutralen Versand buchen. Wählen Sie
  *Ja*, um diesen Service im Checkout anzubieten.

  .. admonition:: Hinweis

     Bei aktivierter Standardbeauftragung im DHL Geschäftskundenportal wird GoGreen Plus
     automatisch auf alle Sendungen angewendet, unabhängig von dieser Shop-Einstellung.
     Dies ist der empfohlene Weg, GoGreen Plus für alle Bestellungen zu aktivieren.

* *GoGreen Plus Aufpreis (Serviceaufschlag)*: Dieser Betrag wird zu den Versandkosten
  addiert, wenn der Service gebucht wird.
* *GoGreen Plus Serviceaufschlag Hinweistext*: Dieser Text wird dem Kunden im Checkout
  angezeigt, wenn der Service ausgewählt wird. Der Platzhalter ``$1`` zeigt den Aufpreis an.
* *Closest Drop Point (CDP) aktivieren*: Der Kunde kann die Zustellung an den nächsten
  DHL-Abholort wählen statt an die Hausadresse. Dieser Service ist nur für berechtigte
  EU-Länder verfügbar (AT, BE, BG, DK, FI, FR, HU, PL, SE). Bei Auswahl von CDP
  wird die E-Mail-Adresse des Empfängers automatisch in die Sendungsdaten aufgenommen.
* *CDP Aufpreis (Serviceaufschlag)*: Dieser Betrag wird zu den Versandkosten addiert,
  wenn der Service gebucht wird.
* *CDP Serviceaufschlag Hinweistext*: Dieser Text wird dem Kunden im Checkout angezeigt,
  wenn der Service ausgewählt wird. Der Platzhalter ``$1`` zeigt den Aufpreis an.

.. raw:: pdf

   PageBreak

Automatische Sendungserstellung
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Automatische Sendungserstellung* legen Sie fest, ob
automatisch Lieferscheine erzeugt und Paketaufkleber abgerufen werden sollen (per Cronjob).

Die Einstellung *Kundenbenachrichtigung* ermöglicht es, die Versandinformationen per E-Mail
an den Kunden zu schicken, wenn Lieferschein und DHL-Auftrag erfolgreich erzeugt wurden.

Die Einstellung *Bestell-Status* legt fest, welchen Status eine Bestellung haben muss,
um durch die automatische Sendungserstellung berücksichtigt zu werden. Hierüber können Sie
steuern, welche Bestellungen von der automatischen Verarbeitung ausgeschlossen werden sollen.

Die Einstellung *Versandprodukt (Inland)* legt das Standard-Versandprodukt für die
automatische Sendungserstellung nationaler Sendungen fest. Hier stehen folgende Produkte zur Verfügung:

- V01PAK – DHL Paket (bis 31,5 kg)
- V62KP – DHL Kleinpaket

Für internationale Ziele wird das Versandprodukt automatisch anhand des Ziellandes bestimmt.

Standardwerte für Sendungen
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Hier legen Sie die *Zusatzservices* fest, die automatisch hinzugebucht werden sollen.
Folgende Standardwerte können konfiguriert werden:

* Alterssichtprüfung (A16 / A18)
* Retourenbeileger
* Transportversicherung
* Sperrgut
* Keine Nachbarschaftszustellung
* Persönliche Übergabe
* Empfängerunterschrift
* Vorausverfügung (Zurück / Preisgabe)
* Zustellart (Economy / Premium / CDP)
* PDDP
* Filial-Routing (mit Benachrichtigungs-E-Mail)

.. admonition:: Hinweis

   Nachnahme wird automatisch anhand der Zahlungsart der Bestellung bestimmt und kann
   nicht als Standardwert konfiguriert werden. GoGreen Plus ist ein reiner
   Checkout-Service für Endkunden und steht nicht als Standardwert zur Verfügung.

Kontaktinformationen
~~~~~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Kontaktinformationen* legen Sie fest, welche Absenderdaten
während der Erstellung von Versandaufträgen an DHL übermittelt werden sollen.

Bankverbindung
~~~~~~~~~~~~~~

Im Konfigurationsbereich *Bankverbindung* legen Sie fest, welche Bankdaten im
Rahmen von Nachnahme-Versandaufträgen an DHL übermittelt werden.
Der vom Kunden erhobene Nachnahmebetrag wird auf dieses Konto transferiert.

Beachten Sie, dass die Bankverbindung ggf. auch in Ihrem DHL-Konto hinterlegt werden
muss. I.d.R. kann dies über das DHL Geschäftskundenportal erledigt werden.

Retourenbeileger
~~~~~~~~~~~~~~~~

Im Konfigurationsbereich *Retourenbeileger* legen Sie fest, welche Empfängeradresse
auf das Retoure-Label gedruckt werden soll, wenn dieser Service gebucht wird.

.. raw:: pdf

   PageBreak

Buchbarkeit von Zusatzservices
------------------------------

Die tatsächlich buchbaren Services sowie die wählbaren Liefertage hängen
von der Lieferadresse bzw. dem Zielland ab. Dazu wird die DHL Paketsteuerung API während
des Checkouts verwendet. Nicht verfügbare Services werden im Checkout
automatisch ausgeblendet.

Falls die Bestellung Artikel enthält, die nicht sofort lieferbar sind, ist keine Buchung
vom Liefertag möglich.

Die gleichzeitige Buchung von Ablageort und Nachbar ist nicht möglich.

Zusatzkosten für Services
-------------------------

Der Service *Liefertag* ist **standardmäßig aktiviert!** Wird dieser gebucht, wird
der konfigurierte Service-Aufschlag zu den Versandkosten hinzugefügt.

Auch die Services *Keine Nachbarschaftszustellung*, *GoGreen Plus* und *Closest Drop
Point (CDP)* können mit einem Serviceaufschlag konfiguriert werden, der bei Buchung im
Checkout zu den Versandkosten addiert wird (siehe `DHL Zusatzleistungen im Checkout`_).

Bei Nutzung der Versandart *Free Shipping / Versandkostenfrei* werden die eingestellten
Zusatzkosten generell außer Kraft gesetzt!

Wenn die Versandart *Table Rates / Tabellenbasierte Versandkosten* genutzt wird und eine
Grenze für kostenlosen Versand festgelegt werden soll, empfehlen wir dazu eine
Warenkorbpreisregel einzurichten. Durch Nutzung dieser Versandart bleiben die Aufpreise
für Zusatzservices erhalten.

Ablaufbeschreibung und Features
===============================

Annahme einer Bestellung
------------------------

Im Folgenden wird beschrieben, wie sich die Extension *DHL Versenden* in den
Bestellprozess integriert.

Checkout
~~~~~~~~

In der Modulkonfiguration_ wurden Versandarten gewählt, die über DHL abgewickelt
werden sollen.

Wählt der Kunde im Checkout-Schritt *Versandart* eine dieser Versandarten, werden
die in der Konfiguration aktivierten DHL-Zusatzleistungen angeboten. Beachten Sie
dazu bitte die Infos zur `Buchbarkeit von Zusatzservices`_.

.. image:: images/de/checkout_services.png
   :width: 14cm

Im Checkout-Schritt *Zahlungsinformation* werden Nachnahme-Zahlarten ausgeblendet,
falls der Nachnahme-Service für die gewählte Lieferadresse nicht zur Verfügung
steht.

Der Kunde kann auf den Link "*Oder wählen Sie die Lieferung an einen Paketshop oder
eine Postfiliale*" klicken. Dadurch wird er zum Schritt *Lieferadresse* zurück
geleitet und kann, wenn gewünscht, einen DHL-Abholort als abweichende Lieferadresse
eingeben.

Falls das Modul `DHL Locationfinder <http://dhl.support.netresearch.de/support/solutions/articles/12000016724>`_
installiert ist, kann der Kunde diesen nutzen, um mit wenig Aufwand naheliegende
DHL-Abholorte zu finden und zu übernehmen.

.. raw:: pdf

   PageBreak

Admin-Bestellung
~~~~~~~~~~~~~~~~

Beim Anlegen von Bestellungen im Admin Panel stehen keine Zusatzleistungen
zur Verfügung. Es ist aber möglich, Zusatzleistungen zu wählen, wenn später die
Lieferung erstellt wird.

Nachnahme-Zahlarten werden ebenso wie im Checkout deaktiviert, falls
der Nachnahme-Service für die gewählte Lieferadresse nicht zur Verfügung steht.

Beachten Sie bitte auch die Hinweise zur `Buchbarkeit von Zusatzservices`_.

DHL Lieferadressen (Packstationen, Postfilialen, Paket-Shops)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Die Extension *DHL Versenden* selbst bietet nur eine eingeschränkte Unterstützung
von DHL Lieferadressen im Checkout:

* Das Format *Packstation 123* im Feld *Straße* wird erkannt.
* Das Format *Postfiliale 123* im Feld *Straße* wird erkannt.
* Ein numerischer Wert im Feld *Firma* wird als Postnummer erkannt.

Eine umfassendere Unterstützung von DHL Lieferadressen im Zusammenspiel mit der
Erteilung von Versandaufträgen über den DHL Webservice bietet die separate
Extension `DHL Locationfinder <http://dhl.support.netresearch.de/support/solutions/articles/12000016724>`_:

* Interaktive Karte zur Auswahl der DHL Lieferadresse
* Gesonderte Eingabefelder für DHL-Daten im Checkout (z.B. Postnummer)
* Validierung der Kundeneingaben
* Unterstützung von Paket-Shops

Erstellen eines Versandauftrags
-------------------------------

Im Folgenden Abschnitt wird beschrieben, wie zu einer Bestellung ein Versandauftrag
erstellt und ein Paketaufkleber abgerufen wird.

Nationale Sendungen
~~~~~~~~~~~~~~~~~~~

Öffnen Sie im Admin Panel eine Bestellung, deren Versandart mit dem DHL
Geschäftskundenversand verknüpft ist (siehe Modulkonfiguration_, Abschnitt *Versandarten
für DHL Versenden*). Betätigen Sie dann den Button *Versand* oben rechts.

.. image:: images/de/button_ship.png

Es öffnet sich die Seite *Neuer Versand für Bestellung*. Aktivieren Sie die Checkbox
*Paketaufkleber erstellen* an und betätigen Sie den Button *Lieferschein erstellen...*.

.. image:: images/de/button_submit_shipment.png
   :width: 6cm

Es öffnet sich nun ein Popup zur Definition der im Paket enthaltenen Artikel.
Betätigen Sie den Button *Artikel hinzufügen*, markieren Sie die bestellten
Produkte und bestätigen Sie Ihre Auswahl durch Klick auf
*Gewählte Artikel zum Paket hinzufügen*. Die Angabe der Paketmaße ist optional.

.. admonition:: Mehrpaket-Sendungen

   Die Aufteilung der Produkte in mehrere Pakete wird von der DHL REST API
   derzeit nicht unterstützt. Erstellen Sie alternativ mehrere Lieferscheine
   (Teillieferung / Partial Shipment) zu einer Bestellung, siehe auch
   `diese Anleitung <http://dhl.support.netresearch.de/support/solutions/articles/12000029043>`_.

Der Button *OK* im Popup ist nun aktiviert. Bei Betätigung wird ein Versandauftrag
an DHL übermittelt und im Erfolgsfall der resultierende Paketaufkleber abgerufen.
Im Fehlerfall wird die von der DHL REST API erhaltene Fehlermeldung eingeblendet und
die Bestellung kann entsprechend korrigiert werden, siehe auch Fehlerbehandlung_.

Internationale Sendungen
~~~~~~~~~~~~~~~~~~~~~~~~

Bei Sendungen mit einer Lieferadresse außerhalb der EU werden zusätzliche Felder
im Popup zur Definition der im Paket enthaltenen Artikel eingeblendet. Geben
Sie für den Abruf der notwendigen Exportdokumente mindestens die Zolltarifnummern
sowie den Inhaltstyp der Sendung an.

Gehen Sie ansonsten wie im Abschnitt `Nationale Sendungen`_ beschrieben vor.

.. raw:: pdf

   PageBreak

Versandprodukt
~~~~~~~~~~~~~~~

Das Versandprodukt wird automatisch anhand des Ziellandes ausgewählt oder kann im
Verpackungs-Popup manuell bestimmt werden. Folgende Produkte stehen zur Verfügung:

**Inland (DE → DE):**

* V01PAK – DHL Paket (bis 31,5 kg)
* V62KP – DHL Kleinpaket

**International (EU + Welt):**

* V53WPAK – DHL Paket International
* V66WPI – DHL Warenpost International

Service-Auswahl
~~~~~~~~~~~~~~~

Neben den im Checkout verfügbaren Zusatzleistungen, die sich an den Käufer richten,
stehen im Verpackungs-Popup weitere, an den Händler gerichtete Services zur Verfügung.
Die für das aktuelle Versandprodukt und die Lieferadresse verfügbaren Zusatzleistungen
werden im Popup zur Definition der im Paket enthaltenen Artikel eingeblendet.

.. image:: images/de/merchant_services.png
   :width: 16cm

Die vom Kunden im Checkout gewählten Services sind entsprechend vorbelegt, ebenso
wie die *Adressprüfung* (Nur leitkodierbare Versandaufträge erteilen) gemäß der
Modulkonfiguration_.

.. admonition:: Schreibgeschützte Services

   Im Checkout vom Kunden gewählte Services (z.B. Ablageort, Nachbar, Closest Drop
   Point) sowie zahlungsartabhängige Services (Nachnahme) werden im Verpackungs-Popup
   als **deaktivierte Checkboxen** angezeigt. Der Händler kann diese einsehen, aber
   nicht ändern.

Nicht alle Services sind für alle Versandprodukte verfügbar. Das Verpackungs-Popup
zeigt automatisch nur die für das gewählte Produkt und die Route anwendbaren Services an.

**Zustellservices:**

* Transportversicherung (Checkbox, Versicherungswert entspricht dem Bestellwert)
* Retourenbeileger (Checkbox)
* Sperrgut (Checkbox)
* Alterssichtprüfung (A16 / A18)
* Persönliche Übergabe (Checkbox)
* Keine Nachbarschaftszustellung (Checkbox)
* GoGreen Plus (Checkbox, nur verfügbar wenn vom Kunden im Checkout gebucht)

**Standort-Services:**

* Filial-Routing (Checkbox + E-Mail-Eingabe, vorbelegt aus der Bestellung)
* Ablageort / Nachbar (aus dem Checkout, falls gewählt)

**Internationale Services:**

* Vorausverfügung (Zurück / Preisgabe)
* Zustellart (Economy / Premium / CDP) — wenn der Kunde im Checkout Closest Drop
  Point (CDP) gewählt hat, wird die Zustellart auf CDP vorbelegt und gesperrt
* PDDP (Checkbox, automatisch aktiviert für CH, GB, NO, US)

**Zahlungsservices:**

* Nachnahme (automatisch, schreibgeschützt — aktiviert wenn die Bestellung eine
  Nachnahme-Zahlart gemäß der Konfiguration unter `Versandaufträge`_ verwendet,
  ansonsten ausgeblendet)

Beachten Sie, dass bei Ablageort oder Nachbar folgende Angaben **nicht** zulässig sind:

**Unzulässige Sonderzeichen**

::

    < > \ ' " " + \n \r

**Unzulässige Angaben**

* Paketbox
* Postfach
* Postfiliale / Postfiliale Direkt / Filiale / Filiale Direkt / Wunschfiliale
* Paketkasten
* DHL / Deutsche Post
* Packstation / P-A-C-K-S-T-A-T-I-O-N / Paketstation / Pack Station / P.A.C.K.S.T.A.T.I.O.N. /
  Pakcstation / Paackstation / Pakstation / Backstation / Bakstation / P A C K S T A T I O N

Für den Versand an DHL-Abholorte (Packstation, Filiale, usw.) nutzen Sie bitte die dafür
vorgesehenen Adressfelder.

.. raw:: pdf

   PageBreak

Massenaktion
~~~~~~~~~~~~

Inländische Lieferscheine und Paketaufkleber können über die Massenaktion
*Paketaufkleber abrufen* in der Bestellübersicht erzeugt werden:

* Verkäufe → Bestellungen → Massenaktion *Paketaufkleber abrufen*

Dies ermöglicht es, einfache Paketaufkleber ohne manuelle Eingaben zu erstellen.
Dabei gilt:

* Es werden alle in der Bestellung enthaltenen Artikel übernommen.
* Die im Checkout gewählten DHL-Zusatzleistungen werden übernommen.
* Weitere Zusatzleistungen, die im Bereich *Automatische Sendungserstellung* in der
  Modulkonfiguration_ eingestellt sind, werden hinzugebucht.

.. admonition:: Hinweis

   Die Massenaktion unterstützt ausschließlich inländische Sendungen (DE → DE).
   Für EU- und internationale Sendungen müssen Versandaufträge manuell über die
   Bestelldetailseite erstellt werden.

Übersicht der Versandaufträge
-----------------------------

Bei Bestellungen, die über DHL abgewickelt werden, erscheinen in der Bestellübersicht
DHL-Icons, die den Status der Versandaufträge zeigen.

* **Durchgestrichenes Icon**: es gab Fehler bei der Label-Erstellung, siehe Fehlerbehandlung_.
* **Gelbes Icon**: Übertragung ok, Label erfolgreich erstellt.
* **Graues Icon**: Übertragung an DHL wurde noch nicht ausgeführt.

.. image:: images/de/label_status.png
   :width: 5cm

.. raw:: pdf

   PageBreak

Drucken eines Paketaufklebers
-----------------------------

Bereits abgerufene Paketaufkleber können standardmäßig an verschiedenen
Stellen im Admin Panel eingesehen werden:

* Verkäufe → Bestellungen → Massenaktion *Paketaufkleber drucken*
* Verkäufe → Lieferscheine → Massenaktion *Paketaufkleber drucken*
* Detail-Ansicht eines Lieferscheins → Button *Paketaufkleber drucken*

Beachten Sie, dass hierüber keine *neuen* Aufträge an DHL übermittelt werden,
sondern lediglich die bereits in OpenMage gespeicherten DHL-Label abgerufen werden.

Zur Erstellung von *neuen* DHL-Aufträgen und Labeln gehen Sie bitte wie unter
Massenaktion_ beschrieben vor.

Erstellen eines Retouren-Beilegers
----------------------------------

Bei Versand innerhalb Deutschlands (DE → DE) ist es möglich, gemeinsam mit dem Paketaufkleber
einen Retouren-Beileger zu beauftragen.

Nutzen Sie dafür beim Erstellen des Labels im Popup das Auswahlfeld *Retouren-Beileger*.
Diese Option ist nur verfügbar, wenn der Service *Retourenbeileger* in den *Standardwerten
für Sendungen* aktiviert ist. Wenn für die Hinsendung der Service *GoGreen Plus* gebucht
wurde, wird dieser automatisch auch auf den Retourenbeileger angewendet.

Stellen Sie sicher, dass die Teilnahmenummern für Retouren korrekt konfiguriert sind:

* Retoure DHL Paket (DE → DE)

Stornieren eines Versandauftrags
--------------------------------

Solange ein Versandauftrag nicht manifestiert ist, kann dieser über die DHL
REST API storniert werden. Öffnen Sie dazu im Admin-Panel die Detail-Ansicht
eines Lieferscheins und betätigen Sie den Link *Löschen* in der Box
*Versand- und Trackinginformationen* neben der Sendungsnummer.

.. image:: images/de/shipping_and_tracking.png
   :width: 10cm

Wenn der Versandauftrag erfolgreich über die DHL REST API storniert wurde,
werden Sendungsnummer und Paketaufkleber aus dem System entfernt.

.. raw:: pdf

   PageBreak

Automatische Sendungserstellung
-------------------------------

Der manuelle Prozess zur Erstellung von Versandaufträgen ist insbesondere für
Händler mit hohem Versandvolumen sehr zeitaufwendig und unkomfortabel. Um den
Abruf von Paketaufklebern zu erleichtern, können Sie das Erstellen von
Lieferscheinen und Versandaufträgen automatisieren. Aktivieren Sie dazu in der
Modulkonfiguration_ die automatische Sendungserstellung. Bei der
automatischen Erstellung werden die in den *Standardwerten für Sendungen*
konfigurierten Zusatzleistungen gebucht. Darüber hinaus werden vom Kunden im
Checkout gewählte Services übernommen.

.. admonition:: Hinweis

   Die automatische Sendungserstellung erfordert die Einrichtung der Cron Jobs.

   ::

      # m h dom mon dow user command
      */15 * * * * /bin/sh /absolute/path/to/magento/cron.sh

Im Abstand von 15 Minuten wird die Extension *DHL Versenden* alle gemäß der
getroffenen Einstellungen versandbereiten inländischen Bestellungen sammeln,
Lieferscheine erstellen und Versandaufträge an DHL übermitteln. EU- und
internationale Sendungen werden nicht automatisch verarbeitet und erfordern
eine manuelle Erstellung über die Bestelldetailseite.

Sollten Sie den Zeitplan für die automatische Sendungserstellung anpassen oder
die Ausführung besser überwachen wollen, installieren Sie die Extension
`Aoe_Scheduler`_.

.. _Aoe_Scheduler:  https://github.com/AOEpeople/Aoe_Scheduler

.. raw:: pdf

   PageBreak

Fehlerbehandlung
----------------

Sendungserstellung
~~~~~~~~~~~~~~~~~~

Während der Übertragung von Versandaufträgen an die DHL REST API kann es zu
Fehlern bei der Erstellung eines Paketaufklebers kommen. Die Ursache dafür ist
in der Regel eine invalide Lieferadresse oder eine für die Lieferadresse nicht
unterstützte Kombination von Zusatzleistungen.

Bei der manuellen Erstellung von Versandaufträgen bekommen Sie die von der REST API
zurückgemeldete Fehlermeldung direkt angezeigt. Bei der automatischen
Sendungserstellung werden Fehlermeldungen als Bestellkommentare an der betroffenen
Bestellung gespeichert. Wenn die Protokollierung in der Modulkonfiguration_
eingerichtet ist, können Sie fehlerhafte Versandaufträge auch in der Log-Datei
detailliert nachvollziehen.

.. admonition:: Hinweis

   Wenn Sie die automatische Sendungserstellung verwenden, prüfen Sie regelmäßig
   den Status Ihrer Bestellungen, um die wiederholte Übertragung invalider
   Versandaufträge zu vermeiden.

Fehlerhafte Versandaufträge können wie folgt manuell korrigiert werden:

* Im Popup zur Definition der im Paket enthaltenen Artikel können ungültige
  Zusatzleistungen abgewählt werden.
* Im Popup zur Definition der im Paket enthaltenen Artikel kann die
  Adressvalidierung für einen betroffenen Versandauftrag abgewählt werden, so
  dass DHL die kostenpflichtige Nachkodierung (Korrektur der Lieferadresse)
  übernimmt.
* In der Detail-Ansicht der Bestellung oder des Lieferscheins kann die
  Lieferadresse korrigiert werden. Betätigen Sie dazu den Link *Bearbeiten*
  in der Box *Versandadresse*.

  .. image:: images/de/edit_address_link.png
     :width: 10cm

  Im nun angezeigten Formular können Sie im oberen
  Bereich die Standard-Felder der Lieferadresse bearbeiten und im unteren Bereich
  die zusätzlichen, für den DHL Geschäftskundenversand spezifischen Felder:

  * Straße, Hausnummer und Adresszusatz
  * Packstation
  * Postfiliale
  * Paket-Shop


.. image:: images/de/edit_address_form.png
   :width: 12cm

Speichern Sie anschließend die Adresse. Wurde die Fehlerursache behoben, so kann
das manuelle `Erstellen eines Versandauftrags`_ erneut durchgeführt werden.

Wurde ein Versandauftrag über die REST API erfolgreich erstellt und sollen
dennoch nachträgliche Korrekturen vorgenommen werden, so stornieren Sie den
Versandauftrag wie im Abschnitt `Stornieren eines Versandauftrags`_ beschrieben
und betätigen Sie anschließend den Button *Paketaufkleber erstellen…* in
derselben Box *Versand- und Trackinginformationen*. Es gilt dasselbe Vorgehen
wie im Abschnitt `Erstellen eines Versandauftrags`_ beschrieben.

DHL Zusatzservices
~~~~~~~~~~~~~~~~~~

Bei Problemen mit `DHL Zusatzleistungen im Checkout`_ (z.B. Liefertag) werden die Fehlermeldungen
in eine separate Log-Datei geschrieben. Siehe Hinweise im Kapitel `Allgemeine Einstellungen`_.
Das Log enthält Hinweise zur weiteren Fehlersuche.

Beachten Sie auch die Hinweise zur `Buchbarkeit von Zusatzservices`_.

.. raw:: pdf

   PageBreak

Modul deinstallieren oder deaktivieren
======================================

Um das Modul zu **deinstallieren**:

1. Löschen Sie alle Moduldateien aus dem Dateisystem.
2. Entfernen Sie die im Abschnitt `Installation`_ genannten Adressattribute.
3. Entfernen Sie den zum Modul gehörigen Eintrag ``dhl_versenden_setup`` aus der Tabelle ``core_resource``.
4. Entfernen Sie die zum Modul gehörigen Einträge ``carriers/dhlversenden/*`` aus der Tabelle ``core_config_data``.
5. Leeren Sie abschließend den Cache.

Das Modul wird **deaktiviert**, wenn der Knoten ``active`` in der Datei
``app/etc/modules/Dhl_Versenden.xml`` von ``true`` auf ``false`` abgeändert wird.


Technischer Support
===================

Wenn Sie Fragen haben oder auf Probleme stoßen, werfen Sie bitte zuerst einen Blick in das
Support-Portal (FAQ): http://dhl.support.netresearch.de/

Sollte sich das Problem damit nicht beheben lassen, können Sie das Supportteam über das o.g.
Portal oder per Mail unter dhl.support@netresearch.de kontaktieren.
