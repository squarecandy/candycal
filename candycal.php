<?php
/*
Plugin Name: SquareCandy Google Calendar
Plugin URI: http://squarecandydesign.com/plugins/candycal
Description: A custom display for your google calendar feed. 
Version: 1.0.2
Author: Peter Wise (squarecandy)
Author URI: http://squarecandydesign.com
License:  GPL2
*/

add_action('wp_print_styles', 'add_candycal_stylesheet');

// look for candycal.css in the current theme directory first, then in the plugin directory
function add_candycal_stylesheet() {

  $myThemeUrl = get_bloginfo('stylesheet_directory') . '/candycal.css';
  $myThemeFile = get_stylesheet_directory() . '/candycal.css';
  $myStyleUrl = WP_PLUGIN_URL . '/candycal_v3/candycal.css';
  $myStyleFile = WP_PLUGIN_DIR . '/candycal_v3/candycal.css';
  if ( file_exists($myThemeFile) ) { 
    wp_register_style('candycalstyle', $myThemeUrl);
    wp_enqueue_style( 'candycalstyle');  
  } 
  elseif ( file_exists($myStyleFile) ) {
    wp_register_style('candycalstyle', $myStyleUrl);
    wp_enqueue_style( 'candycalstyle');
  }
}

/**********************
 * ADMIN OPTIONS SETTINGS
 */

// Add the admin options page

function candycal_menu() {

  add_options_page('Candy-Cal Options', 'Candy-Cal', 'manage_options', 'candycal', 'candycal_options');
  add_action( 'admin_init', 'register_candycalsettings' );
  
}
add_action('admin_menu', 'candycal_menu');



function register_candycalsettings() {
  
  register_setting( 'candycal_group', 'candycal_options', 'candycal_options_validate' );
  
  // google section
  add_settings_section('candycal_google', 'Google Setup Options', 'candycal_google_header', 'candycal');
  add_settings_field('candycal_gcal_id', 'Google Calendar ID', 'candycal_gcal_id_string', 'candycal', 'candycal_google');
  add_settings_field('candycal_gcal_key', 'Google Calendar Developer API Key', 'candycal_gcal_key_string', 'candycal', 'candycal_google');
  
  // main section
  add_settings_section('candycal_main', 'Main Display Options', 'candycal_main_header', 'candycal');
  // main fields
  add_settings_field('candycal_date_format', 'Date Format', 'candycal_date_format_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_multidate_format1', 'Multi-Day Date Format Start Date', 'candycal_multidate_format1_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_multidate_format2', 'Multi-Day Date Format End Date', 'candycal_multidate_format2_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_time_format', 'Time Format', 'candycal_time_format_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_date_time_sep', 'Date/Time Separator Text', 'candycal_date_time_sep_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_date_or_title', 'List Date or Title First?', 'candycal_date_or_title_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_display_address', 'Display Location/Address?', 'candycal_display_address_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_map_link', 'Display Map Link?', 'candycal_map_link_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_gcal_link', 'Display Add to gCal Link?', 'candycal_gcal_link_string', 'candycal', 'candycal_main');
  add_settings_field('candycal_past_by_year', 'Show Past Events Headers by Year?', 'candycal_past_by_year_string', 'candycal', 'candycal_main');
  
  
  //mini section
  add_settings_section('candycal_mini', 'Mini Display Options', 'candycal_mini_header', 'candycal');
  //mini fields
  add_settings_field('candycal_mini_date_format', 'Mini: Date Format', 'candycal_mini_date_format_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_multidate_format1', 'Mini: Multi-Day Date Format Start Date', 'candycal_mini_multidate_format1_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_multidate_format2', 'Mini: Multi-Day Date Format End Date', 'candycal_mini_multidate_format2_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_mini_num_events', 'Mini: How Many Upcoming Events to Show', 'candycal_mini_num_events_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_mini_more', 'Mini: Display More Link?', 'candycal_mini_more_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_mini_more_url', 'Mini: More Link URL', 'candycal_mini_more_url_string', 'candycal', 'candycal_mini');
  add_settings_field('candycal_mini_description', 'Mini: Display Description?', 'candycal_mini_description_string', 'candycal', 'candycal_mini');

}


