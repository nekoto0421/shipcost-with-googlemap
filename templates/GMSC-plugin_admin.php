<div class="wrap">
	<h2>運費價格設定</h2>
	<br><br>
	<!-- option.php是固定的,wordpress會自動記錄所有設定值 -->
	<p style="font-weight:bold;font-size:1.3em;">短代碼(shortcode)</p>
	<p>[GMSC-plugin type='main_page']</p>
	<br>
	<br>
	<form method="post" action="options.php">
		<?php settings_fields('google_map_base-group'); ?>
		<!-- 要先宣告這句下面才有辦法使用上面註冊的變數 -->
		<h3>基礎里程價格設定</h3>
		基礎里程&nbsp;&nbsp;:
		<input type="number" id="plugin_google_map_base_distance" name="plugin_google_map_base_distance" value="<?php echo get_option('plugin_google_map_base_distance'); ?>"/>&nbsp;公里
		&nbsp;&nbsp;基礎里程價格&nbsp;&nbsp;:
		<input type="number" id="plugin_google_map_base_price" name="plugin_google_map_base_price" value="<?php echo get_option('plugin_google_map_base_price'); ?>"/>&nbsp;元
		<br>
		<br>
		<h3>額外里程價格設定</h3>
		額外里程&nbsp;&nbsp;:&nbsp;每
		<input type="number" id="plugin_google_map_extra_distance" name="plugin_google_map_extra_distance" value="<?php echo get_option('plugin_google_map_extra_distance'); ?>"/>&nbsp;公里為一單位
		&nbsp;&nbsp;額外里程每單位價格&nbsp;&nbsp;:
		<input type="number" id="plugin_google_map_extra_price" name="plugin_google_map_extra_price" value="<?php echo get_option('plugin_google_map_extra_price'); ?>"/>&nbsp;元
		<p class="submit">
			<input type="submit" class="button-primary" value="儲存" />
		</p>
	</form>
</div>