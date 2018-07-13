Validation.add(
    'validate-text',
    'Your input is not allowed',
    function (the_field_value) {

        var exp = /\bPaketbox|\bPackstation|\bPostfach|\bPostfiliale|\bFiliale|\bPostfiliale Direkt|\bFiliale Direkt|\bPaketkasten|\bDHL|\bP-A-C-K-S-T-A-T-I-O-N|\bPaketstation|\bPack Station|\bP.A.C.K.S.T.A.T.I.O.N.|\bPakcstation|\bPaackstation|\bPakstation|\bBackstation|\bBakstation|\bP A C K S T A T I O N|\bWunschfiliale|\bDeutsche Post/g;

        return the_field_value.match(exp);
    }
);