// Set defaults when the plugin is activated for the first time
register_activation_hook(__FILE__,'candycal_set_default_settings');

function candycal_set_default_settings() {
  
  $options = get_option('candycal_options');
  $defaultoptions = array (
  		'date_format' => 'l, F j, Y',
  		'multidate_format1' => 'F j',
  		'multidate_format2' => 'F j, Y',
      'time_format' => 'g:ia',
      'date_time_sep' => ' at ',
      'date_or_title' => 'date',
      'display_address' => 'yes',
      'map_link' => 'yes',
      'gcal_link' => 'yes',
      'past_by_year' => '',
      'mini_date_format' => 'D, M j',
      'mini_multidate_format1' => 'M j',
  		'mini_multidate_format2' => 'M j',
      'mini_num_events' => 3,
      'mini_more' => '',
      'mini_more_url' => '',
      'mini_description' => 'yes'
    );
    $insertoptions = array();
    foreach ($defaultoptions as $key => $value) {
      if (!isset($options[$key])) $insertoptions[$key] = $value;
      else $insertoptions[$key] = $options[$key];
    }
    add_option( 'candycal_options', $insertoptions );
  } 
	
}

// Render the fields
function candycal_gcal_id_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_gcal_id" name="candycal_options[gcal_id]" size="50" value="'.$options['gcal_id'].'" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxx@group.calendar.google.com" /><br />
    <small>In Google calendar, under <strong>My Calendars</strong> on the left, click the down arrow next to your public calendar and select <strong>Calendar settings</strong>.  The ID is listed under <strong>Calendar Address</strong> near the bottom and will usually end with @group.calendar.google.com.</small>';
}

function candycal_gcal_key_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_gcal_key" name="candycal_options[gcal_key]" size="50" value="'.$options['gcal_key'].'" placeholder="AbC123dEf456Ghi789JkL012mNo345PqR678sTu" /><br />
    <small>Go to <a href="https://console.developers.google.com/project">https://console.developers.google.com/project</a>. Click <strong>Create Project</strong>.  Name your project "My Calendar" or whatever you want and agree to the terms.  Click <strong>Enable an API</strong>.  Scroll down and turn on <strong>Calendar API</strong>.  Now in the left menu, click <strong>APIs &amp; Auth > Credentials</strong>. Click <strong>Create New Key</strong> and pick <strong>Browser Key</strong>.  Enter your website\'s domain name here in the format <strong>*.example.com/*</strong>.  If you prefer you may setup a server wide key using your server\'s IP address instead.  Enter your new API key in the field above.</small>';
}

function candycal_date_format_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_date_format" name="candycal_options[date_format]" size="20" value="'.$options['date_format'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_multidate_format1_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_multidate_format1" name="candycal_options[multidate_format1]" size="20" value="'.$options['multidate_format1'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_multidate_format2_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_multidate_format2" name="candycal_options[multidate_format2]" size="20" value="'.$options['multidate_format2'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_time_format_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_time_format" name="candycal_options[time_format]" size="20" value="'.$options['time_format'].'" /><br />
  		<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_date_time_sep_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_date_time_sep" name="candycal_options[date_time_sep]" size="6" value="'.$options['date_time_sep'].'" />';
}

function candycal_date_or_title_string(){
  $options = get_option('candycal_options');
  echo '<input type="radio" id="candycal_date_or_title_date" name="candycal_options[date_or_title]" value="date"';
  if ($options['date_or_title']=='date') echo ' checked="checked"';
  echo ' /> Date<br />';
  echo '<input type="radio" id="candycal_date_or_title_title"  name="candycal_options[date_or_title]" value="title"';
  if ($options['date_or_title']=='title') echo ' checked="checked"';  
  echo ' /> Title';
}

function candycal_display_address_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_display_address" name="candycal_options[display_address]" value="yes"';
  if ($options['display_address']=='yes') echo ' checked="checked"';
  echo ' />';
}

function candycal_map_link_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_map_link" name="candycal_options[map_link]" value="yes"';
  if ($options['map_link']=='yes') echo ' checked="checked"';
  echo ' />';
}

function candycal_gcal_link_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_gcal_link" name="candycal_options[gcal_link]" value="yes"';
  if ($options['gcal_link']=='yes') echo ' checked="checked"';
  echo ' />';
}

