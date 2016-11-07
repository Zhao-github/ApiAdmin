var happy = {
    USPhone: function (val) {
        // return /^\(?(\d{3})\)?[\- ]?\d{3}[\- ]?\d{4}$/.test(val);
        return /^1[34578]\d{9}$/.test(val);
    },

    // matches mm/dd/yyyy (requires leading 0's (which may be a bit silly, what do you think?)
    date: function (val) {
        return /^(?:0[1-9]|1[0-2])\/(?:0[1-9]|[12][0-9]|3[01])\/(?:\d{4})/.test(val);
    },

    email: function (val) {
        return /^(?:\w+\.?\+?)*\w+@(?:\w+\.)+\w+$/.test(val);
    },

    minLength: function (val, length) {
        return val.length >= length;
    },

    maxLength: function (val, length) {
        return val.length <= length;
    },

    equal: function (val1, val2) {
        return (val1 == val2);
    }
};
