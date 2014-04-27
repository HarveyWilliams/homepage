<h3>Manage Backgrounds</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<ul>
		<?php
			$dir = "../graphics/backgrounds/";
			$images = glob($dir . '*.*');
			$i = 0;
			
			foreach($images as $image) {
				// basename because the php is being run in a different location than the core html file
				$image = basename($image);
				// get the extension of the selected image
				$ext = end(explode('.', $image));
				// if the selected file is a text file..,
				if ($ext == 'txt') {
					// the text file will be read, and the url inside will be copied
					$fh = fopen($image, 'r');
					$image = fread($fh, filesize($image));
					fclose($fh);
				} else {
					$image = 'graphics/backgrounds/'.$image;
				}
				echo "<li>";
					echo "<div class='backgrounds_wrapper'>";
						echo "<img src='$image'></img>";
						echo "<div class='background_delete'></div>";
						echo "<input class='hidden' type='checkbox' name='background_delete_#$i'/>";
					echo "</div>";
				echo "</li>";
				
				$i++;
			}
		?>
	</ul>
	<script>
		$('.background_delete').click(function() {
			$(this).parent().parent().hide();
			$(this).next('input[type=checkbox]').attr('checked','checked');
		});
	</script>
	<input class="update" type="submit" value="Update"></input>
</form>
<button class="back">Cancel</button>
<script>
	// lets the back button in the settings go back
	$('.back').click(function() {
		$(this).parent().hide();
		$('#settings_menu').show();
	});
</script>