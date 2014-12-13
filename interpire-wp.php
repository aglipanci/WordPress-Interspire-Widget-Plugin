<?php
/*
Plugin Name: Interspire WP Integration
Plugin URI: http://dm-consulting.biz
Description: Interspire integration
Version: 1.0.0
Author: Agli Panci
Author URI: http://dm-consulting.biz
License: GPL2
*/

if(class_exists("WP_Widget")){
class interspire_wp extends WP_Widget
{
	

  function interspire_wp()
  {
    $widget_ops = array('classname' => 'interspire_wp', 'description' => 'Allows to send email to site administrator' );
    $this->WP_Widget('interspire_wp', 'Interspire WP Integration', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => 'Subscribe here' ));
    
    $title = $instance['title'];
    if(empty($title))
    $title = 'Subscribe here';
    
   ?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('interpire_url'); ?>">Interspire URL: <input class="widefat" id="<?php echo $this->get_field_id('interpire_url'); ?>" name="<?php echo $this->get_field_name('interpire_url'); ?>" type="text" value="<?php echo esc_attr($instance['interpire_url']); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('interpire_user'); ?>">Interspire User: <input class="widefat" id="<?php echo $this->get_field_id('interpire_user'); ?>" name="<?php echo $this->get_field_name('interpire_user'); ?>" type="text" value="<?php echo esc_attr($instance['interpire_user'] ); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('interpire_token'); ?>">Interspire Token: <input class="widefat" id="<?php echo $this->get_field_id('interpire_token'); ?>" name="<?php echo $this->get_field_name('interpire_token'); ?>" type="text" value="<?php echo esc_attr($instance['interpire_token']); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('interpire_list'); ?>">Interspire List ID: <input class="widefat" id="<?php echo $this->get_field_id('interpire_list'); ?>" name="<?php echo $this->get_field_name('interpire_list'); ?>" type="text" value="<?php echo esc_attr($instance['interpire_list']); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['interpire_url'] = $new_instance['interpire_url'];
    $instance['interpire_user'] = $new_instance['interpire_user'];
    $instance['interpire_token'] = $new_instance['interpire_token'];
    $instance['interpire_list'] = $new_instance['interpire_list'];

    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    
    if (!empty($title))
      echo $before_title . $title . $after_title;
 
    // Help Form
    ?>
    <div id="get_help">
          <form name="wdm_give_help" id="wdm_give_help" method="POST">
          <label for="help_name">Name</label><input type="text" id="is_name" name="is_name" value="" class="required" />
          <label for="help_name">Surname</label><input type="text" id="is_surname" name="is_surname" value="" class="required" />
          <label for="help_email">Email</label><input type="text" id="is_email" name="is_email" value="" class="required email" />
          <br />
          <input type="submit" name="submit_help" id="submit_help" value="Submit" />
          </form>
        </div>
    
    <script type="text/javascript">
  jQuery(document).ready(function(){
			jQuery("#wdm_give_help").validate();
  });
  </script>
    
    <?php
    if (!empty($_POST["is_name"]) && !empty($_POST["is_surname"]) && !empty($_POST["is_email"]))
    {
     //Send meassge
    $is_name = $_POST["is_name"];
    $is_surname = $_POST["is_surname"];
    $is_email = $_POST["is_email"];
    $interpire_user = $instance['interpire_user'];
    $interpire_token = $instance['interpire_token'];
    $interpire_url = $instance['interpire_url'];
    $interpire_list = $instance['interpire_list'];
    

    if(AddToInterspire($is_name, $is_surname, $is_email, $interpire_user, $interpire_token, $interpire_url, $interpire_list))
    echo "<script type='text/javascript'>alert('Message sent successfully. We will get back to you soon.');</script>";
    else
    echo "<script type='text/javascript'>alert('Message sending failed.');</script>";
    }
    
    echo $after_widget;
  }
  

}
add_action( 'widgets_init', create_function('', 'return register_widget("interspire_wp");') );


function AddToInterspire($name, $surname, $email, $is_user, $is_token, $is_url, $is_listid){


	$xml = '<xmlrequest>
	<username>'.$is_user.'</username>
	<usertoken>'.$is_token.'</usertoken>
	<requesttype>subscribers</requesttype>
	<requestmethod>AddSubscriberToList</requestmethod>
	<details>
	<emailaddress>'.$email.'</emailaddress>
	<mailinglist>'.$is_listid.'</mailinglist>
	<format>html</format>
	<confirmed>yes</confirmed>
	<customfields>
	<item>
	<fieldid>2</fieldid>
	<value>'.$name.'</value>
	</item>
	<item>
	<fieldid>4</fieldid>
	<value>'.$surname.'</value>
	</item>
	</customfields>
	</details>
	</xmlrequest>
	';


	$ch = curl_init($is_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	$result = @curl_exec($ch);

	if($result === false) {
	return false;
	}else {

	$xml_doc = simplexml_load_string($result);
	
	if ($xml_doc->status == 'SUCCESS') {
	return true;
	} else {
	return false;
	}
	}

  }

}
?>