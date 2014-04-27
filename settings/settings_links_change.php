<?php
	$xml_file = '../data.xml';
	$xml = simpleXML_load_file($xml_file);
?>
<h3>Reorder the links</h3>
<form enctype="multipart/form-data" action="action.php" method="POST">
	<div id="list_container">
		<ul id="sortable">
			<?php
				// this creates a row for each link
				for ($i=0; $i<sizeof($xml->links->link); $i++) { 
					$link_name = $xml->links->link[$i]->name;
					
					echo "<li id='item-$i'>
						$link_name
						<input type='hidden' name='link_sort[]' value='$i'/>
						<span class='link_delete delete'></span>
						<input class='hidden' type='checkbox' value='checked' name='link_delete[]'/>
						<input class='hidden' type='checkbox' value='unchecked' name='link_delete[]' checked='checked'/>
					</li>";
				}
			?>
		</ul>
		<p id="link_new">Add a new link</p>
		<input class="hidden" type="checkbox" name="link_new_check"></input>
		<div class="clear"></div>
		<input class="update" type="submit" value="Update"></input>
	</div>
</form>
<button class="back">Cancel</button>
<script>
	// lets the back button in the settings go back
	$('.back').click(function() {
		$(this).parent().hide();
		$('#settings_menu').show();
	});
	
	// allows users to delete links,
	$('.link_delete').click(function() {
		$(this).parent().hide();
		$(this).next('input[type=checkbox]').attr('checked', true);
		$(this).next().next('input[type=checkbox]').attr('checked', false);
	});
	
	// mootools sortables
	window.addEvent('domready', function(){
		var mySortables = new Sortables('#sortable', {
			clone: true
		});
	});
</script>