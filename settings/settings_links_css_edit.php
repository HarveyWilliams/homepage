<h3>Edit the links CSS</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<div class="show-and-hide-content">
		Select the file you wish to edit:
		<select name="links_css_edit_file">
			<option>Select a file...</option>
			<?php
				$dir = '../styles/links/personal/';
				$css_files = glob($dir . '*.*');
				$i=0;
				foreach ($css_files as $css_file) {
					$css_file_basename = basename($css_file);
					echo "<option value='$css_file' data-type='$i'>$css_file_basename</option>";
					$i++;
				}
			?>
		</select>
		<?php
			$i=0;
			foreach ($css_files as $css_file) {
				// find the contents of the css file
				$fh = fopen($css_file, 'r');
				$css_text = fread($fh, filesize($css_file));
				fclose($fh);
				$css_file_basename = basename($css_file);
				
				echo "<textarea class='content content-$i' name='css'>$css_text</textarea>";
				$i++;
			}
		?>
		<br />
		Use this file after update<input type="checkbox" name="links_css_edit_use" checked="checked"/>
	</div>
	<div class="clear"></div>
	<input class="update" type="submit" value="Update"></input>
</form>
<button class="back">Cancel</button>
<script>
	// lets the back button in the settings go back
	$('.back').click(function() {
		$(this).parent().hide();
		$('#settings_menu').show();
	});
	
	// allows an inputted option to show
	$(function () {
		$('.show-and-hide-content').each(function (i) {
			var $row = $(this);
			// radios
			var $radios = $row.find('input');
			$radios.on('change', function () {
				var type = $(this).attr('data-type');
				$row
					.find('.content').hide()
					.filter('.content-' + type)
						.show();
			});
			// dropdown boxes
			var $selects = $row.find('select');
			$selects.on('change', function () {
				var type = $(this).find('option:selected').attr('data-type');
				$row
					.find('.content').hide().prop('disabled', true)
					.filter('.content-' + type)
					// any textboxes which are not being shown must be disabled as the content will be sent either way
					// this is to fix the problem where if all the textboxes have the same name, only the information from the last text box will be posted
						.show().prop('disabled', false);
			});
		});
	});
</script>