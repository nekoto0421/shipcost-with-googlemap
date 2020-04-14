<?php
add_action('admin_menu', 'add_GMSC_setting_menu');

function add_GMSC_setting_menu() {
	wp_register_script('GMSC-colpick_script', GMSC_URL.'/js/colpick/js/colpick.js');
	wp_enqueue_script('GMSC-colpick_script');
	wp_register_style('GMSC-colpick_style', GMSC_URL.'/js/colpick/css/colpick.css');
	wp_enqueue_style('GMSC-colpick_style');
	add_submenu_page('GMSC-plugin_main', __('設定'), __('設定'), __('manage_options'), 'GMSC_setting','add_GMSC_settings_page');
	add_action( 'admin_init', 'register_GMSC_plugin_settings' );
}
//先加上設定的頁面

function register_GMSC_plugin_settings() {
	//begin of地圖設定
	register_setting( 'google_map-group', 'plugin_google_map_api_key' );

	register_setting( 'google_map-group', 'plugin_google_map_control_component' );

	register_setting( 'google_map-group', 'plugin_google_map_style_setting' );

	register_setting( 'google_map-group', 'plugin_google_map_route_setting' );
	//end of地圖設定
	//begin of運費設定
	register_setting( 'google_map_base-group', 'plugin_google_map_extra_price' );

	register_setting( 'google_map_base-group', 'plugin_google_map_extra_distance' );

	register_setting( 'google_map_base-group', 'plugin_google_map_base_price' );

	register_setting( 'google_map_base-group', 'plugin_google_map_base_distance' );
	//end of運費設定

	//begin of金流設定
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_hashkey' );
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_hashIV' );
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_merchantID' );
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_orderNm' );
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_returnUrl' );
	register_setting( 'google_map_pay_setting-group', 'plugin_google_map_ecpay_responceUrl' );
	//end of金流設定

	//begin of表單樣式設定
	register_setting( 'google_map_pay_formstyle-group', 'plugin_google_map_formstyle-fontcolor');
	register_setting( 'google_map_pay_formstyle-group', 'plugin_google_map_formstyle-backgroundcolor' );
	//end of表單樣式設定
}
//記得表格中有新的設定時要在這邊註冊變數名稱,否則變數不會記錄進options.php

