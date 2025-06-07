var BILLING = function() {
    var tbl_billing = $('#tbl_billing',document);
    var handler_billing = function() {
        handler_tbl_billing();
    }
    var handler_tbl_billing = function() {
        PECO.DTDefault(tbl_billing, 'Select GDLB First..');

    };
    return {
        list: function() {
            handler_billing();
        }
    }
}();