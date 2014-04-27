<h3>New Background</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<div class="show-and-hide-content">
		<h4>Decide how you want to choose a new background image:</h4>
		<input type="radio" name="background_type" value="upload" data-type="true"/>Upload
		<input type="radio" name="background_type" value="url" data-type="false"/>Give a URL from the internet
		<input type="file" name="background_upload" class="hidden content content-true" style="display:none;"></input>
		<input style="width:200px;" type="text" name="background_url" placeholder="image url" autocomplete="off"  class="hidden content content-false" />
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