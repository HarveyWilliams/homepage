<?php
	$xml_file = '../data.xml';
	$xml = simpleXML_load_file($xml_file);

	$background_fill_type = $xml->backgrounds->settings->fill_type;
?>
<h3>Backgrounds</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<table class="table1">
		<tbody>
			<tr>
				<th>Fill type:</th>
				<td>
					<select name="background_fill_type">
						<option value="fill" <?php if($background_fill_type=="fill") { echo "selected='selected'"; } ?>>Fill</option>
						<option value="stretch" <?php if($background_fill_type=="stretch") { echo "selected='selected'"; } ?>>Stretch</option>
						<option value="tile" <?php if($background_fill_type=="tile") { echo "selected='selected'"; } ?>>Tile</option>
						<option value="center" <?php if($background_fill_type=="center") { echo "selected='selected'"; } ?>>Center</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Change type:</th>
				<td>
					<select name="background_change_type">
						<option value="change">Change on page reload</option>
						<option value="static">Static</option>
					<select>
				</td>
			</tr>
		</tbody>
	</table>
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