<h3>Create a new links CSS file</h3>
	<form enctype="multipart/form-data" action="action.php" method="POST">
		<p>Name</p>
		<input type="text" name="links_css_new_name"/>
		<p>CSS Content</p>
		<textarea class="textarea_max" name="links_css_new_content"></textarea>
		<input class="update" type="submit" value="Update"></input>
		Use this file after update<input type="checkbox" name="links_css_new_use" checked="checked"/>
	</form>
<button class="back">Cancel</button>
<script>
	// lets the back button in the settings go back
	$('.back').click(function() {
		$(this).parent().hide();
		$('#settings_menu').show();
	});
</script>