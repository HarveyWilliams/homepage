<?php
	$xml_file = '../data.xml';
	$xml = simpleXML_load_file($xml_file);
?>
<div id="select">
	<h3>Select a link to edit</h3>
	<div id="list_container">
		<ul class="list">
			<?php
				// this creates a row for each link
				for ($i=0; $i<sizeof($xml->links->link); $i++) { 
					// get data from each link in the xml
					$link_name = $xml->links->link[$i]->name;
					$link_icon = $xml->links->link[$i]->icon;
					$link_background_colour = $xml->links->link[$i]->background_colour;
					$link_icon_colour = $xml->links->link[$i]->icon_colour;
					$link_url = $xml->links->link[$i]->url;
					$link_font = $xml->links->link[$i]->icon_font;
					
					echo "
					<li
						link_number='link_$i'
						name='$link_name' icon='$link_icon'
						background_colour='$link_background_colour'
						icon_colour='$link_icon_colour'
						url='$link_url'
						font='$link_font'>
						$link_name
					</li>";
				}
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<button class="back">Cancel</button>
</div>

<div class="hidden" id="edit">
	<h3>Edit the selected link</h3>
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
		<div class="show-and-hide-content">
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
							echo "<li><img class='icon' src='$image'></img><img class='tick' src='./graphics/tick.png'><input type='radio' name='link_icon_choose' value='$image' style='display:none;'/></li>";
						}
						
					?>
					</ul>
				</div>
				<label for="icon_colour">Icon Colour:</label>
				<input type="text" name="icon_colour" class="colour_picker" id="icon_colour" value="#ffffff"/>
			</div>
			<div class="icons content content-choose_personal">
				<ul>
				<?php
					$dir = '../graphics/icons/personal/';
					$images = glob($dir . '*.*');
					foreach ($images as $image) {
						$image = 'graphics/icons/personal/'.basename($image);
						echo "<li></img><img class='icon' src='$image'></img><img class='tick' src='graphics/tick.png'><input type='radio' name='link_icon_choose' value='$image' style='display:none;'/></li>";
					}
				?>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
		<input class="update" type="submit" value="Update"/>
	</form>
	<button class="back">Cancel</button>
</div>

<script>
	// fills out the form depending on which link was chosen to be edited
	$('.list li').click(function() {
		var link_number = $(this).attr('link_number');
		
		var name = $(this).attr('name');
		var icon = $(this).attr('icon');
		var background_colour = $(this).attr('background_colour');
		var icon_colour = $(this).attr('icon_colour');
		var url = $(this).attr('url');
		var font = $(this).attr('font');
		
		$('#link_name').val(name);
		$('#link_font').val(font);
		$('#link_url').val(url);
		$('#background_colour').val(background_colour);
		$('#icon_colour').val(icon_colour);
		
		// if no background colour has been set (from the xml) then make it black
		if (background_colour!='') {
			$("#background_colour").spectrum ({
				color: background_colour
			});
		} else {
			$("#background_colour").spectrum ({
				color: '#000000'
			});
		}
		
		// if no icon colour has been set (from the xml) then make it white
		if (icon_colour!='') {
			$("#icon_colour").spectrum ({
				color: icon_colour
			});
		} else {
			$("#icon_colour").spectrum ({
				color: '#ffffff'
			});
		}
		
		$('#select').toggle();
		$('#edit').toggle();
	});
	
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
	
	// selects a radio button and shows a tick on an li when that li is clicked on
	$('#settings_links_new li').click(function() {
		$(this).children('input[type=radio]').prop('checked', true);
		$('.tick').hide();
		$(this).children('.tick').show();
	});
	
	// javascript color picker
	$(".colour_picker").spectrum({
		showInput: true,
		className: "colour_picker",
		showInitial: false,
		showPalette: true,
		showSelectionPalette: true,
		maxPaletteSize: 10,
		preferredFormat: "hex",
		localStorageKey: "spectrum.demo",
		move: function (color) {
			
		},
		show: function () {
		
		},
		beforeShow: function () {
		
		},
		hide: function () {
		
		},
		change: function() {
			
		},
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