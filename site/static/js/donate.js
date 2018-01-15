function hider(channel, no) {

    if (channel == 1) {

        var x = document.getElementById('donation1')
        var y = document.getElementById('regular1');
        if (x.className === 'hide') {
            if (no == 1) {
                y.className = 'hide';

                x.className = 'appear';
            }

        }
        if (y.className === 'hide') {
            if (no == 2) {
                y.className = 'appear';
                x.className = 'hide';

            }
        }

    }
    if (channel == 2) {

        var x = document.getElementById('donation2')
        var y = document.getElementById('regular2');
        if (x.className === 'hide') {
            if (no == 1) {
                y.className = 'hide';

                x.className = 'appear';
            }

        }
        if (y.className === 'hide') {
            if (no == 2) {
                y.className = 'appear';
                x.className = 'hide';


            }
        }

    }
}

/* For stripe payment.
Two seperate jquery functions are used in stripe payment for donation and regular supporter.
On clicking the donate button with ids customButton and customButtone respective fumctions 
will be called*/
(function() {
    var amount = 25;
    var handler = StripeCheckout.configure({
        key: 'pk_test_IHdlNeCcW1H44btA1bcjWXa9',
        image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/62/FOSSASIA_Logo.svg/2000px-FOSSASIA_Logo.svg.png',
        token: function(token) {

            $("#stripeToken").val(token.id);
            $("#stripeEmail").val(token.email);
            $("#amounts").val(amount * 100);
            $("#paymentMethod").val("stripe")
            $("#myForm").submit();
        }
    });
    $("#button1id").on("click", function() {
        var form = serialize($(this).closest("form")[0], true);
        amount = form.otheram || form.amount
        handler.open({
            name: 'Donate to fossasia',
            description: 'Donating: ($' + amount + ')',
            amount: amount * 100
        });
        return false
    });

    $("#myForm").on("change", "[name=amount]", function() {
        $("#otheram").val("")
    })
    $("#otheram").on("type change input paste", function() {
        $("#myForm [name=amount]").removeAttr("checked");
    });
})()

// Close Checkout on page navigation
$(window).on('popstate', function() {
    handler.close();
});

function createN(name, key) {
    return $("<input type='hidden'>").attr("name", name).attr("value", key)
}
$("#Paypal").click(function() {

    $("#myForm").attr("action", "https://www.paypal.com/cgi-bin/webscr").append(createN("business", "office@fossasia.org")).append(createN("cmd", "donations")).append(createN("item_name", "FOSSASIA friends")).append(createN("item_number", "Supportin FOSSASIA")).append(createN("currency_code", "USD"));

})
