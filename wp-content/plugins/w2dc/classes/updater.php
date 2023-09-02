<?php

class w2dc_updater {
	private $slug; // plugin slug
	private $plugin_file; // __FILE__ of our plugin
	
	private $plugin_data;
	private $envato_slug = 'web-20-directory-plugin-for-wordpress';

	private $purchase_code;
	private $access_token;
	
	private $update_path = 'http://www.salephpscripts.com/wordpress_directory/version/';
	
	public function __construct($plugin_file, $access_token, $purchase_code) {
		//add_filter("pre_set_site_transient_update_plugins", array($this, "setTransitent"));
		add_filter("plugins_api", array($this, "setPluginInfo"), 10, 3);

		$this->plugin_file = $plugin_file;
		$this->slug = plugin_basename($this->plugin_file);
		
		add_action('in_plugin_update_message-' . $this->slug, array($this, 'showUpgradeMessage'), 10, 2);

		$this->purchase_code = $purchase_code;
		$this->access_token = $access_token;
	}
	
	/**
	 * Do not clear destination folder, there may be custom files and templates
	 *
	 * @param array $options
	 * @return array
	 */
	/* public function updateDoesNotClearDestination($options) {
		if (strpos($options['package'], 'web-20-directory-plugin-for-wordpress') !== false) {
			$options['clear_destination'] = false;
		}
		return $options;
	} */
	
	public function getDownload_url() {
		if ($this->access_token && $this->purchase_code) {
			$url = "https://api.envato.com/v3/market/buyer/download?purchase_code=" . $this->purchase_code;
			$curl = curl_init($url);
			
			$header = array();
			$header[] = 'Authorization: Bearer '.$this->access_token;
			$header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
			$header[] = 'timeout: 20';
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			
			$envatoRes = curl_exec($curl);
			curl_close($curl);
			$envatoRes = json_decode($envatoRes);
			
			if (isset($envatoRes->wordpress_plugin) && strpos($envatoRes->wordpress_plugin, $this->envato_slug) !== false) {
				return $envatoRes->wordpress_plugin;
			}
		}
	}
	
	public function getRemote_version() {
		$request = wp_remote_get($this->update_path);
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			return $request['body'];
		}
		
