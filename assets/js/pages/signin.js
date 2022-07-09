$(document).ready(function() {
	$('#buttonSignin').click(function(event) {
		event.preventDefault();

		const login = event.target.form.login.value;
		const password = event.target.form.password.value;

		const data = {
			login: login,
			password: password
		};

		$.ajax({
			url: '/authentication/signin_ajax',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			success: function(response, status, jqXHR) {
				if (response.status === 'SUCCESS') {

					$('#infoBlockMessage').removeClass('alert-danger').addClass('alert-success').text(response.message);
					$('#infoBlock').show();
					setTimeout(() => {
						$('#infoBlock').slideUp(1000);
					}, 3000);

					setTimeout(() => {
						location.href = '/';
					}, 4000);
				}
				else {
					let message = '';
					response.message.forEach((item, key) => {
						message += `
							<span>${item.message}</span><br>						
						`;
					});

					$('#infoBlockMessage').html(message);
					$('#infoBlock').show();
					setTimeout(() => {
						$('#infoBlock').slideUp(1000);
					}, 3000);
				}
			},
			error: function(jqXHR, status, errorThrown ){
				console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
			}
		});
	});

	// localStorage.removeItem('news');
});