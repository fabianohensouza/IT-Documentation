$(document).ready(() => {
	
	$(document).ready(function() {
    	$("a.coleta-id").click(function(event) {
        	//alert(event.target.id);
        	id = event.target.id

        	if (id == 'dashboard') {
        		console.log('Reload')
        		//location.reload();
        	} else {
        		console.log(id+".phtml")
        	}

        	component = 'https://google.com'

        	//$('#pagina').load(component)
        	$.get(component, data => {
			$('#pagina').html(data)
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