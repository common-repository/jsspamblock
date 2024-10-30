<?php
/*
Plugin Name: JS SpamBlock
Plugin URI: http://www.paulbutler.org/
Description: Reduces spam by executing a JavaScript script on the client to ensure that it is not a spam-bot. Users without JavaScript can simply copy a code manually to comment.
Author: Paul Butler, Christoph "Stargazer" Bauer
Version: 2.1
Author URI: http://my.stargazer.at
*/

/* Configuration options */

// The file where comments are logged
// Leave blank '' for no log file
// Working directory is path of wordpress index
// File must be editable by PHP
// default: ''
define('JSSPAMBLOCK_LOGFILE', 'jsspamblock.log');

// If this is set to false, the form will not be shown automatically.
// It must be called with jsspamblock_doform() in the template.
// Useful for custom positioning and to get it to work with WP-Cache
// default: true
// As of version 1.1, this is detected automatically.
define('JSSPAMBLOCK_FORMLISTENER', true);

// Delete comments that are detected as spam
// Otherwise, the script just moderates the comments as spam and 
// they remain in the database. Since they are marked as spam, they
// are currently not accessable from the admin interface, so unless
// you need the comments in the database for some reason there is
// no reason to set this to false.
// default: true
define('JSSPAMBLOCK_DELETECOMMENTS', true);

/* End configuration options */

class jsspamblock {
   var $didform = false;
   
   function header() {
      if(!isset($_SESSION)){
         session_start();
      }
   }
   
   function do_form () {
      if($this->didform)
         return;
      
      $this->didform = true;
      
      $num = rand(1000, 9999);
      $hash = md5(uniqid(rand(), true));
      $_SESSION['jsspamblock'][$hash] = $num;
?>
<div id="jsspamblock_hideable">
	<input name="jsspamblock_hidden" type="hidden" value="<?php echo $hash; ?>" />
	<p>For spam filtering purposes, please copy the number <strong><?php echo $num; ?></strong> to the field below:</p>
	<input name="jsspamblock_input" id="jsspamblock_input" tabindex="5" value="" />
</div>
<script type="text/javascript">
	document.getElementById("jsspamblock_input").value = <?php echo $num; ?>;
	document.getElementById("jsspamblock_hideable").style.display = "none";
</script>
<?php
   }

   function check_comment ($id) {
      global $wpdb;
      
      $this->header();      
      $log = false;
      
      if(JSSPAMBLOCK_LOGFILE != ''){
         $log = fopen(JSSPAMBLOCK_LOGFILE, 'a');
      }
      
      $comments_table = $wpdb->prefix . "comments";
      
      // It is unlikely that bots do have a login - so we don't need to check logged in users
      // which is a workaround for the admin-reply-to-comment stuff
      
      if (!is_user_logged_in()) {

	if(!isset($_POST['jsspamblock_hidden'])){
	  wp_die(__('It appears that JS SpamBlock is not installed properly. Please check the documentation for instructions on installation for WordPress templates without a comment form hook.'));
	}
      
	$hash = $_POST['jsspamblock_hidden'];
	$code = $wpdb->escape(isset($_POST['jsspamblock_input'])?$_POST['jsspamblock_input']:'');
      
	if(isset($_SESSION['jsspamblock'][$hash]) && $code == $_SESSION['jsspamblock'][$hash]) {
	  unset($_SESSION['jsspamblock'][$hash]);
	  // comment is ok, do nothing
	  if($log){
	      fwrite($log, "Comment approved from ".$_SERVER['REMOTE_ADDR']." at ".date('M j, Y - G:i:s')." ($code, $hash)".PHP_EOL);
	  }
	} else {
	  if(!preg_match('/^\d+$/', $id)){
	      // ID given is not a valid number
	      return;
	  } else {
	      if($log){
		fwrite($log, "Comment REJECTED from ".$_SERVER['REMOTE_ADDR']." at ".date('M j, Y - G:i:s')." ($code, $hash)".PHP_EOL);
	      }
	      if(JSSPAMBLOCK_DELETECOMMENTS){
		$sql = "DELETE FROM $comments_table WHERE comment_id = $id";
	      } else {
		$sql = "UPDATE $comments_table SET comment_approved = 'spam' WHERE comment_id = $id";
	      }
	      $wpdb->query($sql);
	      wp_die( __('In order to prevent spam, you must manually copy the given number if you do not have JavaScript enabled. Please go back and try again.'));
	  }
	}
	return $id;
    }
  }
}

if(!function_exists('wp_die')){
  function wp_die($message){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title>WordPress &rsaquo; Error</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
	html { background: #eee; }

body {
	background: #fff;
	color: #000;
	font-family: Georgia, "Times New Roman", Times, serif;
	margin-left: 20%;
	margin-right: 20%;
	padding: .2em 2em;
}

h1 {
	color: #006;
	font-size: 18px;
	font-weight: lighter;
}

h2 { font-size: 16px; }

p, li, dt {
	line-height: 140%;
	padding-bottom: 2px;
}

ul, ol { padding: 5px 5px 5px 20px; }

#logo { margin-bottom: 2em; }

.step a, .step input { font-size: 2em; }

td input { font-size: 1.5em; }

.step, th { text-align: right; }

#footer {
	text-align: center; 
	border-top: 1px solid #ccc; 
	padding-top: 1em; 
	font-style: italic;
}
	</style>
</head>
<body>
	<h1 id="logo"><img alt="WordPress" src="wp-admin/images/wordpress-logo.png" /></h1>
	<p><?php echo $message; ?></p>
</body>
</html>
<?php
    die;
  }
}

$jsspamblock = new jsspamblock();

// Actions
if(function_exists('add_action')){
   if(JSSPAMBLOCK_FORMLISTENER){
      add_action('comment_form', array($jsspamblock, 'do_form'));
   }
   add_action('comment_post', array($jsspamblock, 'check_comment'));
   add_action('get_header', array($jsspamblock, 'header'));
}

?>
