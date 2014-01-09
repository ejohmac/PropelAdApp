<html>
<head>

</head>
<body>
<div id="fb-root"></div>
<script>

	$(document).ready(function() {

		$.ajaxSetup({ cache: true });
		$.getScript('//connect.facebook.net/en_UK/all.js', function(){

			FB.init({
				appId: '415153341951879',
				status     : true, // check login status
				cookie     : true, // enable cookies to allow the server to access the session
				xfbml      : true  // parse XFBML
			});

			
			FB.Event.subscribe('auth.authResponseChange', function(response) {
				// Here we specify what we do with the response anytime this event occurs. 
				if (response.status === 'connected') {
					
					FB.api('/me', function(response) {
						$('#greeting').append('<h1>Hello '+response.name+'</h1>');
					});
				} else if (response.status === 'not_authorized') {
					
					FB.login();
				} else {
					
					FB.login();
				}
			});

		});

		$("#getButton").bind('click', function(event) {

			FB.api('/me', function(response) {

				var userID = response.id;

				FB.api('/me/friends', function(response) {

					if(response.data) {

						// Save to server			

						var dataArray = {};
						dataArray[userID] = response.data;

						$.ajax({

							url:"/download",
							data:dataArray,
							type:"POST",
							dataType:"JSON",
							cache:false,

							success: function(response) {

								$("#msgModalLabel").text(response.msg);
								
							},
							error: function(xhr, status ) {

								$("#msgModalLabel").text("Download to DB Error");

							},
						 
							complete: function(xhr, status ) {
								$("#msgModal").modal('show');
								$('.modal-body #modalMessage').empty();
								$('.modal-body #modalMessage2').empty();
							}


						});
						
					} else {
					   $("#msgModalLabel").html('Oops theres a problem');
					}
				});
			});

		});

		$("#showButton").bind('click', function(event) {

			FB.api('/me', function(response) {

				var userID = response.id;

				$.ajax({

					url:"/download/retreive?id="+userID,
					type:"GET",
					dataType:"JSON",
					cache:false,

					success: function(response) {

						if (response.data) {
							data = response.data;
						
							$.each(data, function(index, value) {
							
								$('.modal-body #modalMessage').append(value+"<br/>");
							});
							$('.modal-body #modalMessage2').append("<strong>They have now been deleted from the database</strong>");
						} else {
							$('.modal-body #modalMessage').empty();
							$('.modal-body #modalMessage2').empty();
						}
						
						$("#msgModalLabel").text(response.msg);
					},
					error: function(xhr, status ) {
						$("#msgModalLabel").text("Error");
					},
				 
					complete: function(xhr, status ) {
						$("#msgModal").modal('show');
					}


				});

			});

		});

		
	});

</script>

<div class="hero-unit">

	<div class="row-fluid">
		<div class="span12 text-center">
			<div id="greeting"></div>
				<fb:login-button show-faces="true" width="600" max-rows="1"></fb:login-button>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span6 text-center">
		
		<button type="button" class="btn btn-primary" id="getButton"><i class="fa fa-download"></i> Download My Friends</button>
		
	</div>

	<div class="span6 text-center">

		<button type="button" class="btn btn-primary" id="showButton"><i class="fa fa-bolt"></i> Show Me My Friends</button>	
		
	</div>
</div>

<div id="msgModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<h3 id="msgModalLabel"></h3>
	</div>
	<div class="modal-body">
		<p id="modalMessage"></p>
		<p id="modalMessage2"></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
	</div>
</div>

</body>
</html>
