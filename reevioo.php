<?php
	/*
	Plugin Name: Reevioo Rich Snippet Plugin
	Description: Insert JSON LD rich snippet from Reevioo
	Version: 1.0.0
	Author: Reevioo
	Author URI: http://www.reevioo.com
	*/
	
	register_activation_hook(__FILE__, 'reevioo_activate');
	add_action('admin_menu', 'reevioo_menu');
		
	function reevioo_getOptionName()
	{
		return 'reevioo';
	}
	
	function reevioo_activate()
	{
		$optionexists = false;
		$vals = array();
		$currentoption = get_option(reevioo_getOptionName());
		if ($currentoption!==false)
		{
			$vals = $currentoption;
			$optionexists = true;
		}
		
		$defaultvals = array('reevioo_id' => '');
		foreach ($defaultvals as $k => $defaultval)
		{
			if (!isset($vals[$k]))
				$vals[$k] = $defaultval;
		}
		
		if ($optionexists)
			update_option(reevioo_getOptionName(), $vals);
		else
			add_option(reevioo_getOptionName(), $vals);
	}
	
	function reevioo_menu() 
	{
		add_options_page('Reevioo', 'Reevioo', 'manage_options', 'reevioo', 'reevioo_options');
	}

	function reevioo_options() 
	{
		if (!current_user_can('manage_options'))
			wp_die('Je hebt niet voldoende permissies om deze pagina te gebruiken.');
		
		$saved = false;
		$error = false;
		$fields = array('reevioo_id');
		$optionvalue = get_option(reevioo_getOptionName());
		
		foreach ($fields as $field)
		{
			$$field = isset($_POST[$field]) ? trim($_POST[$field]) : $optionvalue[$field];
		}

		if (isset($_POST['Submit']))
		{
			if ($error===false)
			{
				$vals = array();
				foreach ($optionvalue as $key => $value)
					$vals[sanitize_key($key)] = sanitize_text_field($$key);

				update_option(reevioo_getOptionName(), $vals);
				$optionvalue = $vals;
				$saved = true;
			}
		}
		?>
		<div class="wrap">
			<h2>Reevioo</h2>
			<?php
				if ($saved)
				{
					?>
					<div class="updated"><p><strong>Je instellingen zijn opgeslagen</strong></p></div>
					<?php
				} elseif ($error!==false)
				{
					?>
					<div class="error"><p><strong><?php echo esc_html($error);?></strong></p></div>
					<?php
				}
			?>
			
			<form id="reeviooform" name="form1" method="post" action="" enctype="multipart/form-data">
				<h3>Instellingen</h3>
				<table class="form-table">
					<tbody>
						<tr>
							<td><label for="reevioo_id">Reevioo ID</label></td>
							<td><input type="text" name="reevioo_id" id="reevioo_id" value="<?php echo esc_html($reevioo_id);?>" /></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="Opslaan" />
				</p>
			</form>
		</div>
		<?php
	}
	
	function reevioo_rich_snippet() 
	{
		$optionvalue = get_option(reevioo_getOptionName());
		if (is_array($optionvalue) && isset($optionvalue['reevioo_id']))
		{
			$rs = file_get_contents('https://www.reevioo.com/widget.php?jd&id='.intval($optionvalue['reevioo_id']));
			if ($rs!==false)
			{
				echo $rs;
			}
		}
	}
	//if (is_plugin_active('reevioo/reevioo.php'))
		add_action('wp_footer', 'reevioo_rich_snippet');
?>