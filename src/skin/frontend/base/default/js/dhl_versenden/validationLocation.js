Validation.add(
    'validate-with-location',
    'You cannot use drop-off location with this service.',
    function (the_field_value) {
         return !(the_field_value != '' && $('shipment_service_preferredLocationDetails').value != '');
    }
);


