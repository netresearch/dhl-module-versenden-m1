/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  design
 * @package   base_default
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
var DhlServiceContainer = Class.create();

DhlServiceContainer.prototype = {
    initialize: function (elementId, dhlMethods) {
        this.initServiceContainer(elementId);
        this.initDhlMethods(dhlMethods);
    },

    /**
     * Locate the service info container for future reuse.
     * @param {string} elementId - The service container id.
     */
    initServiceContainer: function (elementId) {
        if (elementId) {
            this.serviceContainer = $(elementId);
        }
    },

    /**
     * Set the DHL Versenden shipping methods for future reuse.
     * @param {string} dhlMethods - JSON encoded method codes
     */
    initDhlMethods: function (dhlMethods) {
        if (dhlMethods) {
            this.dhlMethods = dhlMethods.evalJSON(true);
        }
    },

    /**
     * Toggle service info container visibility based on currently selected shipping method.
     */
    toggleServiceContainer: function () {
        if (this.serviceContainer instanceof Element) {
            var inputs = this.serviceContainer.up('form').select("input:checked[name=shipping_method]");
            if (inputs.length) {
                var selectedMethod = inputs[0].value;
                var canDisplayServices = (this.dhlMethods.filter(
                    function (element) {
                      return selectedMethod.indexOf(element) != -1;
                    }).length > 0);
                if (canDisplayServices) {
                    this.serviceContainer.show();
                } else {
                    this.serviceContainer.hide();
                }
            }
        }
    },

    /**
     * Perform action when user selects another shipping method.
     */
    registerMethodChange: function () {
        if (this.serviceContainer instanceof Element) {
            var methodInputs = this.serviceContainer.up('form').getInputs('radio', 'shipping_method');
            methodInputs.each(function (input) {
                input.observe('change', function () {
                    this.toggleServiceContainer();
                }.bind(this));
            }.bind(this));
        }
    },

    /**
     * Perform action when user changes service details.
     */
    registerServiceDetailsChange: function () {
        if (this.serviceContainer instanceof Element) {
            this.serviceContainer.select('input[data-select-id]').each(function (inputElm) {
                // hide service selectors
                var serviceCheckbox = $(inputElm.readAttribute('data-select-id'));
                serviceCheckbox.hide();

                // (un)check service selector based on user input
                inputElm.observe('keyup', function (event) {
                    serviceCheckbox.checked = (event.findElement().value != '');
                });
            });
        }
    },

    /**
     * Perform action when user checked preferred day or preferred time.
     */
    registerCalendarChange: function () {
        var currentClass = this;
        var idRadioElement = ['shipment_service_preferredDay','shipment_service_preferredTime'];
        idRadioElement.each( function (id) {
            currentClass.getSeviceListener(id);
        });
    },

    /**
     * Change CSS Class and set radio as checked or unchecked
     * @param idRadioElement
     */
    getSeviceListener: function (idRadioElement) {
        var classNameRadioChecked = 'radio-checked';
        if (this.serviceContainer instanceof Element) {
            this.serviceContainer.select('[id^=' + idRadioElement + '_]').each(function (radioElm) {
                radioElm.observe('click', function () {
                    if (!this.hasClassName(classNameRadioChecked)) {
                        $$('[id^=' + idRadioElement + '_]').each(function (checked) {
                            checked.removeClassName(classNameRadioChecked);
                        });
                        $(idRadioElement).checked = true;
                        this.addClassName(classNameRadioChecked);
                    }
                });
            });
        }
    },
};
