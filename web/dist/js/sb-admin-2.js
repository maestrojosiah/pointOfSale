/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
    $('#side-menu').metisMenu();
});

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

$('.toggleSale').click(function(){
    var $this = $(this);
    $this.toggleClass('toggleSale');
    if($this.hasClass('toggleSale')){
        $this.text('Sale');         
    } else {
        $this.text('Return');
    }
});


function loadShortcuts() {
	shortcut.add("F1", function() {
		alert("F1 should redirect to new sale");
	});
	shortcut.add("F3", function() {
		alert("F3 should redirect to new sale");
	});
	shortcut.add("F4", function() {
		alert("F4 should complete sale");
	});
	shortcut.add("F2", function() {
		$('#searchItem').focus();      
	});
	shortcut.add("F7", function() {
        $('#paidAmt').focus();      
	});
    shortcut.add("ESC", function() {
        alert("ESC should cancel the current sale");
    });
	shortcut.add("F6", function() {
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
        $('#valueHolder').addClass('changeRow_'+id);
        $('#valueHolder').focus().select();
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
