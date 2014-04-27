<?php
	$xml_file = 'data.xml';
	$xml = simpleXML_load_file($xml_file);
	$sxe = new SimpleXMLElement($xml->asXML());
	
	function xml_reformat() {
		global $xml_file;
		// this reformats the xml file so it remains readable
		if( !file_exists($xml_file) ) die('Missing file: ' . $xml_file);
		else {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dl = @$dom->load($xml_file); // remove error control operator (@) to print any error message generated while loading.
			if ( !$dl ) die('Error while parsing the document: ' . $xml_file);
			$dom->save($xml_file);
		}
	}
	
	function clean($input) {
		return htmlspecialchars(strip_tags($input));
	}
	
	// if any data has been posted
	if (!empty($_POST)) {
		
		/*
		###################
		Link Updates
		###################
		*/
		
		// if a new link has been set...
		if (isset($_POST['link_name'])) {
			$link_name = clean($_POST['link_name']);
			$link_colour = clean($_POST['link_colour']);
			$link_url = clean($_POST['link_url']);
			//! icon name still needs sanatizing
		
			$link = $sxe->links->addChild('link');
			$link->addChild('name', $link_name);
			
			switch ($_POST['icon_type']) {
				case "upload":
					$name = $_FILES["link_icon_upload"]["name"];
					$ext = end(explode(".", $name));
					if ($ext == 'jpg' || $ext == 'png') {
						// the background image will be saved
						$target_path = "graphics/icons/personal/";
						$target_path = $target_path . basename($_FILES['link_icon_upload']['name']); 
						move_uploaded_file($_FILES['link_icon_upload']['tmp_name'], $target_path);
					}
					$link->addChild('icon', 'personal/'.$name);
				break;
				case "choose_archive":
					$link->addChild('icon', basename($_POST['link_icon_choose']));
				break;
				case "choose_personal":
					$link->addChild('icon', 'personal/'.basename($_POST['link_icon_choose']));
				break;
			}
			
			$link->addChild('colour', $link_colour);
			$link->addChild('url', $link_url);
			$sxe->asXML($xml_file);
		}
		
		// if a change has been made to the link order
		if (isset($_POST['link_sort'])) {
		
			$link_sort = $_POST['link_sort'];
			
			// find out if the order has changed by checking the position of the new order against the old one
			for ($i=0; $i<sizeof($xml->links->link); $i++) {
				if ($link_sort[$i]!=$i) {
					$wrong_position = 1;
					// exit the if and for early
					break 1;
				}
				
			}
			
			// if the link order has been confirmed to be changed
			if (isset($wrong_position)) {
				$links_xml_size = sizeof($xml->links->link);
				$links = $sxe->links;
				// save the current links
				for ($i=0; $i<$links_xml_size; $i++) {
					// this works by saving the first link
					$links_name[$i] = (string) $links->link[0]->name;
					$links_icon[$i] = (string) $links->link[0]->icon;
					$links_colour[$i] = (string) $links->link[0]->colour;
					$links_url[$i] = (string) $links->link[0]->url;
					
					// then deleting it
					unset($links->link[0]);
					$sxe->asXML($xml_file);
				}
				
				for ($i=0; $i<$links_xml_size; $i++) {
					$x = $link_sort[$i];
					
					$link = $links->addChild('link');
					$link->addChild('name', $links_name[$x]);
					$link->addChild('icon', $links_icon[$x]);
					$link->addChild('colour', $links_colour[$x]);
					$link->addChild('url', $links_url[$x]);
					$sxe->asXML($xml_file);
				}
			}
		}
		
		// if a change has been made to the current links...
		// the for statement has to be done by going down the list of links rather than up
		// this is because if it deletes, for example, 2 and 3, and it is going in ascending order, it will delete 2, 3 will take 2s position, and when the program deletes 3 it will be acutally deleting 4
		if (isset($_POST['link_delete'])) {
			$link_delete = $_POST['link_delete'];
			for ($i=sizeof($xml->links->link)-1; $i>-1; $i--) {
				// if any of the delete checkboxes have been set, then the following will be true and that link will be set to be deleted
				if ($link_delete[$i]=='checked') {
					unset($sxe->links->link[$i]);
					$sxe->asXML($xml_file);
				}
			}
		}
		
		/*
		-------------------
		Link CSS Updates
		-------------------
		*/
		
		// if a new links css file has been created
		if (isset($_POST['links_css_new_name'])) {
			$dir = 'styles/links/personal/';
			$fh = fopen($dir.$_POST['links_css_new_name'].'.css', 'w');
			fwrite($fh, $_POST['links_css_new_content']);
			fclose($fh);
			
			if(isset($_POST['links_css_new_use'])) {
				$links = $sxe->links;
				unset($links->settings->css);
				$links->settings->addChild('css', $dir.$_POST['links_css_new_name'].'.css');
				$sxe->asXML($xml_file);
			}
		}
		
		// if a change has been made to the current links css
		if (isset($_POST['links_css_edit_file'])) {
			$fh = fopen($_POST['links_css_edit_file'], 'w');
			fwrite($fh, $_POST['css']);
			fclose($fh);
			if (isset($_POST['links_css_edit_use'])) {
				$links = $sxe->links;
				unset($links->settings->css);
				$links->settings->addChild('css', $_POST['links_css_edit_file']);
				$sxe->asXML($xml_file);
			}
		}
		
		// if a css file has been selected to be deleted
		if (isset($_POST['links_css_delete'])) {
			foreach ($_POST['links_css_delete'] as $links_css_delete) {
				unlink($links_css_delete);
			}
		}
		
		// if a different css file has been chosen
		if (isset($_POST['links_css_set'])) {
			$links_css = $xml->links->settings->css;
			if ($_POST['links_css_set']!=$links_css) {
				unset($sxe->links->settings->css);
				// check if the css file the user has selected exists
				if (file_exists($_POST['links_css_set'])) {
					$sxe->links->settings->addChild('css', $_POST['links_css_set']);
				} else {
					$sxe->links->settings->addChild('css', 'default');
				}
				$sxe->asXML($xml_file);
			}
		}
		
		/*
		###################
		Background Updates
		###################
		*/
		
		// if a new background has been submitted
		if (isset($_POST['background_type'])) {
			switch ($_POST['background_type']) {
				// if the background is an uploaded image...
				case "upload";
					// only allows upload of the extension is .jpg or .png
					$name = $_FILES["background_upload"]["name"];
					$ext = end(explode(".", $name));
					if ($ext == 'jpg' || $ext == 'png') {
						// the background image will be saved
						$target_path = "graphics/backgrounds/";
						$target_path = $target_path . basename($_FILES['background_upload']['name']); 
						move_uploaded_file($_FILES['background_upload']['tmp_name'], $target_path);
					}
				break;
				// if the background is a url...
				case "url";
					// the url has to be checked if it is a url first
					$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
					$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
					$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
					$regex .= "(\:[0-9]{2,5})?"; // Port
					$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
					$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
					$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 
					$background_url = $_POST['background_url'];
					if(preg_match("/^$regex$/", $background_url)) {
						// the .txt file will be given a unique id
						$file_name = uniqid();
						$image_file = "graphics\backgrounds\\$file_name.txt";
						// a .txt file with be created with the url in it
						$fh = fopen($image_file, 'w');
						fwrite($fh, $background_url);
						fclose($fh);
					}
				break;
			}
		}
		
		// if a background has been deleted...
		$dir = 'graphics/backgrounds/';
		$file_count = count(glob($dir . "*.*"));
		$images = glob($dir . '*.*');
		for ($i=$file_count; $i>-1; $i--) {
			if (isset($_POST['background_delete_#'.$i])) {
				unlink($images[$i]);
			}
		}
		
		// if the fill type has been changed
		if (isset($_POST['background_fill_type'])) {
			$fill_setting = $sxe->backgrounds->settings;
			if ($_POST['background_fill_type']!=$fill_setting->fill_type) {
				unset($fill_setting->fill_type);
				$fill_setting->addChild('fill_type', $_POST['background_fill_type']);
				$sxe->asXML($xml_file);
			}
			
		}
		xml_reformat();
		$update='true';
	}
	
	/*
	###################
	Current HTML Setting up
	###################
	*/
	
	// $xml must be reloaded otherwise the pre-updated xml will continue to be used
	$xml = simpleXML_load_file($xml_file);
	
	// this sets the current background
	$dir = 'graphics/backgrounds/';
	$files = glob($dir . '*.*');
    $file_number = array_rand($files);
    $background = $files[$file_number];
	$ext = end(explode('.', $background));
	// if the selected file is a text file..,
	if ($ext == 'txt') {
		// the text file will be read, and the url inside will be copied
		$fh = fopen($background, 'r');
		$background = fread($fh, filesize($background));
		fclose($fh);
	}
	
	// set background fill settings
	$background_fill_type = $xml->backgrounds->settings->fill_type;
	switch ($background_fill_type) {
		case "fill";
			$background_fill = " no-repeat center center fixed;background-size:cover;";
		break;
		case "stretch";
			$background_fill = ";background-size:100% 100%;";
		break;
		case "tile";
			$background_fill = ";background-repeat:repeat;";
		break;
		case "center";
			$background_fill = "no-repeat center center fixed;";
		break;
		default:
			$background_fill = " no-repeat center center fixed;background-size:cover;";
		break;
	}
	
	// set the links css file
	$links_css = $xml->links->settings->css;
	if ($links_css=="default") {
		$links_css = "styles/links/default.css";
	}
	
	// if the links css file does not exist
	if (!file_exists($links_css)) {
		$links_css = "styles/links/default.css";
	}
