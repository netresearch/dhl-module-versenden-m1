Validation.add(
    'validate-with-neighbour',
    'You cannot use preferred neighbor with this service.',
    function (the_field_value) {
        return !(the_field_value != '' && $('shipment_service_preferredNeighbourDetails').value != '');
    }
);
