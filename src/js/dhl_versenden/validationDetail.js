Validation.add(
    'validate-detail',
    'Your input is invalid.',
    function (v) {

        var exp = /\bPaketbox|\bPackstation|\bPostfach|\bPostfiliale|\bFiliale|\bPostfiliale Direkt|\bFiliale Direkt|\bPaketkasten|\bDHL|\bP-A-C-K-S-T-A-T-I-O-N|\bPaketstation|\bPack Station|\bP.A.C.K.S.T.A.T.I.O.N.|\bPakcstation|\bPaackstation|\bPakstation|\bBackstation|\bBakstation|\bP A C K S T A T I O N|\bWunschfiliale|\bDeutsche Post/g;

        return Validation.get('IsEmpty').test(v) || (!exp.test(v));
    }
);
