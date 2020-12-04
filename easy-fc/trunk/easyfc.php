<?php
/*
Plugin Name: Easy Flashcards
Description: Easily integrate flashcards in your posts and pages
Version: 1.0
Author: Florin Muscalu
Author URI: http://www.florinm.ro
Plugin URI: http://florinm.ro/easy-flashcards-wordpress-plugin/
License: MIT License
License URI: http://opensource.org/licenses/MIT

Easy Flashcards
Copyright (C) 2020, Florin Muscalu - florin.muscalu@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
 
function easyfc_scripts(){
	wp_enqueue_script('easyfc-script',plugins_url('easyfc.js' , __FILE__ ), $ver="1.3" );

    $scriptData = array(
        'easyfc_questions_text' => get_option('easyfc_questions_text'),
		'easyfc_question_text' => get_option('easyfc_question_text'),
		'easyfc_correct_answers_text' => get_option('easyfc_correct_answers_text'),
		'easyfc_correct_all_text' => get_option('easyfc_correct_all_text'),
		'easyfc_correct_answers_text2' => get_option('easyfc_correct_answers_text2'),
		'easyfc_questions_total' => get_option('easyfc_questions_total'),
		'easyfc_front_textsize' => get_option('easyfc_front_textsize'),
		'easyfc_back_textsize' => get_option('easyfc_back_textsize'),
		'easyfc_latest_try' => get_option('easyfc_latest_try'),
		'easyfc_correct_latest' => get_option('easyfc_correct_latest'),
    );
    wp_localize_script('easyfc-script', 'fc_options', $scriptData);
	
	wp_register_style( 'easyfc-style', plugins_url('easyfc.css', __FILE__), $ver="1.3" );
	wp_enqueue_style( 'easyfc-style' );
}

class Question
	{
    	public $q;
    	public $a;
		public $font_front;
		public $font_back;
		public $height;
	}

function easyfc_build($atts,$content=null){
	
	$string = file_get_contents($atts['file']);
	$json_a = json_decode($string, true);
	
	$list = [];
	foreach ($json_a as $k => $v) {
		$Q = new Question();
		$Q->q = $v["q"];
		$Q->a = $v["a"];	
//		echo $Q->q, ", \"", $Q->a, "\"<br><br>";
		$Q->font_front = get_option('easyfc_front_textsize');	
		if (isset ($v["font_front"])) {
			$Q->font_front = $v["font_front"];	
		}
		$Q->font_back = get_option('easyfc_back_textsize');	
		if (isset ($v["font_back"])) {
			$Q->font_back = $v["font_back"];	
		}
		$Q->height = "200";	
		if (isset ($v["height"])) {
			$Q->height = $v["height"];	
		}
		array_push($list, $Q);
	};	
		
	$ret ='<script type="text/javascript">';
	$ret = $ret . 'instance++;';
	$ret = $ret . 'sets.push(new flashcards(instance,"' . $atts['title'] . '"));';
	$ret = $ret . 'sets[instance].amount = ' . count($list) . ';';
	
	for ($c=0;$c<count($list);$c++) 
		$ret = $ret . "sets[instance].cards.push({q:'" . $list[$c]->q . "', font_front:'" . $list[$c]->font_front . "', font_back:'" . $list[$c]->font_back . "', height:'" . $list[$c]->height . "', a:'" . $list[$c]->a . "',correct:0});\n";  
		
	$ret = $ret . '</script>';
	$ret = $ret . '
	<div id="fc_start" class="flashcard_start">
		<button id="fc_start_btn" class="flashcart_btn_start">
			<script type="text/javascript">
				if (getCookie(sets[instance].title)==""){
					document.write(fc_setStartBtnText(sets[instance].title, sets[instance].amount, ""));
				}
				else{
					
					document.write(fc_setStartBtnText(sets[instance].title, sets[instance].amount, "' . get_option('easyfc_latest_try') . ': "+getCookie(sets[instance].title)+" ' . get_option('easyfc_correct_latest') . '."));
				}
			</script>
		</button>
	</div>
	<div id="fc_main" class="flashcard_main">
		<div class="flashcard_header">' . $atts['title'] . '</div>
		<div id="fc_flip" class="flipCard"> 
  			<div id="fc_content" class="card" onclick="this.classList.toggle(\'flipped\');"> 
    			<div id="fc_content_front" class="side front"></div> 
    			<div id="fc_content_back" class="side back"></div> 
  			</div> 
		</div> 
		
		<div id="fc_footer" class="flashcard_footer"></div>
		<div class="flashcard_buttons">
			<button id="fc_btn_corect" class="flashcard_btn_correct" >' . get_option('easyfc_correct_btn') . '</button>
			<button id="fc_btn_gresit" class="flashcard_btn_wrong" >' . get_option( 'easyfc_wrong_btn' ) . '</button>
			<button id="fc_btn_reset" class="flashcard_btn_reset" style="align:left">' . get_option( 'easyfc_reset_btn' ) . '</button>
		</div>
	</div>
	
	<div id="fc_finish" class="flashcard_finish" style="display:none">
		<h3>' . $atts['title'] . '</h3>
		<p id="fc_message"></p>
		<div id="fc_repeat">
			<p>' . get_option( 'easyfc_repeat_wrong_text' ) . '</p>
			<div>
				<button id="fc_repeat_btn" class="flashcard_btn_yes">' . get_option( 'easyfc_yes_btn' ) . '</button>
				<button id="fc_repeat_btn_nu_" class="flashcard_btn_no">' . get_option( 'easyfc_no_btn' ) . '</button>
			</div>
		</div>
		<div id="fc_repeat_test">
			<p>' . get_option( 'easyfc_repeat_all_text' ) . '</p>
			<div>
				<button id="fc_repeat_test_btn" class="flashcard_btn_yes">' . get_option( 'easyfc_yes_btn' ) . '</button>
				<button id="fc_repeat_btn_nu1_" class="flashcard_btn_no">' . get_option( 'easyfc_no_btn' ) . '</button>
			</div>
		</div>
	</div>
	 
	<script type="text/javascript">
		sets[instance].fc_setDivs();
	</script>';
	return $ret;
}

	if (! is_admin() ) {
		add_action( 'wp_enqueue_scripts', 'easyfc_scripts' );
		add_shortcode("easyfc", "easyfc_build");
	}
	add_action( 'init', 'set_default_options' );
	add_action( 'admin_menu', 'easy_fc_info_menu' );  
 
	function set_default_options() {
    	if (get_option('easyfc_correct_btn', '1') == '1') update_option('easyfc_correct_btn', 'Correct');
		if (get_option('easyfc_wrong_btn', '1') == '1') update_option('easyfc_wrong_btn', 'Wrong');
		if (get_option('easyfc_reset_btn', '1') == '1') update_option('easyfc_reset_btn', 'Reset');
		if (get_option('easyfc_questions_text', '1') == '1') update_option('easyfc_questions_text', 'questions');
		if (get_option('easyfc_yes_btn', '1') == '1') update_option('easyfc_yes_btn', 'Yes');
		if (get_option('easyfc_no_btn', '1') == '1') update_option('easyfc_no_btn', 'No');
		if (get_option('easyfc_question_text', '1') == '1') update_option('easyfc_question_text', 'Question');
		if (get_option('easyfc_correct_answers_text', '1') == '1') update_option('easyfc_correct_answers_text', 'Correct answers');
		if (get_option('easyfc_correct_all_text', '1') == '1') update_option('easyfc_correct_all_text', 'all the questions');
		if (get_option('easyfc_repeat_all_text', '1') == '1') update_option('easyfc_repeat_all_text', 'Do you want to repeat the test?');
		if (get_option('easyfc_repeat_wrong_text', '1') == '1') update_option('easyfc_repeat_wrong_text', 'Do you want to repeat the questions you answered incorrectly?');
		if (get_option('easyfc_correct_answers_text2', '1') == '1') update_option('easyfc_correct_answers_text2', 'You correctly answered');
		if (get_option('easyfc_questions_total', '1') == '1') update_option('easyfc_questions_total', 'questions out of');
		if (get_option('easyfc_front_textsize', '1') == '1') update_option('easyfc_front_textsize', '32');
		if (get_option('easyfc_back_textsize', '1') == '1') update_option('easyfc_back_textsize', '32');
		if (get_option('easyfc_latest_try', '1') == '1') update_option('easyfc_latest_try', 'Latest try');
		if (get_option('easyfc_correct_latest', '1') == '1') update_option('easyfc_correct_latest', 'correct answers');
     }

	function easy_fc_info_menu(){    
		$page_title = 'Easy Flashcards Options';   
		$menu_title = 'Easy Flashcards';   
		$capability = 'manage_options';   
		$menu_slug  = 'easy-fc-settings';   
		$function   = 'easy_fc_info_page';   
		$icon_url   = 'dashicons-media-code';   
		$position   = 4;    
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); 
		add_action( 'admin_init', 'update_easy_fc_info' );
	} 
	
	function update_easy_fc_info() {
		$args = array('type' => 'string', 'default' => "Correct");
		register_setting( 'easyfc-settings', 'easyfc_correct_btn', $args); 
		
		$args = array('type' => 'string', 'default' => "Wrong");
		register_setting( 'easyfc-settings', 'easyfc_wrong_btn', $args ); 
		
		$args = array('type' => 'string', 'default' => "Reset");
		register_setting( 'easyfc-settings', 'easyfc_reset_btn', $args ); 
		
		$args = array('type' => 'string', 'default' => "questions");
		register_setting( 'easyfc-settings', 'easyfc_questions_text', $args ); 
			
		$args = array('type' => 'string', 'default' => "Yes");
		register_setting( 'easyfc-settings', 'easyfc_yes_btn', $args ); 
		
		$args = array('type' => 'string', 'default' => "No");
		register_setting( 'easyfc-settings', 'easyfc_no_btn', $args ); 
		
		$args = array('type' => 'string', 'default' => "Question");
		register_setting( 'easyfc-settings', 'easyfc_question_text', $args ); 
		
		$args = array('type' => 'string', 'default' => "Correct answers");
		register_setting( 'easyfc-settings', 'easyfc_correct_answers_text', $args ); 
		
		$args = array('type' => 'string', 'default' => "all the questions");
		register_setting( 'easyfc-settings', 'easyfc_correct_all_text', $args ); 
		
		$args = array('type' => 'string', 'default' => "Do you want to repeat the test?");
		register_setting( 'easyfc-settings', 'easyfc_repeat_all_text', $args ); 
		
		$args = array('type' => 'string', 'default' => "Do you want to repeat the questions you answered incorrectly?");
		register_setting( 'easyfc-settings', 'easyfc_repeat_wrong_text', $args ); 
		
		$args = array('type' => 'string', 'default' => "You correctly answered");
		register_setting( 'easyfc-settings', 'easyfc_correct_answers_text2', $args ); 
		
		$args = array('type' => 'string', 'default' => "questions out of");
		register_setting( 'easyfc-settings', 'easyfc_questions_total', $args ); 
		
		$args = array('type' => 'integer', 'default' => 32);
		register_setting( 'easyfc-settings', 'easyfc_front_textsize', $args ); 
		
		$args = array('type' => 'integer', 'default' => 32);
		register_setting( 'easyfc-settings', 'easyfc_back_textsize', $args ); 
		
		$args = array('type' => 'string', 'default' => 'Latest try');
		register_setting( 'easyfc-settings', 'easyfc_latest_try', $args );
		
		$args = array('type' => 'string', 'default' => 'correct answers');
		register_setting( 'easyfc-settings', 'easyfc_correct_latest', $args );
	}

	function easy_fc_info_page(){ 
		?>  <h1>Easy fc options:</h1> 
			<form method="post" action="options.php">     
				<?php settings_fields( 'easyfc-settings' ); ?>     
				<?php do_settings_sections( 'easyfc-settings' ); ?>     
				<table class="form-table">       
					<tr valign="top">       
						<th scope="row" style="width:300px">Correct button text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_correct_btn" value="<?php echo get_option( 'easyfc_correct_btn' ); ?>"/></td>       
					</tr>     
					<tr valign="top">       
						<th scope="row" style="width:300px">Wrong button text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_wrong_btn" value="<?php echo get_option( 'easyfc_wrong_btn' ); ?>"/></td>       
					</tr>     
					<tr valign="top">       
						<th scope="row" style="width:300px">Reset button text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_reset_btn" value="<?php echo get_option( 'easyfc_reset_btn' ); ?>"/></td>       
					</tr>     
					<tr valign="top">       
						<th scope="row" style="width:300px">Yes button text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_yes_btn" value="<?php echo get_option( 'easyfc_yes_btn' ); ?>"/></td>       
					</tr>     
					<tr valign="top">       
						<th scope="row" style="width:300px">No button text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_no_btn" value="<?php echo get_option( 'easyfc_no_btn' ); ?>"/></td>       
					</tr>     
					<tr valign="top">       
						<th scope="row" style="width:300px">"questions" text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_questions_text" value="<?php echo get_option( 'easyfc_questions_text' ); ?>"/></td>       
					</tr>     	
					<tr valign="top">       
						<th scope="row" style="width:300px">Bottom question counter text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_question_text" value="<?php echo get_option( 'easyfc_question_text' ); ?>"/></td>       
					</tr>    
					<tr valign="top">       
						<th scope="row" style="width:300px">Bottom correct answers text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_correct_answers_text" value="<?php echo get_option( 'easyfc_correct_answers_text' ); ?>"/></td>       
					</tr>  
					<tr valign="top">       
						<th scope="row" style="width:300px">"Do you want to repeat the test?" text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_repeat_all_text" value="<?php echo get_option( 'easyfc_repeat_all_text' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"Do you want to repeat the questions you answered incorrectly?" text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_repeat_wrong_text" value="<?php echo get_option( 'easyfc_repeat_wrong_text' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"You correctly answered" message text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_correct_answers_text2" value="<?php echo get_option( 'easyfc_correct_answers_text2' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"all the questions" message text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_correct_all_text" value="<?php echo get_option( 'easyfc_correct_all_text' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"questions out of" message text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_questions_total" value="<?php echo get_option( 'easyfc_questions_total' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">Front card text size:</th>       
						<td> <input type="text" style="width:50px" name="easyfc_front_textsize" value="<?php echo get_option( 'easyfc_front_textsize' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">Back card text size:</th>       
						<td> <input type="text" style="width:50px" name="easyfc_back_textsize" value="<?php echo get_option( 'easyfc_back_textsize' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"Latest try" text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_latest_try" value="<?php echo get_option( 'easyfc_latest_try' ); ?>"/></td>       
					</tr>
					<tr valign="top">       
						<th scope="row" style="width:300px">"correct answers" text:</th>       
						<td> <input type="text" style="width:400px" name="easyfc_correct_latest" value="<?php echo get_option( 'easyfc_correct_latest' ); ?>"/></td>       
					</tr>
				</table>     
				<?php submit_button(); ?>   </form>  
		<?php 
	}
?>
