/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
// $(function() {
//     $('#side-menu').metisMenu();
// });

$('.toggleButton').click(function(){
    var $this = $(this);
    $this.toggleClass('toggleButton');
    if($this.hasClass('toggleButton')){
        $this.text('Show Items');
        console.log('hidden');
        $("#category").hide(700);
    } else {
        $this.text('Hide Items');
        console.log('hidden');
        $("#category").show(700);        
    }
});

$(document).on("click", '.toggleSale', function(){
    var $this = $(this);
    if($this.text() == 'Sale'){
        $this.text('Return');   
        console.log($this.text());
        $("#action").html(
            '<button type="button" id="salesReturn" class="btn btn-primary"><strong>Submit Return</strong></button>'
        );
        $("#rightSide").hide();
        $("#totalsRecord").hide()
        $("#changeRecord").hide()

    } else if($this.text() == 'Return') {
        $this.text('Stock In');
        console.log($this.text());      
        $("#action").html(
            '<button type="button" id="stockIn" class="btn btn-primary"><strong>Submit Stock Received</strong></button>'
        );
        $("#rightSide").hide();
        $("#totalsRecord").hide()
        $("#changeRecord").hide()
    } else if($this.text() == 'Stock In') {
        $this.text('Sale');
        console.log($this.text());   
        $("#action").html("");   
        $("#rightSide").show();
        $("#totalsRecord").show()
        $("#changeRecord").show()
    }
});


function loadShortcuts() {
	shortcut.add("F1", function() {
        if($("#amountDue").text() == ""){
            $("#infoParagraphAuto").html("<span class='text-danger'>There is no receipt to print. Please make a sale first</span><br />Hit F7 to focus");
            $("#infoModalAuto").modal('show');
        } else {
		  $("#sendPrint").click();
        }
	});
	shortcut.add("F2", function() {
		$('#searchItem').focus();      
	});
	shortcut.add("F7", function() {
        $('#paidAmt').focus();      
	});
    shortcut.add("F3", function() {
        $("#showSuspendModal").click();
    });
	shortcut.add("F6", function() {
        if($("#amountDue").text() == ""){
            $("#infoParagraphAuto").html("<span class='text-danger'>No sale to update quantity</span><br />Hit F7 to focus");
            $("#infoModalAuto").modal('show');
        } else {
            var lastQtySpan = $('#sales tr:last td:nth-last-child(2) span');
            var lastQtyColumn = $('#sales tr:last td:nth-last-child(2)');
            var lastQtyValue = lastQtyColumn.text();
            var trimmedValue = $.trim(lastQtyValue);
            var id = lastQtySpan.attr("id");
            console.log(lastQtyValue);
            $("#changeValueModal").modal('show');
            $("#valueHolder").val(trimmedValue);
            $("#valueHolder").removeClass (function (index, className) {
                return (className.match (/(^|\s)changeRow_Qt_\S+/g) || []).join(' ');
            });
            $('#changeValueModal').on('shown.bs.modal', function () {
              $('#valueHolder').focus().select();
            })                  
            $('#valueHolder').addClass('changeRow_'+id);
            $('#valueHolder').focus().select();
        }
	});
}

//window.onload=init;//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }
});
