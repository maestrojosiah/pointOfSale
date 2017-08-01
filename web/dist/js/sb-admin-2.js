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
    } else {
        $this.text('Hide Items');
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
		alert("F2 should focus on item search");
	});
	shortcut.add("F7", function() {
		alert("F7 should focus on amount paid field");
	});
	shortcut.add("ESC", function() {
		alert("ESC should cancel the current sale");
	});
}

window.onload=init;//Loads the correct sidebar on window load,
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
