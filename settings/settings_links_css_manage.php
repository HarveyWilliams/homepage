<h3>Manage the links CSS files</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<ul>
		<?php
			$xml_file = '../data.xml';
			$xml = simpleXML_load_file($xml_file);
			$links_css = $xml->links->settings->css;
			
			if ($links_css == '../styles/links/default.css') {
				$selected = 'selected="selected"';
				$color = 'style="background-color:#d9d9d9;"';
			} else {
				$selected = '';
				$color = '';
			}
		?>
		<li <?php echo $color; ?>>
			Default
			<input class="hidden" type="radio" name="links_css_set" value="default" <?php echo $selected; ?>/>
		</li>
		<?php 
			$dir = '../styles/links/personal/';
			$css_files = glob($dir . '*.*');
			$i=0;
			
			foreach ($css_files as $css_file) {
			
				$css_file_basename = basename($css_file);
				
				if (basename($css_file) == basename($links_css)) {
					$selected = 'selected="selected"';
					$color = 'style="background-color:#d9d9d9;"';
				} else {
					$selected = '';
					$color = '';
				}
				
				echo "<li $color>
					$css_file_basename
					<input class='hidden' type='radio' name='links_css_set' value='styles/links/personal/$css_file_basename' $selected/>
					<span class='css_delete delete'></span>
					<input class='hidden' type='checkbox' value='$css_file' name='links_css_delete[]'/>
				</li>";
				$i++;
			}
		?>
	</ul>
	<script>
		$('.css_delete').click(function() {
			$(this).parent().hide();
			$(this).next('input[type=checkbox]').attr('checked', 'checked');
		});
		$('#settings_links_css_manage ul li').click(function() {
			$(this).parent().children().css('background-color', 'transparent');
			$(this).css('background-color', '#d9d9d9');
			$(this).children('input[type=radio]').prop('checked', true);
		});
	</script>
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
</script>