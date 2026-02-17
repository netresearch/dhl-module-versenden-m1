/**
 * DHL Service Compatibility Engine
 *
 * Enforces product-service compatibility rules in the packaging popup.
 * Disables services not available for the selected product and handles
 * pDDP auto-enable.
 */
var DhlServiceCompatibility = Class.create({

    /**
     * @param {Object} rules - Compatibility rules from PHP block
     * @param {Object} rules.productServiceMatrix - Product code => service codes[]
     * @param {Object} rules.pddp - pDDP threshold config
     * @param {Object} rules.productRadioOptions - Product code => {serviceCode => allowedValues[]}
     */
    initialize: function (rules) {
        this.rules = rules;
        this.serviceContainer = $('packaging-dhlversenden-services');
        this.productContainer = $('packaging-dhlversenden-products');

        if (!this.serviceContainer || !this.productContainer) {
            return;
        }

        this.bindEvents();
        this.enforceProductRules();
        this.enforceRadioOptionRules();
        this.enforceServiceRules();
        this.enforceTextInputRules();
        this.enforcePddpRules();
    },

    /**
     * Bind change events on product radios and service checkboxes.
     */
    bindEvents: function () {
        var self = this;

        // Product radio changes
        this.productContainer.select('input[type=radio]').each(function (radio) {
            radio.observe('change', function () {
                self.enforceProductRules();
                self.enforceRadioOptionRules();
                self.enforceServiceRules();
                self.enforcePddpRules();
            });
        });

        // Service checkbox changes — re-evaluate all rules.
        // enforceProductRules resets enable/disable state, then enforceServiceRules
        // applies mutual exclusivity on top. enforceProductRules already calls
        // enforceTextInputRules internally.
        this.serviceContainer.select('input[type=checkbox]').each(function (checkbox) {
            checkbox.observe('change', function () {
                self.enforceProductRules();
                self.enforceServiceRules();
            });
        });
    },

    /**
     * Get the currently selected product code from the radio buttons.
     *
     * @return {String|null}
     */
    getSelectedProduct: function () {
        var checked = this.productContainer.down('input[type=radio]:checked');
        return checked ? checked.getValue() : null;
    },

    /**
     * Extract service code from a dt/dd element.
     * Reads the data-service-code attribute set by the PHP template.
     *
     * @param {Element} element
     * @return {String|null}
     */
    getServiceCode: function (element) {
        return element.readAttribute('data-service-code') || null;
    },

    /**
     * Disable/enable service rows based on selected product's allowed services.
     */
    enforceProductRules: function () {
        var selectedProduct = this.getSelectedProduct();
        if (!selectedProduct) {
            return;
        }

        var allowedServices = this.rules.productServiceMatrix[selectedProduct] || [];
        var self = this;

        // Process both checkbox and dropdown columns
        this.serviceContainer.select('dl dt').each(function (dt) {
            var serviceCode = self.getServiceCode(dt);
            if (!serviceCode) {
                return;
            }

            // Find the matching dd (next sibling)
            var dd = dt.next('dd');
            if (!dd) {
                return;
            }

            if (allowedServices.indexOf(serviceCode) === -1) {
                self.disableServiceRow(dt, dd);
            } else {
                self.enableServiceRow(dt, dd);
            }
        });

        // Re-apply input visibility rules after enable/disable changes
        this.enforceTextInputRules();
    },

    /**
     * Hide/show radio options within a service based on the selected product.
     * Used to restrict DeliveryType to Economy+Premium for V66WPI (no CDP).
     */
    enforceRadioOptionRules: function () {
        var productRadioOptions = this.rules.productRadioOptions;
        if (!productRadioOptions) {
            return;
        }

        var selectedProduct = this.getSelectedProduct();
        if (!selectedProduct) {
            return;
        }

        var allowedOptionsForProduct = productRadioOptions[selectedProduct];

        // For each service that has radio options, show/hide individual options
        this.serviceContainer.select('dd.service-radio').each(function (dd) {
            dd.select('div').each(function (optionDiv) {
                var radio = optionDiv.down('input[type=radio]');
                if (!radio) {
                    return;
                }

                if (!allowedOptionsForProduct) {
                    // No restrictions for this product — show all options
                    optionDiv.show();
                    radio.disabled = false;
                    return;
                }

                var serviceCode = radio.name.replace('service_setting[', '').replace(']', '');
                var allowedValues = allowedOptionsForProduct[serviceCode];
                if (!allowedValues) {
                    // No restrictions for this service — show all options
                    optionDiv.show();
                    radio.disabled = false;
                    return;
                }

                if (allowedValues.indexOf(radio.value) === -1) {
                    optionDiv.hide();
                    radio.disabled = true;
                    // If this hidden option was selected, select the first allowed option
                    if (radio.checked) {
                        radio.checked = false;
                        var firstAllowed = dd.down('input[type=radio][value="' + allowedValues[0] + '"]');
                        if (firstAllowed) {
                            firstAllowed.checked = true;
                        }
                    }
                } else {
                    optionDiv.show();
                    radio.disabled = false;
                }
            });
        });
    },

    /**
     * Disable a service row: add CSS class, disable inputs, uncheck checkboxes.
     *
     * @param {Element} dt
     * @param {Element} dd
     */
    disableServiceRow: function (dt, dd) {
        dt.addClassName('disabled');
        dd.addClassName('disabled');

        dt.select('input, select').each(function (input) {
            input.disabled = true;
            if (input.type === 'checkbox') {
                input.checked = false;
            }
        });

        dd.select('input, select').each(function (input) {
            input.disabled = true;
            if (input.type === 'checkbox') {
                input.checked = false;
            }
        });
    },

    /**
     * Enable a service row: remove CSS class, enable inputs.
     *
     * @param {Element} dt
     * @param {Element} dd
     */
    enableServiceRow: function (dt, dd) {
        dt.removeClassName('disabled');
        dd.removeClassName('disabled');

        dt.select('input, select').each(function (input) {
            if (!input.readAttribute('data-locked')) {
                input.disabled = false;
            }
        });

        dd.select('input, select').each(function (input) {
            if (!input.readAttribute('data-locked')) {
                input.disabled = false;
            }
        });
    },

    /**
     * Show/hide text, radio, and select input areas based on whether their checkbox is checked.
     */
    enforceTextInputRules: function () {
        var self = this;
        this.serviceContainer.select('dd.service-text, dd.service-radio, dd.service-select').each(function (dd) {
            var serviceCode = self.getServiceCode(dd);
            if (!serviceCode) {
                return;
            }

            var checkbox = $('shipment_service_' + serviceCode);
            // Locked (read-only) checkboxes: show value area if checked
            if (checkbox && checkbox.readAttribute('data-locked') && checkbox.checked) {
                dd.show();
                return;
            }
            if (checkbox && checkbox.checked && !checkbox.disabled) {
                dd.show();
            } else {
                dd.hide();
            }
        });
    },

    /**
     * Enforce service-to-service mutual exclusivity rules.
     * When a master service checkbox is checked, disables the subject service.
     */
    enforceServiceRules: function () {
        var serviceRules = this.rules.serviceRules;
        if (!serviceRules) {
            return;
        }

        var self = this;
        serviceRules.each(function (rule) {
            var masterCheckbox = $('shipment_service_' + rule.master);
            if (!masterCheckbox) {
                return;
            }

            var subjectDt = self.serviceContainer.down('dt.' + rule.subject);
            var subjectDd = subjectDt ? subjectDt.next('dd') : null;
            if (!subjectDt || !subjectDd) {
                return;
            }

            if (masterCheckbox.checked && !masterCheckbox.disabled && rule.action === 'disable') {
                self.disableServiceRow(subjectDt, subjectDd);
            }
        });
    },

    /**
     * Handle pDDP auto-enable logic for USA shipments.
     * Pre-checks the pDDP checkbox when order value is under threshold.
     */
    enforcePddpRules: function () {
        var pddp = this.rules.pddp;
        if (!pddp) {
            return;
        }

        var checkbox = $('shipment_service_postalDeliveryDutyPaid');
        if (!checkbox || checkbox.disabled) {
            return;
        }

        if (pddp.recipientCountry !== 'US') {
            return;
        }

        // Determine applicable threshold
        var threshold = null;
        if (pddp.currency === 'EUR') {
            threshold = pddp.thresholdEur;
        } else if (pddp.currency === 'USD') {
            threshold = pddp.thresholdUsd;
        }

        if (threshold === null) {
            return;
        }

        var dt = this.serviceContainer.down('dt.postalDeliveryDutyPaid');
        var dd = this.serviceContainer.down('dd.postalDeliveryDutyPaid');

        // Remove any existing tooltip
        if (dt) {
            var existingTooltip = dt.down('.dhl-tooltip');
            if (existingTooltip) {
                existingTooltip.remove();
            }
        }

        if (pddp.orderValue < threshold) {
            // Auto-check pDDP
            checkbox.checked = true;
            if (dt && pddp.tooltipTemplate) {
                var tooltip = pddp.tooltipTemplate.replace('%s', threshold).replace('%s', pddp.currency);
                dt.insert(new Element('span', {'class': 'dhl-tooltip'}).update(tooltip));
            }
        }
    }
});
