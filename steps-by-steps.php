<?php
   /*
   Plugin Name: Stepy
   Description: Stepy, dont allow user if a previous step is not completed
   Author: Johnabelardom
   Version: 1.0.0
   License: GPL2
   */



/**
 * The main class that handles the entire output, content filters, etc., for this plugin.
 *
 * @package Stepy
 * @since 1.0
 */
class Stepy {

	var $user_meta_key = "steps_by_steps_progress";

	/** Constructor */
	function __construct() {
		// $this->user_meta_key = "steps_by_steps_progress";
		// register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );
		add_shortcode('sbs', array($this, 'steps_by_steps'));
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_links_scripts' ) );

		
		// add_action('frm_after_create_entry', 'step_2_completion', 30, 2);
		// add_action('frm_after_create_entry', 'step_1_completion', 30, 2);
	}

	function wp_enqueue_links_scripts() {
		wp_enqueue_style( 'steps-by-steps-styling', plugins_url( '/assets/style.css', __FILE__  ) );
		wp_enqueue_style( 'font-awesome', plugins_url( '/assets/font-awesome/css/font-awesome.min.css', __FILE__  ) );
		// wp_enqueue_script( 'steps-by-steps-script', plugins_url('/assets/steps-by-steps-script.js', __FILE__ ), array(), '1.0.0', true );
	}


	// Function to add subscribe text to posts and pages
	function steps_by_steps( $atts, $content = null ) {
		extract( shortcode_atts( array(
	        'confetti' => false,
	        'step_names' => '',
	        'step_contents' => ''
	    ), $atts, 'sbs' ) );

		$new_content = $content;
		$current_user = get_current_user_id();



	    // begin output buffering
	    ob_start();

	    if ( strpos($step_names, ',') ) {
			$steps = explode(',', $step_names);
			$contents = explode(',', $step_contents);

			$ave_len = (sizeof($steps) + sizeof($contents)) / 2;

			// var_dump($steps);
			// exit();	

			if ( ! $this->is_step_complete( $current_user, $steps[0] ) ) {
			    if ($confetti) {
			    	echo "<script>";
			    	include( plugin_dir_path( __FILE__ ) . 'assets/steps-by-steps-script.js' );
			    	echo "</script>";
			    }
			    $new_content = "[formidable id=" . $step_contents[0] . "]";
			    ?>
					<canvas id="confetti"></canvas>
					<div id="party-info">
					 	<?= do_shortcode($new_content) ?>
					</div>
			    <?php
				return;
			}

			// for ( $i = 0; $i <= $ave_len; $i++ ) {
			// 	if ( ! $this->is_step_complete( $current_user, $steps[$i] ) ) {
			// 	    if ($confetti) {
			// 	    	echo "<script>";
			// 	    	include( plugin_dir_path( __FILE__ ) . 'assets/steps-by-steps-script.js' );
			// 	    	echo "</script>";
			// 	    }
			// 		include( plugin_dir_path( __FILE__ ) . 'views/page.php' );
			// 		return;
			// 	}
			// }

		} else
			$step = $step_name;

		// end output buffering, grab the buffer contents, and empty the buffer
    	return ob_get_clean();
	}

	function add_meta_step( $user_id, $data,  $meta_key = '' ) {
		$meta_key = $this->user_meta_key;

		if ( get_user_meta( $user_id, $meta_key, true ) != "") {
			$stored_val = get_user_meta( $user_id, $this->user_meta_key, true );

			$push_val = $data;
			array_push($stored_val, $push_val);

			update_user_meta( $user_id, $meta_key, $old_val );			
		} else {
			add_user_meta( $user_id, $meta_key, $data );
		}

	}

	function is_step_complete($user_id, $step_name) {

		$val = get_user_meta( $user_id, $this->user_meta_key, true );

		var_dump($val);

		if ( array_key_exists( $step_name, $val ) ) {
			return true;
		} elseif  ( $val == '' ) {
			return false;
		} else {
			return false;
		}

	}

	 // ======================================== STEP 1
	function step_1_completion( $entry_id, $form_id ) {
		$step_name = "step_1";

		if( $form_id == '2' ) {
			$entry = FrmEntry::getOne($entry_id);
			if ( ! $entry->user_id ) {
				return; //don't continue if no user
			}

			if ( ! $this->is_step_complete( $entry->user_id, $step_name ) ) {
				$data[$step_name] = $entry_id;
				$this->add_meta_step( $entry->user_id, $data );
			}

			// if ( get_user_meta( $entry->user_id, 'frm_step_1', true ) != "") {
			// 	update_user_meta( $entry->user_id, 'frm_step_1', $entry_id );
			// 	// echo "Updated";
			// }
			// else {
			// 	add_user_meta( $entry->user_id, 'frm_step_1', $entry_id );
			// 	// echo "Added";
			// }
		}
	}

	// ======================================== STEP 2
	function step_2_completion( $entry_id, $form_id ) {
		$step_name = "step_2";

		if( $form_id ==  '4' ) {
			$entry = FrmEntry::getOne($entry_id);
			if ( ! $entry->user_id ) {
				return; //don't continue if no user
			}

			if ( ! $this->is_step_complete( $entry->user_id, $step_name ) ) {
				$data[$step_name] = $entry_id;
				$this->add_meta_step( $entry->user_id, $data );
			}

			// if ( get_user_meta( $entry->user_id, 'frm_step_3', true ) != "") {
			// 	update_user_meta( $entry->user_id, 'frm_step_3', $entry_id );
			// 	// echo "Updated";
			// }
			// else {
			// 	add_user_meta( $entry->user_id, 'frm_step_3', $entry_id );
			// 	// echo "Added";
			// }
		}
	}

}
	
	

$Stepy = new Stepy;


