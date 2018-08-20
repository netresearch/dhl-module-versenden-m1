Validation.add(
    'validate-special',
    'Your input is invalid.',
    function (v) {
        var expSpec = /["+;<>,.']|\\/g;

        return Validation.get('IsEmpty').test(v) || (!expSpec.test(v));
    }
);