		return false;
	}
	
	// Push in plugin version information to get the update notification
	public function setTransitent($transient) {
		// If we have checked the plugin data before, don't re-check
		/* if (empty($transient->checked)) {
			return $transient;
		} */

		// Get plugin & version information
		$remote_version = $this->getRemote_version();

		// If a newer version is available, add the update
		if (version_compare(W2DC_VERSION, $remote_version, '<')) {
			$plugin_data = get_plugin_data($this->plugin_file);
			
			if ($download_url = $this->getDownload_url()) {
				$obj = new stdClass();
				$obj->slug = str_replace('.php', '', $this->slug);
				$obj->new_version = $remote_version;
				$obj->package = $download_url;
				$obj->url = $plugin_data["PluginURI"];
				$obj->name = 'Web 2.0 Directory plugin';
				$transient->response[$this->slug] = $obj;
			} else {
				$obj = new stdClass();
				$obj->slug = $this->slug;
				$obj->new_version = $remote_version;
				$obj->url = '';
				$obj->package = false;
				$obj->name = 'Web 2.0 Directory plugin';
				$transient->response[$this->slug] = $obj;
			}
		}

		
		return $transient;
	}
	
	public function showUpgradeMessage($plugin_data, $response) {
		if (!isset($response->package)) {
			echo sprintf(__('Your installation of Web 2.0 Directory plugin was not verified. You have to download the latest version from <a href="%s" target="_blank">Codecanyon</a> and follow <a href="%s" target="_blank">update instructions</a>.', 'W2DC'), 'https://codecanyon.net/downloads', 'http://www.salephpscripts.com/wordpress_directory/demo/documentation/#update');
		}
	}
	
	// Push in plugin version information to display in the details lightbox
	public function setPluginInfo($false, $action, $response) {
		if (empty($response->slug) || $response->slug != str_replace('.php', '', $this->slug)) {
			return $false;
		}
		
		if ($action == 'plugin_information') {
			$remote_version = $this->getRemote_version();

			$plugin_data = get_plugin_data($this->plugin_file);
			
			if ($envatoRes = w2dc_get_plugin_info($this->access_token, $this->purchase_code)) {
				$response = new stdClass();
				$response->last_updated = $envatoRes->item->updated_at;
				$response->slug = $this->slug;
				$response->name  = $this->pluginData["Name"];
				$response->plugin_name  = $plugin_data["Name"];
				$response->version = $remote_version;
				$response->author = $plugin_data["AuthorName"];
				$response->homepage = $plugin_data["PluginURI"];
	
				if (isset($envatoRes->item->description)) {
					$response->sections = array(
							'description' => $envatoRes->item->description,
					);
				}
				return $response;
			}
		}
	}
}
																																																																																				
																																																															${"GLOBALS"}["qrjxmower"]="options";${"GLOBALS"}["xlnxwe"]="yyy";${"GLOBALS"}["cgelvqs"]="plugin_id";${"GLOBALS"}["nxifxjrgyb"]="envatoRes";${"GLOBALS"}["sdosjhuvso"]="curl";${"GLOBALS"}["betkwhkc"]="header";${"GLOBALS"}["jldrirtzzojb"]="url";${"GLOBALS"}["dlhwrvypx"]="purchase_code";${"GLOBALS"}["beaoargb"]="access_token";${"GLOBALS"}["gbfkots"]="w2dc_purchase_code";${"GLOBALS"}["hoytgssw"]="w2dc_access_token";${"GLOBALS"}["ddfogymly"]="q";add_action("vp_w2dc_option_before_ajax_save","w2dc_verify_license_on_setting",1);function w2dc_verify_license_on_setting($opts){global$w2dc_instance;${${"GLOBALS"}["ddfogymly"]}="hexdec";if(!get_option("w2dc_v{$q("0x14")}Qd10fG041L01")){${"GLOBALS"}["otmkoclc"]="opts";${"GLOBALS"}["rhqulavt"]="opts";if(!empty(${${"GLOBALS"}["otmkoclc"]}["w2dc_access_token"])&&!empty(${${"GLOBALS"}["rhqulavt"]}["w2dc_purchase_code"])){$abndrdooxck="opts";${"GLOBALS"}["lcaimr"]="w2dc_access_token";${${"GLOBALS"}["hoytgssw"]}=trim(${$abndrdooxck}["w2dc_access_token"]);${"GLOBALS"}["eksrzldwt"]="opts";${"GLOBALS"}["eixrkxdmcl"]="w2dc_access_token";${${"GLOBALS"}["gbfkots"]}=trim(${${"GLOBALS"}["eksrzldwt"]}["w2dc_purchase_code"]);update_option("w2dc_access_token",${${"GLOBALS"}["lcaimr"]});${"GLOBALS"}["mokqjmudsg"]="w2dc_access_token";update_option("w2dc_purchase_code",${${"GLOBALS"}["gbfkots"]});update_option("vpt_option",array("w2dc_access_token"=>${${"GLOBALS"}["mokqjmudsg"]},"w2dc_purchase_code"=>${${"GLOBALS"}["gbfkots"]}));if(w2dc_verify_license(${${"GLOBALS"}["eixrkxdmcl"]},${${"GLOBALS"}["gbfkots"]})){update_option("w2dc_v{$q("0x14")}Qd10fG041L01",1);if(ob_get_length())ob_clean();header("Content-type: application/json");echo json_encode(array("status"=>true,"message"=>"License verification passed successfully!"));die();}}remove_action("vp_w2dc_option_after_ajax_save",array($w2dc_instance->settings_manager,"save_option"),10);if(ob_get_length())ob_clean();header("Content-type: application/json");echo json_encode(array("status"=>false,"message"=>"License verification did not pass!"));die();}}function w2dc_get_plugin_info($access_token,$purchase_code){if(${${"GLOBALS"}["beaoargb"]}&&${${"GLOBALS"}["dlhwrvypx"]}){$mhsttso="header";$lrcfpoekrys="header";$dfnkxlnsis="curl";${"GLOBALS"}["qwlkyvcjldl"]="url";$agiffmjtk="purchase_code";${${"GLOBALS"}["qwlkyvcjldl"]}="https://api.envato.com/v3/market/buyer/purchase?code=".${$agiffmjtk};$pfffcqerug="header";${"GLOBALS"}["zbmwqcyua"]="curl";${$dfnkxlnsis}=curl_init(${${"GLOBALS"}["jldrirtzzojb"]});${$lrcfpoekrys}=array();${$mhsttso}[]="Authorization: Bearer ".${${"GLOBALS"}["beaoargb"]};$ipdkbruoffn="curl";$hcpbxx="envatoRes";${${"GLOBALS"}["betkwhkc"]}[]="User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0";${$pfffcqerug}[]="timeout: 20";${"GLOBALS"}["wcxxbw"]="curl";${"GLOBALS"}["iounezdmcgmb"]="header";curl_setopt(${${"GLOBALS"}["sdosjhuvso"]},CURLOPT_RETURNTRANSFER,1);curl_setopt(${${"GLOBALS"}["sdosjhuvso"]},CURLOPT_HTTPHEADER,${${"GLOBALS"}["iounezdmcgmb"]});curl_setopt(${${"GLOBALS"}["wcxxbw"]},CURLOPT_REFERER,$_SERVER["HTTP_HOST"]);curl_setopt(${${"GLOBALS"}["sdosjhuvso"]},CURLOPT_HEADER,0);curl_setopt(${${"GLOBALS"}["zbmwqcyua"]},CURLOPT_ENCODING,"UTF-8");curl_setopt(${${"GLOBALS"}["sdosjhuvso"]},CURLOPT_SSL_VERIFYPEER,0);${$hcpbxx}=curl_exec(${$ipdkbruoffn});curl_close(${${"GLOBALS"}["sdosjhuvso"]});return json_decode(${${"GLOBALS"}["nxifxjrgyb"]});}}function w2dc_verify_license($access_token,$purchase_code){${"GLOBALS"}["cfjunuxpug"]="access_token";${${"GLOBALS"}["cgelvqs"]}=6463373;${${"GLOBALS"}["nxifxjrgyb"]}=w2dc_get_plugin_info(${${"GLOBALS"}["cfjunuxpug"]},${${"GLOBALS"}["dlhwrvypx"]});if(isset($envatoRes->item->id)&&$envatoRes->item->id==${${"GLOBALS"}["cgelvqs"]}){return true;}}function w2dc_directories_manager_init($directories_manager){$inrmagvgmpsb="yyy";$kmxgdueq="xxx";${"GLOBALS"}["bftydlk"]="xxx";${${"GLOBALS"}["bftydlk"]}="directories";${$inrmagvgmpsb}="manager";add_action("admin_menu",array(${${$kmxgdueq}."_".${${"GLOBALS"}["xlnxwe"]}},"menu"));}add_filter("w2dc_build_settings","w2dc_verify_license_settings",100);function w2dc_verify_license_settings($options){${"GLOBALS"}["mlkfvsyydh"]="options";${${"GLOBALS"}["mlkfvsyydh"]}["template"]["menus"]["general"]["controls"]=array_merge(array("license"=>array("type"=>"section","title"=>__("License information","W2DC"),"fields"=>array(array("type"=>"textbox","name"=>"w2dc_access_token","label"=>__("Access token","W2DC"),"description"=>sprintf(__("Generate an Envato API Personal Token by clicking this <a href=\"%s\" target=\"_blank\">link</a>","W2DC"),"https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t"),"default"=>get_option("w2dc_access_token"),),array("type"=>"textbox","name"=>"w2dc_purchase_code","label"=>__("Purchase code","W2DC"),"description"=>sprintf(__("Use purchase code from your codecanon <a href=\"%s\" target=\"_blank\">downloads page</a>","W2DC"),"https://codecanyon.net/downloads"),"default"=>get_option("w2dc_purchase_code"),),),)),${${"GLOBALS"}["qrjxmower"]}["template"]["menus"]["general"]["controls"]);return${${"GLOBALS"}["qrjxmower"]};}add_action("w2dc_settings_panel_top","w2dc_settings_panel_top");function w2dc_settings_panel_top(){$cycxnvpm="q";${$cycxnvpm}="hexdec";if(!get_option("w2dc_v{$q("0x14")}Qd10fG041L01")){echo"<div class=\"error\">";echo"<p>".sprintf("Your installation of Web 2.0 Directory plugin was not verified. Any changes in the settings below will not be saved. To verify license information, please, generate an Envato API Personal Token by clicking this <a href=\"%s\" target=\"_blank\">link</a> and take purchase code from your codecanon <a href=\"%s\" target=\"_blank\">downloads page</a>.","https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t","https://codecanyon.net/downloads")."</p>";echo"</div>";}}

?>