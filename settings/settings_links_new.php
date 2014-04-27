<h3>New links</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<label for="link_name">Name:</label>
	<input type="text" name="link_name" id="link_name" class="new_links" autocomplete="off" required="required"/>
	<br />
	<label for="link_font">Font:</label>
	<input type="text" name="link_font" id="link_font" class="new_links" autocomplete="off" list="fonts"/>
	<datalist id="fonts">
		<option value='Arial, "Helvetica Neue", Helvetica, sans-serif;'>Arial</option>
		<option value='Futura, "Trebuchet MS", Arial, sans-serif;'>Futura</option>
		<option value='"Helvetica Neue", Helvetica, Arial, sans-serif;'>Helvetica</option>
		<option value='Optima, Segoe, "Segoe UI", Candara, Calibri, Arial, sans-serif;'>Optima</option>
		<option value='Cambria, Georgia, serif;'>Cambria</option>
		<option value='Rockwell, "Courier Bold", Courier, Georgia, Times, "Times New Roman", serif;'>Rockwell</option>
		<option value='TimesNewRoman, "Times New Roman", Times, Baskerville, Georgia, serif;'>Times New Roman</option>
		<option value='Consolas, monaco, monospace;'>Consolas</option>
		<option value='"Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;'>Courier New</option>
		<option value='"Lucida Sans Typewriter", "Lucida Console", Monaco, "Bitstream Vera Sans Mono", monospace;'>Lucida Sans Typewriter</option>
		<option>Custom...</option>
	</datalist>
	<br />
	<label for="link_url">URL:</label>
	<input type="text" name="link_url" id="link_url" class="new_links" autocomplete="off" required="required"/>
	<br />
	<label for="background_colour">Background:</label>
	<input type="text" name="background_colour" class="colour_picker" id="background_colour" value="#000000"/>
	<br />
	<label for="icon_colour">Icon Colour:</label>
	<input type="text" name="icon_colour" class="colour_picker" id="icon_colour" value="#ffffff"/>
	<br />
	<label for="link_icon">Icon:</label>
	<input name="link_icon" class="pic" id="link_icon"/>
	<br />
	<div class="show-and-hide-content hidden" id="choose_icon">
		<h4>Decide how you want to choose the link icon:</h4>
		<input type="radio" name="icon_type" value="upload" data-type="upload"/>Upload
		<input type="radio" name="icon_type" value="choose_archive" data-type="choose_archive"/>Archive
		<input type="radio" name="icon_type" value="choose_personal" data-type="choose_personal"/>Personal Uploads
		<input type="file" name="link_icon_upload" class="content content-upload"/>
		<div class="content content-choose_archive">
			<div class="icons">
				<ul>
				<?php
					$dir = '../graphics/icons/';
					$images = glob($dir . '*.*');
					foreach ($images as $image) {
						$image = 'graphics/icons/'.basename($image);
						echo "<li>
								<img class='icon' src='$image'/>
							</li>";
					}
					
				?>
				</ul>
			</div>
		</div>
		<div class="icons content content-choose_personal">
			<ul>
			<?php
				$dir = '../graphics/icons/personal/';
				$images = glob($dir . '*.*');
				foreach ($images as $image) {
					$image = 'graphics/icons/personal/'.basename($image);
					echo "<li>
							<img class='icon' src='$image'/>
						</li>";
				}
			?>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
	<input class="update" type="submit" value="Update"/>
</form>
<button class="back">Cancel</button>
<script>
	// lets the back button in the settings go back
	$('.back').click(function() {
		$(this).parent().hide();
		$('#settings_menu').show();
	});
	
	// sets the icon preview background to be equal to the selected colour
	$('#background_colour').on('change', function() {
		background_colour = $(this).val();
		$('#link_icon').css('background-color', background_colour);
	});
	
	// sets the icon preview icon colour to be equal to the selected colour
	$('#icon_colour').on('change', function() {
		icon_colour = $(this).val();
		$('#link_icon').css('fill', icon_colour);
	});
	
	// allows the user to choose an icon
	$('#link_icon').click(function() {
		$('#choose_icon').toggle();
	});
	
	// when that li is clicked on, the image is sent to the #icon (to let the user know that something has happened), and the value of #link_icon is set the the image
	$('#settings_links_new li').click(function() {
		var image = $(this).children('.icon').attr('src');
		$('#link_icon').css('background-image', 'url("'+image+'")');
		$('#link_icon').val(image);
		$('#choose_icon').hide();
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
	
	// javascript color picker
	$("#background_colour").spectrum ({
		color: "#000000",
	});
	
	$("#icon_colour").spectrum ({
		color: "#ffffff",
	});
	$(".colour_picker").spectrum({
		showInput: true,
		className: "colour_picker",
		showInitial: false,
		showPalette: true,
		showSelectionPalette: true,
		maxPaletteSize: 10,
		preferredFormat: "hex",
		localStorageKey: "spectrum.demo",
		palette: [
			["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
			"rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
			["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
			"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
			["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
			"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
			"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
			"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
			"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
			"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
			"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
			"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
			"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
			"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
		]
	});
</script>