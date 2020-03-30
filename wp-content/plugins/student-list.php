<?php
/**
 * Plugin Name:       Student List
 * Plugin URI:        http://eram.io/plugin
 * Description:       All Student Details List 
 * Version:           1.0
 * Author:            SahilQaiser
 * Author URI:        http://eram.io
 */
 
function custom_view() {
	global $wpdb;
	$results = $wpdb->get_results("select * from student");
	echo '<table>
  <tr>
    <th>Name</th>
    <th>City</th>
    <th>State</th>
	<th>Age</th>
  </tr>';
	foreach( $results as $user_data) {
		echo "<tr>
    <td>$user_data->name</td>
    <td>$user_data->city</td>
    <td>$user_data->state</td>
	<td>$user_data->age</td>
  </tr>";
	}
	echo '</table>';
	
}
add_shortcode('views', 'custom_view');
?>