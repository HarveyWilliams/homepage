<h3>Manage Clocks</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<ul>
		<?php
			$xml_file = '../data.xml';
			$xml = simpleXML_load_file($xml_file);
			$clock = $xml->clock;
			if ($clock == 'none') {
				$selected = 'selected="selected"';
				$color = 'style="background-color:#d9d9d9;"';
			} else {
				$selected = '';
				$color = '';
			}
		?>
		<li <?php echo $color; ?>>
			None
			<input class="hidden" type="radio" name="clocks_set" value="None" <?php echo $selected; ?>/>
		</li>
		<?php 
			$dir = '../js/clock/';
			$js_files = glob($dir . '*.*');
			
			$i=0;
			
			foreach ($js_files as $js_file) {
			
				$js_file_basename = preg_replace("/\\.[^.\\s]{2}$/", "", basename($js_file));
				
				if ($js_file_basename == $clock) {
					$selected = 'selected="selected"';
					$color = 'style="background-color:#d9d9d9;"';
				} else {
					$selected = '';
					$color = '';
				}
				
				echo "<li $color>
					$js_file_basename
					<input class='hidden' type='radio' name='clocks_set' value='$js_file_basename' $selected/>
					<span class='js_delete delete'></span>
					<input class='hidden' type='checkbox' value='$js_file_basename' name='clocks_delete[]'/>
				</li>";
				$i++;
			}
		?>
	</ul>
	<script>
		$('.js_delete').click(function() {
			$(this).parent().hide();
			$(this).next('input[type=checkbox]').attr('checked', 'checked');
		});
		$('#settings_clocks_manage ul li').click(function() {
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