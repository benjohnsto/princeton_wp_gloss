<?php
/*
	Plugin Name: Princeton Glossary
	Plugin URI:
	Description: 
	Version: 1.0
	Author: Ben Johnston
*/

function princeton_glossary_adding_scripts() {
  wp_register_script('princeton_glossary_js', plugins_url('js/princeton-glossary.js', __FILE__), array('jquery'),'1.1', true);
  wp_enqueue_script('princeton_glossary_js');
  wp_register_style('princeton_glossary_css', plugins_url('css/princeton-glossary.css',__FILE__ ));
  wp_enqueue_style('princeton_glossary_css');
}

add_action( 'wp_enqueue_scripts', 'princeton_glossary_adding_scripts' );  








function princeton_glossary_filter_content($content) {

  if($settings = get_option('princeton_gallery_settings')) {
     $spreadsheet_url = $settings['princeton_gallery_text_field_0'];
     $cnt = 0;
     if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

     if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
       while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
         if($cnt > 0) { 
   		$data[0] = trim($data[0]);
		//$content = preg_replace( "/\b{$data[0]}\b/i" , "<a href='#' class='glossary'>{$data[0]}<div class='popup' >{$data[1]}</div></a>", $content);
		$content = preg_replace( "/\b{$data[0]}\b/i" , "<a href='#' class='glossary'>{$data[0]}<span class='popup' >{$data[1]}</span></a>", $content);
		}
		else { $cnt++; }
	    }
	    fclose($handle);
	}
     else {
	    die("Problem reading csv");
     }


  }




  return $content;
}

add_filter( 'the_content', 'princeton_glossary_filter_content' );


/***********************************************************************/


add_action( 'admin_menu', 'princeton_gallery__add_admin_menu' );
add_action( 'admin_init', 'princeton_gallery_settings_init' );


function princeton_gallery__add_admin_menu(  ) { 

	add_options_page( 'Gallery', 'Gallery', 'manage_options', 'gallery', 'princeton_gallery_options_page' );

}


function princeton_gallery_settings_init(  ) { 

	register_setting( 'pluginPage', 'princeton_gallery_settings' );

	add_settings_section(
		'princeton_gallery__pluginPage_section', 
		__( 'Enter the url to a google spreadsheet below', 'princeton_g' ), 
		'princeton_gallery_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'princeton_gallery_text_field_0', 
		__( 'Google Spreadsheet URL', 'princeton_g' ), 
		'princeton_gallery_text_field_0_render', 
		'pluginPage', 
		'princeton_gallery__pluginPage_section' 
	);


}


function princeton_gallery_text_field_0_render(  ) { 

	$options = get_option( 'princeton_gallery_settings' );
	?>
	<input type='text' name='princeton_gallery_settings[princeton_gallery_text_field_0]' style='width:100%' value='<?php echo $options['princeton_gallery_text_field_0']; ?>'>
	<?php

}


function princeton_gallery_settings_section_callback(  ) { 

	echo __( 'The Google Spreadsheet needs to be made public (published) and in CSV format. The spreadsheet should consist of twop columns, the first being the words to gloss and the second being the definition of those words.', 'princeton_g' );

}


function princeton_gallery_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Gallery</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
