<?php 
	//Access is prohibited for unauthorized users and non-administrators
	if (!$core->isUserAuthorized() ) header('Location: main');
	if (!$core->isAdmin() ) header('Location: main');


	if (isset($_POST) && isset($_POST['title'])) {
		$res = $core->saveMovie($_POST);
		if ($res['ok']) {
			header("Location: " . $res['route']);
		}
	}

	include_once($app['base'] . 'pages/page_header.php'); 
?>

<div style="text-align:center">
<script>
	window.addEventListener("DOMContentLoaded", function(){
		<?php if (isset($res) && isset($res['ok']) && !$res['ok']) { echo 'Swal.fire({ icon: "error", title: "' . $res['message'] . '", text: "", });'; } ?>
	});
	
	function movieSave(){
		var event = window.event; event.preventDefault();
		 if (document.getElementById("title").value.length == 0) {
			 Swal.fire({ icon: "error", title: "<?php echo __('Enter Title'); ?>", text: "", });
		 }
		 $('#movieForm').submit();
	}
	function movieDelete(){
		var event = window.event; event.preventDefault();
		var id = $("input[name=id]").val();
		Swal.fire({
		  title: "<?php echo __('Are you sure'); ?>?", text: "<?php echo __('You want to delete this movie'); ?>?", icon: "warning", showCancelButton: true, confirmButtonColor: "#3085d6", cancelButtonColor: "#d33", 	  confirmButtonText: "<?php echo __('Delete'); ?>", showClass: {popup: `animate__animated animate__fadeInDown animate__faster`},hideClass: {popup: `animate__animated animate__fadeOutUp animate__faster`}
		  }).then((result) => {
		  if (result.isConfirmed) {
			  
			$.ajax({ url:"pages/ajax_data.php", type: "POST", dataType: 'json', cache: false, data: {'act':'deleteMovie', 'id':id }, success: function(qdata){
					if (qdata && qdata.ok) {
						Swal.fire({ title: "<?php echo __('Deleted'); ?>!", text: "<?php echo __('Your movie has been deleted'); ?>", icon: "success"});
						document.location = qdata.route;
					} else {
						Swal.fire({ title: "<?php echo __('Error'); ?>!", text: qdata.err_txt, icon: "error"});
					}
				}
			}).then( function() { });
			
		  }
		});
	}
</script>	
<?php

	$shortlink = $core->sanitize($app['route'][$debug_index+1]);
	$film = $core->getOneMovie($shortlink);
	$form = $core->getMovieForm($film);
	echo $form['content'];

?>	

</div>
	
<?php include_once($app['base'] . 'pages/footer.php');  ?>