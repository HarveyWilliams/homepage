<?php
	session_start();
	
	$xml_file = 'data.xml';
	
	/*
	###################
	Current HTML Setting up
	###################
	*/
	
	// $xml must be reloaded otherwise the pre-updated xml will continue to be used
	$xml = simpleXML_load_file($xml_file);
	
	// set the clock
	$clock = $xml->clock;
	
	// set the current background
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
		<!-- Random -->
		<title>My Homepage</title>
		<link rel="icon" href="favicon.ico"/>
		<!-- Meta -->
		<meta charset="utf-8"/>
		<meta name="robots" content="noindex"/>
		<!-- Styles -->
		<link rel="stylesheet" href="styles/default.css"/>
		<link rel="stylesheet" href="styles/infinigo.css"/>
		<link rel="stylesheet" href="styles/spectrum.css"/>
		<link rel="stylesheet" href="styles/clock/<? echo $clock; ?>.css"/>
		<link rel="stylesheet" href="<?php echo $links_css ?>"/>
		<!-- Fonts -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Codystar"/>
		<!-- Javascript -->
		<script src="//ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
		<script src="js/mootools-more-1.4.0.1.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="js/spectrum.js"></script>
		<!-- I have no idea what tools.js is used for, but without it, menu clicks have no effect -->
		<script src="js/tools.js"></script>
		<script src="js/go.js"></script>
		<script src="engines/base.js"></script>
		<script src="js/clock/<? echo $clock; ?>.js"></script>
	</head>
	<body onload="startTime()">
		<!-- Search bar -->
		<div id="go_container">
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
			// hide the search if the user clicks off of it
			// show the search if the user hovers over it, and will hide it again if unhovered
			// will not hide if unhovered and the input is focused
			$("#go").hover(function() {
				$(this).css("opacity", 1);
			})
			.mouseleave(function() {
				if(!$("#i").focus()) {
					$(this).css("opacity", 0);
				}
			});
			$("#i").focus(function() {
				$("#go").css("opacity", 1);
			})
			.blur(function() {
				$("#go").css("opacity", 0);
			});
		</script>
		<!-- Links -->
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
						$background_colour = $links->link[$i]->background_colour;
						$icon_colour = $links->link[$i]->icon_colour;
						$link_url = $links->link[$i]->url;
						$link_font = $links->link[$i]->font;
						
						// stops there from being a missing image if there is pic
						if ($link_icon=='') {
							$link_icon = "blank.png";
						}
						
						echo "
							<li>
								<a style='background-color:$background_colour;' href='http://$link_url'>";
									$ext = end(explode(".", $link_icon));
									if ($ext=='svg') {
										$fh = fopen('graphics/icons/'.$link_icon, 'r');
										echo "<div style='fill:$icon_colour;' class='link_icon'>";
											echo fread($fh, filesize('graphics/icons/'.$link_icon));
										echo '</div>';
										fclose($fh);
									} else {
										echo "<div style='background-image:url(graphics/icons/$link_icon);' class='link_icon'></div>";
									}
									
									echo "<span style='font-family:$link_font;' class='link_text'>$link_name</span>
								</a>
							</li>
						";
					}
				?>
				</ul>
			</div>
		</div>
		<!-- Settings -->
		<div id="settings_show"></div>
		<script>
			$('#settings_show').click(function() {
				$('#settings_wrapper').toggle();
			});
		</script>
		<div id="settings_wrapper">
			<div id="settings_border">
				<h2 style="border-bottom:2px solid white;" class="show_link border" show="settings_menu">Settings</h2>
				<h2 class="show_link border" show="settings_help">Help</h2>
				<h2 class="show_link border" show="settings_updates">Updates</h2>
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
					<a class="show_link menu" show="settings_links_new">Add a new link</a>
					<a class="show_link menu" show="settings_links_edit">Edit a link</a>
					<a class="show_link menu" show="settings_links_change">Change the link order or delete a link</a>
					<h4>Links CSS</h4>
					<a class="show_link menu" show="settings_links_css_new">Create a new links CSS file</a>
					<a class="show_link menu" show="settings_links_css_edit">Edit a links CSS file</a>
					<a class="show_link menu" show="settings_links_css_manage">Manage the links CSS files</a>
					<h3>Backgrounds</h3>
					<a class="show_link menu" show="settings_backgrounds_new">Add a new background</a>
					<a class="show_link menu" show="settings_backgrounds_manage">Manage your current backgrounds</a>
					<a class="show_link menu" show="settings_backgrounds_settings">Change a background setting</a>
					<h3>Clocks</h3>
					<a class="show_link menu" show="settings_clocks_manage">Manage the clock JS and CSS files</a>
				</div>
				<div class="hidden" id="settings_links_new"></div>
				<div class="hidden" id="settings_links_edit"></div>
				<div class="hidden" id="settings_links_change"></div>
				<div class="hidden" id="settings_links_css_new"></div>
				<div class="hidden" id="settings_links_css_edit"></div>
				<div class="hidden" id="settings_links_css_manage"></div>
				<div class="hidden" id="settings_backgrounds_new"></div>
				<div class="hidden" id="settings_backgrounds_manage"></div>
				<div class="hidden" id="settings_backgrounds_settings"></div>
				<div class="hidden" id="settings_clocks_manage"></div>
				<div class="hidden" id="settings_help">
					<h3>Firefox</h3>
					<a class="show_link menu" show="help_firefox_newtab">How do I make the new tab direct to this page?</a>
					<h3>Chrome</h3>
					<a class="show_link menu" show="help_chrome_newtab">How do I make the new tab direct to this page?</a>
				</div>
				<div class="hidden" id="help_firefox_newtab">
					<h3>Firefox -> How do I make the new tab direct to this page?</h3>
					<ol>
						<li>Open a new tab</li>
						<li>In the url, type <a href="about:config" target="_blank">about:config</a></li>
						<li>If a warning pops up, click <b>I'll be careful, I promise</b></li>
						<li>In the search box, type <b>newtab</b></li>
						<li>Find the Preference Name <b>browser.newtab.url</b> and change the value to this websites url</li>
						<li>Test by opening a new tab</li>
					</ol>
					<button class="go_back">Go back</button>
				</div>
				<div class="hidden" id="help_chrome_newtab">
					<h3>Chrome -> How do I make the new tab direct to this page?</h3>
					<ol>
						<li>Go to your settings (url: <a href="chrome://settings" target="_blank">chrome://settings)</a></li>
						<li>Click on <b>Settings</b> in the left navigation pane</li>
						<li>Find the <b>On startup</b> section and choose the option <b>Open a specific page or a set of pages</b></li>
						<li>Click <b>Set pages</b></li>
						<li>Add this websites url to the page and click okay</li>
						<li>Download <a href="https://chrome.google.com/webstore/detail/new-tab-redirect/icpgjfneehieebagbmdbhnlpiopdcmna?utm_source=chrome-ntp-icon" target="_blank">New Tab Redirect</a> from the Chrome Web Store</li>
					</ol>
					<button class="go_back">Go back</button>
				</div>
				<div class="hidden" id="settings_updates">
					<span id="version">Version 0.1 alpha</span>
					<h3>Updates</h3>
					<p>2014: First started work on the home page project</p>
				</div>
			</div>
		</div>
		<div id="clock"></div>
		<?php
			// show to the user that the page has been updated
			if (isset($_SESSION['update'])) {
				echo "<div id='saved'>Update Successful!</div>";
				session_destroy();
			}
		?>
		<script>
			// clock
			//var clock = $('#clock').FlipClock({
			//	clockFace: 'TwentyFourHourClock'
			//});
			
			// lets the back button in the settings go back
			$('.go_back').click(function() {
				$(this).parent().hide();
				$('#settings_help').show();
			});
			
			// shows information based on settings menu clicks
			$(function () {
				$('.show_link'+'.menu').click(function() {
					$(this).parent().hide();
					var show = $(this).attr('show');
					$('#'+show).load('settings/'+show+'.php');
					$('#'+show).show();
				});
			});
			
			// shows different tabs depending on which tab is clicked on
			$(function () {
				$('.show_link'+'.border').click(function() {
					$(this).parent().children('h2').css('border-bottom', '1px solid black;');
					$(this).css('border-bottom', '2px solid white');
					var show = $(this).attr('show');
					$('#settings_content').children().hide();
					$('#'+show).show();
				});
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
	</body>
</html>