function candycal_past_by_year_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_past_by_year" name="candycal_options[past_by_year]" value="yes"';
  if ($options['past_by_year']=='yes') echo ' checked="checked"';
  echo ' />';
}

function candycal_mini_date_format_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_mini_date_format" name="candycal_options[mini_date_format]" size="20" value="'.$options['mini_date_format'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_mini_multidate_format1_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_mini_multidate_format1" name="candycal_options[mini_multidate_format1]" size="20" value="'.$options['mini_multidate_format1'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}

function candycal_mini_multidate_format2_string(){
  $options = get_option('candycal_options');
  echo '<input type="text" id="candycal_mini_multidate_format2" name="candycal_options[mini_multidate_format2]" size="20" value="'.$options['mini_multidate_format2'].'" /><br />
  	<small>see PHP\'s <a href="http://www.php.net/date">date() manual</a> for formatting options.</small>';
}


function candycal_mini_num_events_string(){
  $options = get_option('candycal_options');
  echo '<input type="number" id="candycal_mini_num_events" name="candycal_options[mini_num_events]" size="6" value="'.$options['mini_num_events'].'" />';
}

function candycal_mini_more_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_mini_more" name="candycal_options[mini_more]" value="yes"';
  if ($options['mini_more']=='yes') echo ' checked="checked"';
  echo ' />';
}

function candycal_mini_more_url_string(){
  $options = get_option('candycal_options');
  echo '<input type="url" id="candycal_mini_more_url" name="candycal_options[mini_more_url]" size="50" value="'.$options['mini_more_url'].'" />';
}

function candycal_mini_description_string(){
  $options = get_option('candycal_options');
  echo '<input type="checkbox" id="candycal_mini_description" name="candycal_options[mini_description]" value="yes"';
  if ($options['mini_description']=='yes') echo ' checked="checked"';
  echo ' />';
}


// Admin section descriptions
function candycal_google_header() {
  echo '<p>These settings will connect your public Google calendar with your WordPress site.</p>';
}

function candycal_main_header() {
  echo '<p>Options to control the display of the standard upcoming and past events modes.</p>';
}

function candycal_mini_header() {
  echo '<p>Options to control the display of the "Mini" widget, usually used on the homepage or in a sidebar.</p>';
}

// Validation and correction of options
// #TODO: should this be more restrictive?  Does wordpress have actual validation where it marks bad fields for the user?
function candycal_options_validate($input) {
  $newinput['gcal_id'] = trim($input['gcal_id']);
  $newinput['gcal_key'] = trim($input['gcal_key']);
  $newinput['date_format'] = trim($input['date_format']);
  $newinput['multidate_format1'] = trim($input['multidate_format1']);
  $newinput['multidate_format2'] = trim($input['multidate_format2']);
  $newinput['time_format'] = trim($input['time_format']);
  $newinput['date_time_sep'] = $input['date_time_sep'];
  $newinput['date_or_title'] = $input['date_or_title']=='title' ? 'title' : 'date';
  $newinput['display_address'] = $input['display_address']=='yes' ? 'yes' : '';
  $newinput['map_link'] = $input['map_link']=='yes' ? 'yes' : '';
  $newinput['gcal_link'] = $input['gcal_link']=='yes' ? 'yes' : '';
  $newinput['past_by_year'] = $input['past_by_year']=='yes' ? 'yes' : '';
  
  $newinput['mini_date_format'] = trim($input['mini_date_format']);
  $newinput['mini_multidate_format1'] = trim($input['mini_multidate_format1']);
  $newinput['mini_multidate_format2'] = trim($input['mini_multidate_format2']);
  $newinput['mini_num_events'] = preg_replace("/[^0-9]/","",$input['mini_num_events']);
  $newinput['mini_more'] = $input['mini_more']=='yes' ? 'yes' : '';
  $newinput['mini_more_url'] = trim($input['mini_more_url']);
  $newinput['mini_description'] = $input['mini_description']=='yes' ? 'yes' : '';
    
  foreach ($newinput as $k => $v) $newinput[$k] = str_replace('"', '', $v);
    
  return $newinput;
}

