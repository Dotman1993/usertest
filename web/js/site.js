$(document).ready(function () {
	var dataTableOptions = {
		'language': {
			'url': '//cdn.datatables.net/plug-ins/1.10.21/i18n/Russian.json'
		},
		'stateSave': true,
	};
	
	var dataTable = $('#TestTable').DataTable(dataTableOptions);
	
	$('body').on('click', '.delete-link', function(e) {
		e.preventDefault();
		
		var url = $(this).attr('href'),
		container = $(this).attr('data-pjax'),
		result = confirm('Вы уверены, что хотите удалить?');
		
		if(result) {
			$.ajax({
				url: url,
				type: 'post',
				error: function(xhr, status, error) {
					alert('Произошла ошибка.' + xhr.responseText);
				}
			}).done(function(data) {
				console.log('success: ' + data['success']);
				$.pjax.reload(container, {timeout: 3000});
			});
		}
		return false;
	});
	
	$(document).on('click', '#create-user-link', function(e){
		var modal = $($(this).data('target'));
		modal.find('.modal-body').load($(this).attr('href'));
	});
	
	$(document).on('click', '#create-random-user-link', function(e){
		e.preventDefault();
		
		$.ajax({
			url: $(this).attr('href'),
			type: 'get',
			error: function(xhr, status, error) {
				alert('Произошла ошибка.' + xhr.responseText);
			}
		}).done(function(data) {
			console.log('success: ' + data['success']);
			$.pjax.reload('#users_pjax', {timeout: 3000});
		});
	});
	
	$(document).on('beforeSubmit', '.site-index form', function(e){
		e.preventDefault();
		
		var data = $(this).serialize();
		
		$.ajax({
			url: $(this).attr('action'),
			type: 'post',
			data: data,
			success: function(data){
				if (data['success'] == true) {
					console.log('success: ' + data['success']);
					$.pjax.reload('#users_pjax', {timeout: 3000});
					$('.modal.in').modal('hide');
				}
			},
			error: function(xhr, status, error) {
				alert('Произошла ошибка.' + xhr.responseText);
			}
		});
		return false;
	});
	$(document).on('hidden.bs.modal', function(e) {
		$(e.target).find('.modal-body').html('');
	});
	
	$(document).on('pjax:end ready', function() {
		dataTable = $('#TestTable').DataTable(dataTableOptions);
	});
});