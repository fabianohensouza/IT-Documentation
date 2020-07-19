/*!
    * Start Bootstrap - SB Admin v6.0.1 (https://startbootstrap.com/templates/sb-admin)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
   (function($) {
    "use strict";

    // Add active state to sidbar nav links
    /*var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
            if (this.href === path) {
                $(this).addClass("active");
            }
        });*/

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });
})(jQuery);

$(document).ready(() => {
	
	$(document).ready(function() {
    	$("a.coleta-id").click(function(event) {
        	//alert(event.target.id);
        	id = event.target.id

        	if (id == 'dashboard') {
        		location.reload();
        	} else {
                component = "components/dashboard/" + id + ".phtml"
                console.log(component)

                //$('#pagina').load(component)
                $.get(component, data => {
                    $('#layoutSidenav_content').html(data)
                })
        	}
        	
    	});
	});

	//Ajax Method
	$('#competencia').on('change', e => {

		let competencia = $(e.target).val()

		//Ajax Method parameters - Method/URL/Data/Data Type/Success/Error
		$.ajax({
			type: 'GET',
			url: 'app.php',
			data: `competencia=${competencia}`, //x-www-form-urlencoded - name1=value1&name2=value2&name3=value3
			dataType: 'json',
			//success: data => { console.log(data)},
			success: data => { 

				$('#numerovendas').html(data.numeroVendas)
				$('#totalvendas').html(data.totalVendas)

			},
			error: error => { console.log(error)}
		})

	})
})