?>
<!DOCTYPE html>
<html lang="en" style="background:black url('<?php echo "$background')$background_fill"; ?>')">
	<head>
		<title>My Homepage</title>
		<meta charset="utf-8"></meta>
		<meta name="robots" content="noindex"></meta>
		<link rel="stylesheet" type="text/css" href="styles/default.css"></link>
		<link rel="stylesheet" type="text/css" href="<?php echo $links_css ?>" ></link>
		<link rel="stylesheet" type="text/css" href="styles/spectrum.css"></link>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Codystar"></link>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/spectrum.js"></script>
		<script type="text/javascript" src="js/mootools-more-sortable.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		<script type="text/javascript" src="js/tools.js"></script>
		<script type="text/javascript" src="js/go.js"></script>
		<script type="text/javascript" src="engines/base.js"></script>
	</head>
	<body>
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<!-- Searchbar -->
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<div id="go">
			<!-- http://go.infinise.com -->
			<div id="engines"></div>
			<div id="container">
				<form id="form" onsubmit="return doSearch()">
					<div id="box">
						<div id="input"><input id="i" type="text" placeholder="…" autosave="com.infinise.go" results="5"  autofocus/></div>
						<!-- <a onclick="nextLanguage()" id="lang"></a> -->
						<ul id="sugs"></ul>
					</div>
				</form>
				<p id="method"></p>
			</div>
		</div>
		<div id="info">
			<div id="infoBox">
				<h2>Infinise<strong>Go!</strong> 2.5</h2>
				<p>Move your cursor over the logo or use the shortcut <strong>Ctrl + 1</strong> to switch between search engines.</p>
				<p>Use <strong>Ctrl + 2</strong> to change the search option.</p>
				<p><strong>Ctrl + 3</strong> cycles through languages, if available.</p>
				<hr/>
				<p class="source">Powered by <a href="http://go.infinise.com">Go!</a> from <a href="http://infinisedesign.net">Infinise Design.</a></p>
				<div id="corner"></div>
			</div>
			<a id="toggleInfo">i</a>
		</div>
		<script>
			$('#go').mouseout(function(event){
				$('#go').css('opacity', '0');
			});
			$('#go').mouseover(function(event){
				$('#go').css('opacity', '1');
			});
			$('#i').bind('focus',function(event){
				$('#go').css('opacity', '1');
			}).blur(function(event){
				$('#go').css('opacity', '0');
			});
		</script>
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<!-- Links -->
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<div id="links_wrapper">
			<div id="links_grab"></div>
			<div id="links">
				<ul>
				<?php
					// this generates a link for each of the links in the data.xml file
					$links = $xml->links;
					
					for ($i=0; $i<sizeof($xml->links->link); $i++) { 
						$link_name = $links->link[$i]->name;
						$link_icon = $links->link[$i]->icon;
						$link_colour = $links->link[$i]->colour;
						$link_url = $links->link[$i]->url;
						
						// stops there from being a missing image if there is pic
						if ($link_icon=='') {
							$link_icon = "blank.png";
						}
						
						echo "
							<li>
								<a style='background-color:$link_colour;' href='http://$link_url'>
									<div style='background-image:url(graphics/icons/$link_icon);' class='link_icon'></div>
									<span class='link_text'>$link_name</span>
								</a>
							</li>
						";
					}
				?>
				</ul>
			</div>
		</div>
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<!-- Settings -->
		<!-- %%%%%%%%%%%%%%%%%%% -->
		<div id="settings_show"></div>
		<script>
			$('#settings_show').click(function() {
				$('#settings_wrapper').toggle();
			});
		</script>
		<div id="settings_wrapper">
			<div id="settings_border">
				<h2>Settings</h2>
				<div id="settings_border_escape"></div>
				<script>
					$('#settings_border_escape').click(function() {
						$('#settings_wrapper').hide();
					});
				</script>
			</div>
			<div id="settings_content">
				<div id="settings_menu">
					<h3>Links</h3>
					<span class="show_link" show="#settings_links_new">Add a new link</span>
					<span class="show_link" show="#settings_links_change">Change the link order or delete a link</span>
					<h4>Links CSS</h4>
					<span class="show_link" show="#settings_links_css_new">Create a new links CSS file</span>
					<span class="show_link" show="#settings_links_css_edit">Edit a links CSS file</span>
					<span class="show_link" show="#settings_links_css_manage">Manage the links CSS files</span>
					<h3>Backgrounds</h3>
					<span class="show_link" show="#settings_backgrounds_new">Add a new background</span>
					<span class="show_link" show="#settings_backgrounds_manage">Manage your current backgrounds</span>
					<span class="show_link" show="#settings_backgrounds_settings">Change a background setting</span>
				</div>
				<!-- ################### -->
				<!-- Settings -> Links -> New -->
				<!-- ################### -->
				<div class="hidden" id="settings_links_new">
					<h3>New links</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
						<table class="table1">
							<tbody>
								<tr>
									<th>Name:</th>
									<td><input type="text" name="link_name" autocomplete="off"/></td>
								</tr>
								<tr>
									<th>URL:</th>
									<td><input type="text" name="link_url" autocomplete="off"/></td>
								</tr>
								<tr>
									<th>Colour:</th>
									<td>
										<input type="text" name="link_colour" id="colour_picker" value="#000000"/>
										<script>
											$("#colour_picker").spectrum({
												color: "#000000",
												showInput: true,
												className: "full-spectrum",
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
									</td>
								</tr>
							</tbody>
						</table>
						<div class="show-and-hide-content">
							<h4>Decide how you want to choose the link icon:</h4>
							<input type="radio" name="icon_type" value="upload" data-type="upload"/>Upload
							<input type="radio" name="icon_type" value="choose_archive" data-type="choose_archive"/>Archive
							<input type="radio" name="icon_type" value="choose_personal" data-type="choose_personal"/>Personal Uploads
							<input type="file" name="link_icon_upload" class="content content-upload" style="display:none;"/>
							<div id="icons" class="content content-choose_archive" style="display:none;">
								<ul>
								<?php
									$dir = 'graphics/icons/';
									$images = glob($dir . '*.*');
									foreach ($images as $image) {
										echo "<li></img><img class='icon' src='$image'></img><img class='tick' src='graphics/tick.png'><input type='radio' name='link_icon_choose' value='$image' style='display:none;'/></li>";
									}
									
								?>
								</ul>
							</div>
							<div id="icons" class="content content-choose_personal" style="display:none;">
								<ul>
								<?php
									$dir = 'graphics/icons/personal/';
									$images = glob($dir . '*.*');
									foreach ($images as $image) {
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
				<!-- ################### -->
				<!-- Settings -> Links -> Change -->
				<!-- ################### -->
				<div class="hidden" id="settings_links_change">
					<h3>Reorder the links</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
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
							<script>
								$('.link_delete').click(function() {
									$(this).parent().hide();
									$(this).next('input[type=checkbox]').attr('checked', true);
									$(this).next().next('input[type=checkbox]').attr('checked', false);
								});
							</script>
							<div class="clear"></div>
							<input class="update" type="submit" value="Update"></input>
						</div>
					</form>
					<button class="back">Cancel</button>
				</div>
				<!-- ################### -->
				<!-- Settings -> Links -> CSS -> New -->
				<!-- ################### -->
				<div class="hidden" id="settings_links_css_new">
					<h3>Create a new links CSS file</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
						<p>Name</p>
						<input type="text" name="links_css_new_name"/>
						<p>CSS Content</p>
						<textarea class="textarea_max" name="links_css_new_content"></textarea>
						<input class="update" type="submit" value="Update"></input>
						Use this file after update<input type="checkbox" name="links_css_new_use" checked="checked"/>
					</form>
				<button class="back">Cancel</button>
				</div>
				<!-- ################### -->
				<!-- Settings -> Links -> CSS -> Edit -->
				<!-- ################### -->
				<div class="hidden" id="settings_links_css_edit">
					<h3>Edit the links CSS</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
						<div class="show-and-hide-content">
							Select the file you wish to edit:
							<select name="links_css_edit_file">
								<?php
									$dir = 'styles/links/personal/';
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
									
									// this causes the first text area not to be hidden and not to be disabled
									if ($i==0) {
										$hidden = '';
										$disabled = '';
									} else {
										$hidden = 'hidden';
										$disabled = 'disabled="disabled"';
									}
									
									echo "<textarea class='$hidden content content-$i' name='css' $disabled>$css_text</textarea>";
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
				</div>
				<!-- ################### -->
				<!-- Settings -> Links -> CSS -> Manage -->
				<!-- ################### -->
				<div class="hidden" id="settings_links_css_manage">
					<h3>Manage the links CSS files</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
						<ul>
							<?php
								if ($links_css == 'styles/links/default.css') {
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
								$dir = 'styles/links/personal/';
								$css_files = glob($dir . '*.*');
								$i=0;
								
								foreach ($css_files as $css_file) {
								
								$css_file_basename = basename($css_file);
								
								if ($css_file == $links_css) {
									$selected = 'selected="selected"';
									$color = 'style="background-color:#d9d9d9;"';
								} else {
									$selected = '';
									$color = '';
								}
								
									echo "<li $color>
										$css_file_basename
										<input class='hidden' type='radio' name='links_css_set' value='$css_file' $selected/>
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
				</div>
				<!-- ################### -->
				<!-- Settings -> Backgrounds -> New -->
				<!-- ################### -->
				<div class="hidden" id="settings_backgrounds_new">
					<h3>New Background</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
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
				</div>
				<!-- ################### -->
				<!-- Settings -> Backgrounds -> Manage -->
				<!-- ################### -->
				<div class="hidden" id="settings_backgrounds_manage">
					<h3>Manage Backgrounds</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
						<ul>
							<?php
								$dir = "graphics/backgrounds/";
								$images = glob($dir . '*.*');
								$i = 0;
								
								foreach($images as $image) {
									
									$ext = end(explode('.', $image));
									// if the selected file is a text file..,
									if ($ext == 'txt') {
										// the text file will be read, and the url inside will be copied
										$fh = fopen($image, 'r');
										$image = fread($fh, filesize($image));
										fclose($fh);
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
				</div>
				<!-- ################### -->
				<!-- Settings -> Backgrounds -> Settings -->
				<!-- ################### -->
				<div class="hidden" id="settings_backgrounds_settings">
					<h3>Backgrounds</h3>
					<form enctype="multipart/form-data" action="index.php" method="POST">
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
				</div>
			</div>
		</div>
		<?php
			// show to the user that the page has been updated
			if (isset($update)) {
				echo "<div id='saved'>Update Succesful!</div>";
			}
		?>
	</body>
</html>
<script>
	// shows information based on settings menu clicks
	$(function () {
		var content = $('#settings_content');
		$('.show_link').click(function() {
			$(this).parent().hide();
			var show = $(this).attr('show');
			$(show).show();
		});
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
</script>