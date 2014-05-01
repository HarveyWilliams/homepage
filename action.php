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
			$link_background_colour = clean($_POST['link_background_colour']);
			$link_icon_colour = clean($_POST['link_icon_colour']);
			$link_url = clean($_POST['link_url']);
			$link_font = clean($_POST['link_font']);
			//! icon name still needs sanitizing
		
			$link = $sxe->links->addChild('link');
			
			// depending on how the user chose the file
			switch ($_POST['link_icon_type']) {
				case "upload":
					$name = $_FILES["link_icon_upload"]["name"];
					$ext = end(explode(".", $name));
					if ($ext == 'jpg' || $ext == 'png' || $ext == 'JPG' || $ext == 'PNG') {
						// the background image will be saved
						$target_path = "graphics/icons/personal/";
						$target_path = $target_path . basename($_FILES['link_icon_upload']['name']); 
						move_uploaded_file($_FILES['link_icon_upload']['tmp_name'], $target_path);
					}
					$link->addChild('icon', 'personal/'.$name);
				break;
				case "choose_archive":
					$link->addChild('icon', basename($_POST['link_icon']));
				break;
				case "choose_personal":
					$link->addChild('icon', 'personal/'.basename($_POST['link_icon']));
				break;
			}
			
			$link->addChild('name', $link_name);
			$link->addChild('background_colour', $link_background_colour);
			$link->addChild('icon_colour', $link_icon_colour);
			$link->addChild('url', $link_url);
			$link->addChild('font', $link_font);
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
					$name[$i] = (string) $links->link[0]->name;
					$icon[$i] = (string) $links->link[0]->icon;
					$background_colour[$i] = (string) $links->link[0]->background_colour;
					$icon_colour[$i] = (string) $links->link[0]->icon_colour;
					$url[$i] = (string) $links->link[0]->url;
					$font[$i] = (string) $links->link[0]->font;
					
					// then deleting it
					unset($links->link[0]);
					$sxe->asXML($xml_file);
				}
				
				for ($i=0; $i<$links_xml_size; $i++) {
					$x = $link_sort[$i];
					
					$link = $links->addChild('link');
					$link->addChild('name', $name[$x]);
					$link->addChild('icon', $icon[$x]);
					$link->addChild('background_colour', $background_colour[$x]);
					$link->addChild('icon_colour', $icon_colour[$x]);
					$link->addChild('url', $url[$x]);
					$link->addChild('font', $font[$x]);
					$sxe->asXML($xml_file);
				}
			}
		}
		
		// if a change has been made to the current links...
		// the for statement has to be done by going down the list of links rather than up
		// this is because if it deletes, for example, 2 and 3, and it is going in ascending order, it will delete 2, 3 will take 2s position, and when the program deletes 3 it will be actually deleting 4
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
			$fh = fopen('styles/links/personal/'.basename($_POST['links_css_edit_file']), 'w');
			fwrite($fh, $_POST['css']);
			fclose($fh);
			if (isset($_POST['links_css_edit_use'])) {
				$links = $sxe->links;
				unset($links->settings->css);
				$links->settings->addChild('css', 'styles/links/personal/'.basename($_POST['links_css_edit_file']));
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
		
		/*
		###################
		Background Updates
		###################
		*/
		
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
		
		/*
		###################
		Clock Updates
		###################
		*/
		
		// if a js file has been selected to be deleted
		if (isset($_POST['clocks_js_delete'])) {
			foreach ($_POST['clocks_delete'] as $clock_js_delete) {
				$js_dir = 'js/clock/';
				$css_dir = 'styles/clock/';
				unlink($js_dir.$clock_js_delete.'.js');
				unlink($css_dir.$clock_js_delete.'.css');
			}
		}
		
		// if a different css file has been chosen
		if (isset($_POST['clocks_set'])) {
			$clock = $xml->clock;
			if ($_POST['clocks_set']!=$clock) {
				$js_dir = 'js/clock/';
				unset($sxe->clock);
				// check if the css file the user has selected exists
				if (file_exists($js_dir.$_POST['clocks_set'].'.js')) {
					$sxe->addChild('clock', $_POST['clocks_set']);
				} else {
					$sxe->addChild('clock', 'None');
				}
				$sxe->asXML($xml_file);
			}
		}
	}
	
	xml_reformat();
	
	session_start();
	$_SESSION['update'] = 1;
	
	header('Location:index.php');
?>