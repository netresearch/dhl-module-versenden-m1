<?php
/**
 * Timeframe
 *
 * PHP version 5
 *
 * @category Class
 * @package  Dhl\Versenden\Cig
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * DHL Parcel Management API
 *
 * # Introduction ## Overview  The DHL Parcel Management API focuses on services (i.e. REST resources) that enable web API clients to query available service options and to influence shipments during transit. Web API clients may act on behalf of a sender and, e.g., stop an in-transit shipment before delivery. Or they act on behalf of an recipient, thereby setting a preferred drop location or neighbour in case the original recipient is absent at the time of delivery.  ## Terminology   DHL Parcel Management API enables clients to explore current `offers` for shipments. Moreover, clients can query  existing orders of a given shipment or place new `orders`. During checkout, the DHL Parcel Management API allows for query an estimated delivery date as well as available DHL services.  From the viewpoint of DHL three **actors** play an important role in parcel logistics processes: - `shipment sender`: The sender, which typically is some company selling its goods online and shipping them in shipments, a.k.a. parcels. - `DHL`: The logistics company that fulfils the shipping process. The last mile transport and the actual delivery of a shipment is carried out by a DHL *deliverer*. - `shipment recipient`: The customer of the sender that finally receives the shipment.  Each of these actors are considered to have different **types of systems** - `sender's system`: The `sender's system` typically might be a `web shop system` which particularly serves  `rendered web pages`. From the viewpoint of the `DHL Parcel Management API`, the `sender's system` is the `API client`. - `DHL Parcel Management API`: This server (hostname `cig.dhl.de`) actually serves the web API (`API server`) which is specified in this document.  - `recipient's user agent`: The user agent of the `sender's customer` / `shipment recipient`. A user  agent might be a common web `browser` aimed at the sender's web site or an `sender's app` which is installed on the recipient's smartphone or tablet. ```                           +--------------------------+ shipment recipient /      |recipient's user agent    | sender's customer         |(browser / sender's app)  |                           +----------^---+-----------+                                      |   |                          rendered web|   | * offer selection                          pages       |   | * preferredLocation descriptions etc.                                      |   |                           +----------+---v-----------+ shipment sender /         |sender's system           | DHL business customer     |(e.g. web shop system)    |                           |API client                |                           +----------^---+-----------+                                      |   |                       API responses  |   | * requests to DHL Parcel Management API                                      |   | * checkout:                                      |   |    * query (GET) estimated delivery date                                      |   |    * query (GET) available service options                                      |   | * in-transit shipments:                                      |   |    * query (GET) current orders and offers                                      |   |    * place (PUT) new orders                           +----------+---v-----------+ DHL                       |DHL Parcel Management API |                           |(cig.dhl.de)              |                           |API server                |                           +--------------------------+ ```  ## Example scenarios A common sales and shipping process that involves the DHL Parcel Management API might look like this: ### Scenario 1: Checkout 1. The recipient aims his or her browser the sender's (web shop) system, places items in the shopping cart and does a checkout. 2. Based on the recipient's data, in particular his or her address, the sender queries the planned delivery date and possible DHL services and shows these data in the checkout module. 3. The recipient can select one or more DHL services.  4. Based on the recipient's data and selected DHL services, the sender places a shipment order. DHL offers diverse channels for placing shipment orders. In particular, there is another SOAP-based [Business customer shipping API](https://entwickler.dhl.de/en/group/ep/wsapis/geschaeftskundenversand) for doing so. In any case, ordering a shipment yields a shipment label and a `shipmentId` that uniquely identifies that particular shipment.  #### Estimated shipment date The planned delivery day can be queried using the DHL Parcel Management API. To this end, the sender's system has to dispatch a request to the DHL's web API server:  ``` GET https://cig.dhl.de/services/production/rest/checkout/12345/deliveryDayEstimation?startParcelCenter=20&startDate=2017-11-30 ``` The respective response contains a delivery day estimation:  ``` HTTP/1.1 200 OK ... {   \"estimatedDeliveryDay\": {     \"start\": \"2017-12-01T00:00:00.000+01:00\",     \"end\": \"2017-12-01T23:59:59.999+01:00\"   } } ``` #### Available services for the recipient address Available services can be queried based on the zip code of the recipient using the DHL Parcel Management API. To this end, the sender's system has to dispatch a request to the DHL's web API server:  ``` GET https://cig.dhl.de/services/production/rest/checkout/12345/availableServices ``` The respective response contains a delivery day estimation:  ``` HTTP/1.1 200 OK ... {   \"preferredLocation\": {     \"available\": true   },   \"preferredNeighbour\": {     \"available\": true   },   \"preferredDay\": {     \"available\": true     \"validDays\": [       {         \"start\": \"2016-12-11T23:00:00.000Z\",         \"end\": \"2016-12-12T22:59:59.999Z\"       },       {         \"start\": \"2016-12-12T23:00:00.000Z\",         \"end\": \"2016-12-13T22:59:59.999Z\"       },       {         \"start\": \"2016-12-13T23:00:00.000Z\",         \"end\": \"2016-12-14T22:59:59.999Z\"       }     ]   },   \"preferredTime\": {     \"available\": true,     \"timeframes\": [       {         \"start\": \"18:00\",         \"end\": \"21:00\",         \"code\": \"033\"       },       {         \"start\": \"19:00\",         \"end\": \"21:00\",         \"code\": \"032\"       }     ]   },   \"inCarDelivery\": {     \"available\": true   },   \"sameDayDelivery\": {     \"available\": true,     \"sameDayTimeframes\": [       {         \"start\": \"18:00\",         \"end\": \"21:00\",         \"code\": \"033\",         \"DenselyPopulatedAreaId\": \"100\",         \"DenselyPopulatedAreaName\": \"Berlin\",         \"deliveryBaseId\": \"B-JOS\"       },       {         \"start\": \"19:00\",         \"end\": \"21:00\",         \"code\": \"032\",         \"DenselyPopulatedAreaId\": \"100\",         \"DenselyPopulatedAreaName\": \"Berlin\",         \"deliveryBaseId\": \"B-JOS\"       }     ]   },   \"noNeighbourDelivery\": {     \"available\": true   } } ``` ### Scenario 2: In-Transit 1. During transit of the shipment, the recipient comes to the conclusion that he or she won't be able to receive the shipment personally. Instead, he or she prefers to instruct the deliverer to place the shipment in  the right light shaft. Thus, he or she revisits the sender's web site,  navigates to the in-transit shipments and selects the particular shipment (identified by the `shipmentId`). 2. The `sender's system` serves a web page that renders the current shipment's `delivery state` and current valid `recipient offers` that can be turned into `recipient orders` in order to influence the actual  delivery of the shipment. In essence, this web page looks like this (after the recipient has made her/his input):  ``` +----------------------------------------------------------+  |  shipment id:       003412345                            | |                                                          | |  delivery state:    ✓ data sent                         | |                     ✓ picked up                         | |                        out for delivery                  | |                        delivered                         |  |                                                          | |                                                          | |  recipient orders: [none]                                | |                                                          | |  recipient offers: [x] preferred location:               | |                        [ ] garage                        | |                        [ ] terrace                       | |                        [ ] summer house                  | |                        [x] other:                        | |                        +---------------------+           | |                        | right light shaft   |           | |                        +---------------------+           | |                                                          | |                    [ ] preferred neighbour:              | |                          salutation*: [ ] Herr  [ ] Frau | |                          first name*:                    | |                          +---------------------+         | |                          |                     |         | |                          +---------------------+         | |                          last name*:                     | |                          +---------------------+         | |                          |                     |         | |                          +---------------------+         | |                          street*:                        | |                          +---------------------+         | |                          |                     |         | |                          +---------------------+         | |                          house number*:                  | |                          +---------------------+         | |                          |                     |         | |                          +---------------------+         | |                          additional address:             | |                          +---------------------+         | |                          |                     |         | |                          +---------------------+         | |                                                          | |                    [ ] preferred day:                    | |                        [ ] 12/22/2016                    | |                        [ ] 12/23/2016                    | |                                                          | |                                             +--------+   | |                                             | order! |   | |                                             +--------+   | +----------------------------------------------------------+ ``` Please note, that we distinguish between initial shipment orders (scenario 1) and orders that are applied to shipments which are already in transit (scenario 2). This DHL Parcel Management API revolves around the latter sort of orders. However, this API assists during checkout by showing possible services which can be ordered via the SOAP-based [Business customer shipping API](https://entwickler.dhl.de/en/group/ep/wsapis/geschaeftskundenversand).  #### Delivery state In order to render the shipments `delivery state`, the sender's system can fetch the state using DHL's [Shipment tracking API](https://entwickler.dhl.de/en/group/ep/wsapis/sendungsverfolgung).       #### Current recipient orders Current recipient orders for the particular in-transit shipment can be queried using the DHL Parcel Management API. As explained below in detail, the sender's system has to dispatch a request to the DHL's web API server:       ``` GET https://cig.dhl.de/services/production/rest/shipments/003412345/orders ```    The respective response contains orders for the respective shipment that have already been placed. In our scenario none of these exist at this point in time, so the response basically is empty:  ``` HTTP/1.1 200 OK ... { } ```  #### Current recipient offers Current `recipient offers` for the particular in-transit shipment can also be queried using the DHL Parcel Management API.  To this end, the sender's system has to dispatch a request to the DHL's web API server:  ``` GET https://cig.dhl.de/services/production/rest/shipments/003412345/offers ``` The respective response contains valid current offers for the respective shipment.  ``` HTTP/1.1 200 OK ... {     \"preferredLocation\": {      \"commitmentEndPeriod\": \"2016-12-21T23:10:00.000+01:00\"   },   \"preferredNeighbour\": {      \"commitmentEndPeriod\": \"2016-12-21T23:10:00.000+01:00\"    },   \"preferredDay\": {      \"commitmentEndPeriod\": \"2016-12-21T23:20:50.520+01:00\",      \"validDays\": [        { \"start\": \"2016-12-22T00:00:00.000+01:00\", \"end\": \"2016-12-22T23:59:59.999+01:00\" },       { \"start\": \"2016-12-23T00:00:00.000+01:00\", \"end\": \"2016-12-23T23:59:59.999+01:00\" }     ]   } } ``` It is up the the sender's system to render and integrate the offers appropriately within its web page.  #### Order a prefferred location Given that a `preferredLocation` is among the offers, the recipient  selects it on the web page and adds order specific information. In this case, he or she enters a  `description` the specific preferred location, namely `right light shaft`. When the recipient clicks on the `order!`-button the sender's system issues a   ``` PUT https://cig.dhl.de/services/production/rest/shipments/003412345/orders/preferredLocation ... {    \"description\" : \"right light shaft\"  } ``` request to the DHL web API server which responses with   ``` HTTP/1.1 201 Created ... {   \"statusCode\" = \"201\",    \"statusText\" = \"order has been created.\" } ``` #### Checking the order After some minutes the recipient again visits the web page of step 4. Again, the calls to the DHL Parcel Management API are made. However, this time fetching orders via request  ``` GET https://cig.dhl.de/services/production/rest/shipments/003412345/orders ``` yields a response  ``` HTTP/1.1 200 OK ... {   \"preferredLocation\": {     \"creationTime\": \"2016-12-21T09:32:15.000+01:00\",     \"lastUpdate\": \"2016-12-21T09:32:15.000+01:00\",     \"description\": \"right light shaft\"   } } ```  Moreover, again fetching the offers   ``` GET https://cig.dhl.de/services/production/rest/shipments/003412345/offers ``` leads to a comparably sparse response  ``` HTTP/1.1 200 OK ... {     \"preferredDay\": {      \"commitmentEndPeriod\": \"2016-12-21T23:20:50.000+01:00\",      \"validDays\": [        { \"start\": \"2016-12-22T00:00:00.000+01:00\", \"end\": \"2016-12-22T23:59:59.999+01:00\" },       { \"start\": \"2016-12-23T00:00:00.000+01:00\", \"end\": \"2016-12-23T23:59:59.999+01:00\" }     ]   } } ``` since some offers have been ruled out since the last call. So, the sender's  system might now  render the web page like this  ``` +--------------------------------------------------------+ |  shipment id:       003412345                          | |                                                        | |  delivery state:    ✓ data sent                       | |                     ✓ picked up                       | |                        out for delivery                | |                        delivered                       | |                                                        | |                                                        | |  recipient orders: preferred loc.: \"right light shaft\" | |                                                        | |  recipient offers: [ ] preferred day:                  | |                        [ ] 12/22/2016                  | |                        [ ] 12/23/2016                  | |                                                        | |                                            +--------+  | |                                            | order! |  | |                                            +--------+  | +--------------------------------------------------------+ ```  ## Web API Design Decisions and Current Limitations  ### Best Practices  We adhere to best practices concerning the design of WebAPIs. In particular, we follow principles that  make an API \"RESTful\". Among the [plethora of design guide lines](http://apistylebook.com/design/guidelines/) we mainly follow [the recommendations made by Zalando](http://zalando.github.io/restful-api-guidelines/).  ### Versioning  [Following a recommendation](http://zalando.github.io/restful-api-guidelines/compatibility/Compatibility.html), the web API *itself* avoids explicit versioning and is supposed to keep backward compatibility in the future.  ### Scheme (Protocol)  The API merely supports scheme `https`. In contrast `http` is not supported for security reasons.  ### Media Types  Presently, the API supports just plain [`application/json`](https://tools.ietf.org/html/rfc7159) within bodies of requests and responses. Support for hypermedia dialects like [`application/hal+json`](http://stateless.co/hal_specification.html) might be introduced in future revisions.  ### URIs and HTTP-Methods  Each shipment might have at most 1 order of a certain type. Therefore, a particular shipment cannot have two active  preferredLocation orders with might contain contradicting `description`s. In order to reflect this, we identify the preferredLocation order of some a shipment with, e.g., shipmentId 003412345 by the URI ``` https://cig.dhl.de/services/production/rest/shipments/003412345/orders/preferredLocation ``` and allow for applying [HTTP-method `PUT`](https://tools.ietf.org/html/rfc2616#section-9.6) on this resource identifying URI. `PUT` is associated with creating or updating a resource identified with the given URI and considered [idempotent in terms of RFC2616 (Hypertext Transfer Protocol -- HTTP/1.1)](https://tools.ietf.org/html/rfc2616#section-9.1.2). Repeatedly sending identical requests like ``` PUT https://cig.dhl.de/services/production/rest/shipments/003412345/orders/preferredLocation ... {    \"description\" : \"garage\"  } ``` have no other side effects than sending it just once. Due to the idempotency it is safe for the client to resend a request if the client is unsure about whether the first request reached its destination, e.g., because of a timeout before the response.  Nonwithstanding, a first `PUT` like the one above on a non-existing resource might be responded with [HTTP status code 201 - Created](https://tools.ietf.org/html/rfc2616#section-10.4.4) but a second might be rejected with a [HTTP status code 403 - Forbidden](https://tools.ietf.org/html/rfc2616#section-10.4.4) due to (variable) update restrictions on certain orders. We elaborate on the specific behaviour within the descriptions of the respective paths below.  ### Date and Time  This document is written in the standard [OpenAPI Specification Language](http://swagger.io/specification/), which prescribes   [RFC3339](http://xml2rfc.ietf.org/public/rfc/html/rfc3339.html#anchor14) for date and time values.   RFC3339 allows for defining date-times in a human readable yet unambiguous manner. Though RFC3339 allows for some flexibility, we consistently use a single date-time format throughout this API, which is like  `2016-12-21T23:20:50.520+01:00`. Here `2016-12-21` denotes the day which is  separated by a `T` from the time `23:20:50.520` which is accurate to one microsecond. Offsets like `+01:00` are used  to represent the same time in different time zones for even better human readability. For example, `2016-12-21T23:20:50.520Z`, `2016-12-22T08:20:50.520+09:00`, and `2016-12-21T16:20:50.520-07:00` represent the very same microsecond, where Z means \"zulu military time\", i.e., offset 00:00.  RFC3339 date-times are convertible to unix epoc timestamps: In Java, the following expression is true ``` 1482362450L == (new java.text.SimpleDateFormat(\"yyyy-MM-dd'T'HH:mm:ss.SSSXXX\").parse(\"2016-12-22T00:20:50.520+01:00\")).getTime() / 1000L ```  ## Performance Considerations  As we outlined in section \"Example scenario\", this web API might be called by shipment senders in order to populate a shipment tracking web page. This web page might be embedded in the web site of the shipment sender which is finally rendered in the browser of some shipment recipient. Particularly in this setup overall latency -- as experienced by the shipment recipient -- needs to be kept low and calls to this  web API should be avoided if possible. Therefore, responses SHOULD be served from web caches that are local to the shipment sender's systems whenever possible.  That is why responses of this web API are equipped with common HTTP headers like `Cache-Control` and `Expires`.  ## Internal Documentation Design Decisions and Current Limitations  This document is written in the [OpenAPI Specification Language](http://swagger.io/specification/). Therefore, it can be conveniently edited and browsed using tools like [Swagger Editor](http://editor.swagger.io/#/edit) and [SWAGGERhub](https://swaggerhub.com/).  There are numerous ways to specify the very same API, each of which as its advantages and drawbacks. First of all, in contrast to the API itself, this web API *documentation* is versioned. In short,  our versioning scheme has [BREAKCOMPATIBILITY.KEEPCOMPATIBILITY.INTERNALDRAFT semantics](http://zalando.github.io/restful-api-guidelines/compatibility/Compatibility.html#should-provide-version-information-in-openapi-documentation). That is, we increase BREAKCOMPATIBILITY if we remove or replace elements (e.g., methods, resources, properties) and therefore break compatibility. We increase KEEPCOMPATIBILITY if we just add new elements to the API or do refactorings, e.g., within the `definitions` that affect models in Swagger-UI and generated code.  We increase INTERNALDRAFT in case of internal unpublished revisions. Since the first resource (service) for shipmentStop has already been released, we begin counting at 1.0.1 and publish either 1.1.0 or 2.0.0 depending on whether we will have to break backward compatibility. Please note that draft revisions with INTERNALDRAFT > 0 specify services (resources) that might not been implemented so far.  This document is read by human readers as well as processed by UI and code generators. We had to find some compromises to satisfy both. For example we have introduced more indirections to and among `definitions` a mere human reader would expect. However by doing so, we avoid \"inline models\" that are ugly in Swagger-UI an even more in generated source code.  # Change Log ## 1.1.4 * added validation patterns for ``recipientZip`` and ``startParcelCenter`` * description of 404 error in checkout ## 1.1.3 * added EKP parameter to all resources ## 1.1.2 * removed readonly tags which break specification since required fields must not be readonly * added available preferred days * new parameter `startDate` (required to calculate possible preferred days) ## 1.1.1 * delivery date estimation * allow for query available DHL services ## 1.1.0  * fixed markup to support PDF generation * removed unimplemented `localRerouting` from specification * add optional `X-Request-ID` `HTTP-Header` for HTTP request correlation * `TimeInterval.start` and `TimeInterval.end` are no longer `readOnly: true` as `TimeInterval` is also used in requests of PUT-Request and therefore must be writeable * `maxLength` of `PreferredLocationOrder` reduced to `80` (was `100`). * Extended list of prohibited `description`s for `PreferredLocationOrder` * Added regular expression `pattern`s for input fields of `preferredNeighbour` and `preferredLocation`.   These `pattern`s defined valid characters which will be particularly displayed properly on scanning   devices of deliverers.    * Removed key `preferredDay` from request body of `PUT /shipments/{shipmentId/orders/preferredDay`   since it was redundant as well as inconsistent with the other PUT request bodies. * Camel case for `statusCode` and `statusText` in responses in order to be consistent with other attributes * Minor corrections in the example scenario * *`end` in `TimeInterval`*: The `end` in `TimeInterval`s now is of the form    `2016-12-21T23:59:59.999+01:00`, i.e., now has a `date` segment `2016-12-21` that is equal    to that of `start` in `validDays`  * *structured preferred neighbour order*: Placing a new `preferredNeighbour` order now expects    a structured set of properties instead of a plain free text field `description`  * *Time slots*: Common type `TimeInterval` for `validDays` and `preferredDay` and forthcoming offer types with shorter time intervals. For now, these time intervals are used in preferredDay offers and preferredDay orders. * Removed status code `410 - Gone` * *Idempotency of PUT*: Added some explanations about the idempotency of `PUT` requests in \"URIs and HTTP-Methods\" and the description of every `PUT` method. * *GET orders example*: Extended example for `GET /shipments/{shipmentId}/orders`. The example now has two current orders. * *Valid preferred locations*: Extended explanation of what can, should and must not be passed in the `description` of a `preferredLocation`.  * *Valid preferred neighbours*: Extended explanation of how the `description` of a preferred neighbour should look like. * *Recent orders*: Extended explanation for `GET  /shipments/{shipmentId}/orders`, in order to clarify that this ressource just contains the most recent order property values but no order history. * New sections \"Terminology\" and \"Example scenario\" that illustrate the usage of this API  * Added rationale for RFC3339 format in subsection \"Date and Time\" * Added rationale for using HTTP-method `PUT` in subsection \"URIs and HTTP-Methods\"  * Added rationale and contraints for free text field `description` in `PreferredLocationOrder` * Added HTTP status code 403 (Forbidden) * Removed supplemental order data (postnumber, email, mobile number, shipmentname) from responses of   `GET /shipments/{shipmentId}/orders` * Some additional remarks about the `commitmentEndPeriod` * Some additional remarks for `shipmentStop` (and its difference from other orders)  ## 1.0.0  * initial version  # Specification
 *
 * OpenAPI spec version: 1.1.4
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.0-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Dhl\Versenden\Cig\Model;

use \ArrayAccess;
use \Dhl\Versenden\Cig\ObjectSerializer;

/**
 * Timeframe Class Doc Comment
 *
 * @category Class
 * @description A time interval &#x60;[start,end]&#x60;, i.e., beginning at &#x60;start&#x60; and ending at &#x60;end&#x60; independet to an actual date. Each timeframe has an individual code which is given in &#x60;code&#x60;.
 * @package  Dhl\Versenden\Cig
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Timeframe implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Timeframe';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'start' => 'string',
        'end' => 'string',
        'code' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'start' => 'time',
        'end' => 'time',
        'code' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'start' => 'start',
        'end' => 'end',
        'code' => 'code'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'start' => 'setStart',
        'end' => 'setEnd',
        'code' => 'setCode'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'start' => 'getStart',
        'end' => 'getEnd',
        'code' => 'getCode'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['start'] = isset($data['start']) ? $data['start'] : null;
        $this->container['end'] = isset($data['end']) ? $data['end'] : null;
        $this->container['code'] = isset($data['code']) ? $data['code'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['start'] === null) {
            $invalidProperties[] = "'start' can't be null";
        }
        if ($this->container['end'] === null) {
            $invalidProperties[] = "'end' can't be null";
        }
        if ($this->container['code'] === null) {
            $invalidProperties[] = "'code' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets start
     *
     * @return string
     */
    public function getStart()
    {
        return $this->container['start'];
    }

    /**
     * Sets start
     *
     * @param string $start start
     *
     * @return $this
     */
    public function setStart($start)
    {
        $this->container['start'] = $start;

        return $this;
    }

    /**
     * Gets end
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->container['end'];
    }

    /**
     * Sets end
     *
     * @param string $end end
     *
     * @return $this
     */
    public function setEnd($end)
    {
        $this->container['end'] = $end;

        return $this;
    }

    /**
     * Gets code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->container['code'];
    }

    /**
     * Sets code
     *
     * @param string $code code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->container['code'] = $code;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