// Render the options page.
function candycal_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
  /*
  if ( isset($_POST["candycal_submit"]) && ($_POST["candycal_submit"] == 'TRUE')) {
    update_option('candycal_options', $_POST["candycal_options"]);
  }
  */
  
  ?>
  <div class="wrap">
    <h2>Candy-Cal options</h2>
    <form method="post" action="options.php">
    <!--<form method="post" action="options.php">-->
      <?php settings_fields( 'candycal_group' ); ?> 
      <?php do_settings_sections('candycal'); ?>
  	  <!--<input type="hidden" name="candycal_submit" value="TRUE" />-->
      <input name="Submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
  </div>

<?php
}



/****************************
 * PUBLIC CALENDAR DISPLAY
 */

function candycal_display($type) {

  // make sure timezone is set based on Wordpress settings
  date_default_timezone_set(get_option('timezone_string'));

  // get plugin settings
  $options = get_option('candycal_options');
  
  // get the google calendar feed
  if ($type == 'mini'):
    // get just the next X upcoming
    $gcal_url = 'https://www.googleapis.com/calendar/v3/calendars/'. urlencode($options['gcal_id']).'/events?key='.$options['gcal_key'].'&maxResults='.$options['mini_num_events'].'&singleEvents=true&orderBy=startTime&timeMin='.date('Y-m-d\TH:i:s-00:00');
    $gcal_raw = file_get_contents($gcal_url);
    $gcal_raw = json_decode($gcal_raw);
    $date_format = $options['mini_date_format'];
    
    $miniplusone = $options['mini_num_events']+1;
    $minimore = 'https://www.googleapis.com/calendar/v3/calendars/'. urlencode($options['gcal_id']).'/events?key='.$options['gcal_key'].'&maxResults='.$miniplusone.'&singleEvents=true&orderBy=startTime&timeMin='.date('Y-m-d\TH:i:s-00:00');
    $minimore = file_get_contents($minimore);
    $minimore = json_decode($minimore);
    $minimore = count($minimore->items);
    $multidateformat1 = $options['mini_multidate_format1'];
    $multidateformat2 = $options['mini_multidate_format1'];
  
  elseif ($type == 'past'): 
    // Get past events only
    $gcal_url = 'https://www.googleapis.com/calendar/v3/calendars/'. urlencode($options['gcal_id']).'/events?key='.$options['gcal_key'].'&maxResults=2500&singleEvents=true&orderBy=startTime&timeMax='.date('Y-m-d\TH:i:s-00:00');
    $gcal_raw = file_get_contents($gcal_url);
    $gcal_raw = json_decode($gcal_raw);
    $gcal_raw->items = array_reverse($gcal_raw->items, true);
    $date_format = $options['date_format'];
    $multidateformat1 = $options['multidate_format1'];
    $multidateformat2 = $options['multidate_format1'];
    
  
  else:
    // do the default: all upcoming
    $gcal_url = 'https://www.googleapis.com/calendar/v3/calendars/'. urlencode($options['gcal_id']).'/events?key='.$options['gcal_key'].'&maxResults=1000&singleEvents=true&orderBy=startTime&timeMin='.date('Y-m-d\TH:i:s-00:00');
    $gcal_raw = file_get_contents($gcal_url);
    $gcal_raw = json_decode($gcal_raw);
    $date_format = $options['date_format'];
    $multidateformat1 = $options['multidate_format1'];
    $multidateformat2 = $options['multidate_format1'];
        
  endif;
  
  if (!empty($gcal_raw)) :
    
    // Do the display rendering
        
    $output = '<section class="candycal candycal_'.$type.' candycal_'.$options['date_or_title'].'_first">';
    
    $count = count($gcal_raw->items);
    $i = 1;
    
    foreach ($gcal_raw->items as $key => $item) {
      
      $date = '<div class="candycal_date">';
      if (isset($year)) $prevyear = $year;
      
      // All day event - one day
      if (isset($item->start->date) && isset($item->end->date) && (date('Ymd', strtotime($item->end->date)) - date('Ymd', strtotime($item->start->date)) == 1)) {
    	  $date .= '<time itemprop="startDate" datetime="'.date('Y-m-d',strtotime($item->start->date)).'">'.date($date_format, strtotime($item->start->date)).'</time>';
    	  $year = date('Y',strtotime($item->start->date));
    	}
    	
    	// All day event - multi day
    	elseif (isset($item->start->date) && isset($item->end->date) && (date('Ymd', strtotime($item->end->date)) - date('Ymd', strtotime($item->start->date)) > 1)) {
      	$date .= '<time itemprop="startDate" datetime="'.date('Y-m-d',strtotime($item->start->date)).'">'.date($multidateformat1, strtotime($item->start->date)).'</time> &ndash; <time itemprop="endDate" datetime="'.date('Y-m-d',strtotime($item->end->date)).'">'.date($multidateformat2, strtotime($item->end->date)).'</time>';
      	$year = date('Y',strtotime($item->start->date));
    	}
  
    	// Single event w/ start time
    	elseif (isset($item->start->dateTime)) {
        $date .= '<time itemprop="startDate" datetime="'.$item->start->dateTime.'">'.date($date_format, strtotime($item->start->dateTime)) . '<span class="candycal_time">'.$options['date_time_sep'] . date($options['time_format'], strtotime($item->start->dateTime)) . '</span></time>';
        $year = date('Y',strtotime($item->start->dateTime));
    	}

    	//  error... one of the 3 conditions above should be met.
    	else {
    	  $date .= '<!-- date error - what\'s going on here? -->';
    	  $year = date('Y');
    	}
    	
    	if ($options['gcal_link']=='yes' && isset($item->htmlLink)) $date .= ' <a class="candycal_gcal_link" href="'.$item->htmlLink.'">gCal+</a>';
    	
    	$date .= '</div>';
      
      $title = '<h1 itemprop="summary" class="candycal_title">'.$item->summary.'</h1>';
      
      if (isset($item->location) && $options['display_address']=='yes') {
        $location = '<p itemprop="location" itemscope itemtype="http://data-vocabulary.org/Organization">'.$item->location;
        if ($options['map_link']=='yes') $location .= ' <a class="candycal_map_link" href="http://maps.google.com/?q='.urlencode($item->location).'">MAP</a>';
        $location .='</p>';
      }
      else {
        $location = '';
      }
      
      // title or date first?
      if ($options['date_or_title']=='title') $titledate = $title.$date;
      else $titledate = $date.$title;
      
      // description
      if ( ($type!='mini' || $options['mini_description']=='yes') && isset($item->description) ) $description = '<p class="candycal_description">'.make_clickable($item->description).'</p>';
      else $description = '';     
      
      // year headings for past events
      $year_first = FALSE;
      if ($type=='past' && $options['past_by_year']=='yes') {
    	  if ( !isset($prevyear) || (isset($prevyear) && $prevyear!=$year) ) {	
      	  if ($i!=1) $output .= '</div>';  
    	    $output .= '<h2 class="candycal_year';
    	    if ($i==1) $output .= ' candycal_year_first';
    	    $output .= '">'.$year.'</h2><div class="year_archive">';
    	    $year_first = TRUE;
    	  }
    	}
      
      $output .= '<article itemscope itemtype="http://data-vocabulary.org/Event" class="candycal_item';
      if ($i == 1) $output .= ' candycal_item_first';
      if ($i == $count) $output .= ' candycal_item_last';
      if ($year_first) $output .= ' candycal_item_year_first';
      $i++;
      $output .= '">'.$titledate.$location.$description.'</article>';
      
    }
    
    if ($options['mini_more']=='yes' && !empty($options['mini_more_url']) && $type=='mini' && $minimore > $options['mini_num_events']) {
      $output .= '<a class="candycal_mini_more" href="'.$options['mini_more_url'].'">more events &raquo;</a>';  
    }
    
    if (count($gcal_raw->items) < 1 && $type != 'past') $output .= '<p>There are currently no upcoming events.</p>';
    
    if ($type=='past' && $options['past_by_year']=='yes') $output .= '</div>'; 
    $output .= '</section>';
    
    return $output;
    
  
  else :
    return 'calendar error.';
    // #TODO: more detailed error messages
    
  endif; 
    
}  // end candycal_display function




// Add the Shortcode
function candycal_shortcode($atts) {
	extract(shortcode_atts(array(
		'type' => 'upcoming',
	), $atts));
	
	if (empty($atts)) return candycal_display('upcoming');
	else return candycal_display($atts['type']);
}
add_shortcode('candycal', 'candycal_shortcode');

?>
