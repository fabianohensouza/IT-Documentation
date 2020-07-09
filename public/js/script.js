$(document).ready(() => {
	
	$(document).ready(function() {
    	$("a.coleta-id").click(function(event) {
        	//alert(event.target.id);

        	id = event.target.id
        	component = '/components/dashboard/' + id + '.phtml'

        	if (id == 'dashboard') {
        		location.reload();
        	} else {
        		$('#pagina').load(component)

        		$("a").removeClass("active")
        		$('.sr-only').remove()

        		$('#'+id+'').addClass("active")
        	}
		})
    })

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