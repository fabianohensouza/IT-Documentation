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
	
	//Carregando páginas complementares dentro do elemento #layoutSidenav_content
		
    	$("a.lateral-captura-id").click(function(event) {
        	
        	id = event.target.id

			if (id == 'dashboard') {
        		location.reload();
        	} else {
                component = "components/dashboard/" + id + ".phtml"
                console.log(component)

                $.get(component, data => {
                    $('#layoutSidenav_content').html(data)
				})
				
				$('.active').removeClass('active')
				$('#'+id).addClass('active')
        	}
        	
		});

		$(document).ready(function() {
			
			$("a.painel-captura-id").on('click', function(event) {
				
				id = event.target.id
				component = "components/dashboard/" + id + ".phtml?user=null"
	
				$.get(component, data => {
					$('#layoutSidenav_content').html(data)
				})
				
			});

		$(document).on('click','a.painel-captura-id',function(event){

			id = event.target.id
			console.log(id)
			component = "components/dashboard/" + id + ".phtml"
			console.log(component)
	
			$.get(component, data => {
				$('#layoutSidenav_content').html(data)
			})
		  
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