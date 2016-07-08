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

    initServiceContainer: function (elementId) {
        if (elementId) {
            this.serviceContainer = $(elementId);
        }
    },

    initDhlMethods: function (dhlMethods) {
        if (dhlMethods) {
            this.dhlMethods = dhlMethods.evalJSON(true);
            observeShippingEvents.call();
        }
    },

    toggleServiceContainer: function (formId) {
        var inputs = $(formId).select("input:checked[name=shipping_method]");
        if (inputs.length) {
            var selectedMethod = inputs[0].value;
            var canDisplayServices = (this.dhlMethods.indexOf(selectedMethod) != -1);
            if (canDisplayServices) {
                this.serviceContainer.show();
            } else {
                this.serviceContainer.hide();
            }
        }
    },

    registerMethodChange: function (formId) {
        var methodInputs = Form.getInputs(formId, 'radio', 'shipping_method');
        methodInputs.each(function (input) {
            input.observe('change', function () {
                this.toggleServiceContainer(formId);
            }.bind(this));
        }.bind(this));
    }
};

function observeShippingEvents() {

    $$('.input-with-checkbox').each(function (element) {
        var toggleSelect = 'unchecked';
        var selectElement = $(element.readAttribute('data-select-id'));

        element.observe('keyup', function () {
            if (this.value != '' && toggleSelect == 'unchecked') {
                selectElement.checked = true;
                toggleSelect = 'checked';
            } else if (this.value == '' && toggleSelect == 'checked') {
                selectElement.checked = false;
                toggleSelect = 'unchecked';
            }
        });
    });
}