function add_GMSC_settings_page() {
?>
<div id="tabs" class="wrap">
	<ul class="nav-tab-wrapper">
		<li class="nav-tab"><a href="#tab-01">地圖設定</a></li>
		<li class="nav-tab"><a href="#tab-02">付費模組設定</a></li>
		<li class="nav-tab"><a href="#tab-03">樣式設定</a></li>
	</ul>
	<div id="tab-01">
		<div class="wrap">
			<h2>地圖基礎設定</h2>
			<br><br>
			<!-- option.php是固定的,wordpress會自動記錄所有設定值 -->
			<form method="post" action="options.php">
				<?php settings_fields('google_map-group'); ?>
				<!-- 要先宣告這句下面才有辦法使用上面註冊的變數 -->
				google map API Key&nbsp;&nbsp;:
				<input type="text" id="plugin_google_map_api_key" name="plugin_google_map_api_key" value="<?php echo get_option('plugin_google_map_api_key'); ?>"/>
				<br>
				<br>
				<input type="checkbox" name="plugin_google_map_control_component" value="1"
					   <?php if (get_option('plugin_google_map_control_component')==1) echo "checked" ; ?> />
				啟用地圖預設元件<br/>
				<br>
				地圖樣式 :
				<select id="plugin_google_map_style_setting" name="plugin_google_map_style_setting">
					<option value="default" <?php if (get_option('plugin_google_map_style_setting')=="default") echo "selected" ; ?>>預設</option>
					<option value="dark" <?php if (get_option('plugin_google_map_style_setting')=="dark") echo "selected" ; ?>>暗色</option>
					<option value="night" <?php if (get_option('plugin_google_map_style_setting')=="night") echo "selected" ; ?>>夜晚</option>
					<option value="Earth" <?php if (get_option('plugin_google_map_style_setting')=="Earth") echo "selected" ; ?>>大地</option>
				</select>
				<br><br>	
				預測路線 :
				<select id="plugin_google_map_route_setting" name="plugin_google_map_route_setting">
					<option value="DRIVING" <?php if (get_option('plugin_google_map_route_setting')=="DRIVING") echo "selected" ; ?>>開車</option>
					<option value="BICYCLING" <?php if (get_option('plugin_google_map_route_setting')=="BICYCLING") echo "selected" ; ?>>腳踏車</option>
					<option value="TRANSIT" <?php if (get_option('plugin_google_map_route_setting')=="TRANSIT") echo "selected" ; ?>>大眾運輸</option>
					<option value="WALKING" <?php if (get_option('plugin_google_map_route_setting')=="WALKING") echo "selected" ; ?>>走路</option>
				</select>
				<p class="submit">
					<input type="submit" class="button-primary" value="儲存" />
				</p>
			</form>
		</div>
	</div>
	<div id="tab-02">
		<div class="wrap">
			<h2>付費模組相關設定</h2>
			<br><br>
			<!-- option.php是固定的,wordpress會自動記錄所有設定值 -->
			<form method="post" action="options.php">
				<?php settings_fields('google_map_pay_setting-group'); ?>
				<!-- 要先宣告這句下面才有辦法使用上面註冊的變數 -->
				綠界Hashkey&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_hashkey" value="<?php echo get_option('plugin_google_map_ecpay_hashkey'); ?>"/>
				<br>
				<br>
				綠界HashIV&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_hashIV" value="<?php echo get_option('plugin_google_map_ecpay_hashIV'); ?>"/>
				<br>
				<br>
				綠界MerchantID&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_merchantID" value="<?php echo get_option('plugin_google_map_ecpay_merchantID'); ?>"/>
				<br>
				<br>
				訂單序號前綴(限英文)&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_orderNm" value="<?php echo get_option('plugin_google_map_ecpay_orderNm'); ?>"/>
				<br>
				<br>
				交易結束返回網址&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_returnUrl" value="<?php echo get_option('plugin_google_map_ecpay_returnUrl'); ?>"/>
				<br>
				<br>
				接收綠界回應網址&nbsp;&nbsp;:
				<input type="text" name="plugin_google_map_ecpay_responceUrl" value="<?php echo get_option('plugin_google_map_ecpay_responceUrl'); ?>"/>
				<br>
				<br>
				<p class="submit">
					<input type="submit" class="button-primary" value="儲存" />
				</p>
			</form>
		</div>
	</div>
	<div id="tab-03">
		<div class="wrap">
			<h2>表單樣式設定</h2>
			<br><br>
			<!-- option.php是固定的,wordpress會自動記錄所有設定值 -->
			<form method="post" action="options.php">
				<?php settings_fields('google_map_pay_formstyle-group'); ?>
				<!-- 要先宣告這句下面才有辦法使用上面註冊的變數 -->
				表單文字顏色&nbsp;&nbsp;:
				<input type="text" id="plugin_google_map_formstyle-fontcolor" name="plugin_google_map_formstyle-fontcolor" value="<?php echo get_option('plugin_google_map_formstyle-fontcolor'); ?>"/>
				<br>
				<br>
				表單背景顏色&nbsp;&nbsp;:
				<input type="text" id="plugin_google_map_formstyle-backgroundcolor" name="plugin_google_map_formstyle-backgroundcolor" value="<?php echo get_option('plugin_google_map_formstyle-backgroundcolor'); ?>"/>
				<style>
					#plugin_google_map_formstyle-fontcolor {
						margin:0;
						padding:0;
						border:2px #ccc solid;
						width:150px;
						height:20px;
						border-right:40px solid #<?php echo get_option('plugin_google_map_formstyle-fontcolor'); ?>;
						line-height:20px;
					}
					#plugin_google_map_formstyle-backgroundcolor{
						margin:0;
						padding:0;
						border:2px #ccc solid;
						width:150px;
						height:20px;
						border-right:40px solid #<?php echo get_option('plugin_google_map_formstyle-backgroundcolor'); ?>;
						line-height:20px;
					}
				</style>
				<script>
					(function(jQuery) {
						var $ = jQuery.noConflict();
						jQuery('#plugin_google_map_formstyle-fontcolor').colpick({
							layout:'hex',
							submit:0,
							colorScheme:'dark',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery(el).css('border-color','#'+hex);
								if(!bySetColor) $(el).val(hex);
							}
						}).keyup(function(){
							jQuery(this).colpickSetColor(this.value);
						});

						jQuery('#plugin_google_map_formstyle-backgroundcolor').colpick({
							layout:'hex',
							submit:0,
							colorScheme:'dark',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery(el).css('border-color','#'+hex);
								if(!bySetColor) $(el).val(hex);
							}
						}).keyup(function(){
							jQuery(this).colpickSetColor(this.value);
						});
					})(jQuery);
				</script>
				<br>
				<br>
				<p class="submit">
					<input type="submit" class="button-primary" value="儲存" />
				</p>
			</form>
		</div>
		<?php
		?>
	</div>
</div>
<script>
	(function($) {
		$(function(){
			$('#tabs').tabs();
		});
	})(jQuery);
</script>
<?php
							 }
?>