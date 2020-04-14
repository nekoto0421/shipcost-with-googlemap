<?php
class GMSC_Plugin{
	public static $_instance=NULL;
	function __construct(){
		global $wpdb;

		copy(GMSC_DIR."/SDK/ECPay-template.txt", get_template_directory()."/ECPay-template.php");
		//get_template_directory()取得當前主題路徑
		//記得原頁面開發完要轉成txt
		//phpA 複製給 phpB的話 phpB的內容只會是phpA執行完的的結果(非內容)

		copy(GMSC_DIR."/SDK/ECPay.Payment.Integration.txt", get_template_directory()."/ECPay.Payment.Integration.php");
		//複製綠界SDK到相對路徑下

		if(!defined('ABSPATH'))exit;
        $this->CreateTable();
        //自動新增所需資料表

		wp_enqueue_script( 'jquery-ui-tabs' );	
		add_action('wp_head', array($this, 'AddScripts'));
		add_action('wp_head', array($this, 'AddStyles'));
		add_action('admin_menu', array($this, 'AdminMenu'), 1);
		add_shortcode('GMSC-plugin', array($this, 'RenderShortCode'));

		add_action('wp_ajax_nopriv_sendOrder', array($this, 'sendCustomShipOrder'));
		add_action('wp_ajax_sendOrder', array($this, 'sendCustomShipOrder'));

		add_action('wp_ajax_nopriv_googleMapOrderExec', array($this, 'googleMapOrderExec'));
		add_action('wp_ajax_googleMapOrderExec', array($this, 'googleMapOrderExec'));
	}

	function CreateTable(){
		global $wpdb;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$table_name=$wpdb->prefix.'ship_custom_order';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!=$table_name){
			$sql="CREATE TABLE `{$wpdb->prefix}ship_custom_order` (
				  `orderId` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `szCheckMacValue` varchar(50) DEFAULT NULL,
				  `MerchantTradeNo` varchar(100) DEFAULT NULL,
				  `orderTime` varchar(50) NOT NULL,
				  `orderUserNm` varchar(10) NOT NULL,
				  `orderUserPhone` varchar(10) NOT NULL,
				  `orderPrice` int(11) NOT NULL,
				  `orderFee` int(11) NOT NULL,
				  `beginPos` varchar(100) NOT NULL,
				  `endPos` varchar(100) NOT NULL,
				  `payFlag` varchar(1) DEFAULT NULL,
				  `execFlag` varchar(1) DEFAULT 'F'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			 dbDelta($sql);
			/*end of 有多個table就要寫多個區塊*/
		}
	}

	function AddScripts(){
		wp_register_script('GMSC-colpick_script', GMSC_URL.'/js/colpick.js');
		wp_enqueue_script('GMSC-colpick_script');
		
		wp_register_script('GMSC-intro_script', GMSC_URL.'/js/intro.js');
		wp_enqueue_script('GMSC-intro_script');
		
		wp_register_script('GMSC-plugin_script', GMSC_URL.'/js/GMSC-plugin.js');
		wp_enqueue_script('GMSC-plugin_script');
		
		wp_register_script('GMSC-fontawesome_script', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js');
		wp_enqueue_script('GMSC-fontawesome_script');

		wp_register_script('GMSC-googleapis_script','https://maps.googleapis.com/maps/api/js?key='.get_option('plugin_google_map_api_key').'&libraries=places&callback=initMap');
		wp_enqueue_script('GMSC-googleapis_script');
		wp_localize_script(	
			'GMSC-plugin_script', 
			'GMSC_vars',
			array(
				'imgroot'		=>GMSC_URL.'/images/', 
				'templateroot'		=>GMSC_URL.'/templates/', 
				'ajaxurl'		=>admin_url('admin-ajax.php'),
				'disableDefaultUIflag' => getdisableDefaultUI(),
				'mapStyle' => getmapStyle(),
				'travelMode' => getrouteType(),
				'basePrice' => get_option('plugin_google_map_base_price'),
				'basedistance' => get_option('plugin_google_map_base_distance'),
				'extraPrice' => get_option('plugin_google_map_extra_price'),
				'extradistance' => get_option('plugin_google_map_extra_distance'),
				'formfontcolor' => get_option('plugin_google_map_formstyle-fontcolor'),
				'formbkcolor' => get_option('plugin_google_map_formstyle-backgroundcolor')
			));
	}

	function AddStyles(){
		wp_register_style('GMSC-colpick_style', GMSC_URL.'/css/colpick.css');
		wp_enqueue_style('GMSC-colpick_style');
		
		wp_register_style('GMSC-intro_style', GMSC_URL.'/css/introjs.css');
		wp_enqueue_style('GMSC-intro_style');
		
		wp_register_style('GMSC-plugin_style', GMSC_URL.'/css/GMSC-plugin.css');
		wp_enqueue_style('GMSC-plugin_style');
		
		wp_register_style('GMSC-fontawesome_style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/fontawesome.min.css');
		wp_enqueue_style('GMSC-fontawesome_style');
		
		wp_register_style('GMSC-font-animate_style', GMSC_URL.'/css/animate.css');
		wp_enqueue_style('GMSC-font-animate_style');
	}

	function AdminMenu(){	
		add_menu_page('運費計算模組', __('運費計算模組'), __('manage_options'), 'GMSC-plugin_main', create_function('', 'require_once \''.GMSC_DIR.'/templates/GMSC-plugin_admin.php\';'), 'dashicons-location-alt', 56);

		add_submenu_page('GMSC-plugin_main', __('訂單檢視'), __('訂單檢視'), __('manage_options'), 'GMSC-plugin_order_check',create_function('', 'require_once \''.GMSC_DIR.'/templates/GMSC-plugin_order_check.php\';'));
	}

	public function RenderShortCode($args){
		if(!is_array($args))return;
		switch($args['type']){
			case 'main_page':
				$this->MainPage();
				break;
		}
	}

	function MainPage(){
		include GMSC_DIR.'/templates/mainmap_page.php';
	}

	function googleMapOrderExec(){
		global $wpdb;
		$OrderList=trim($_POST["OrderList"]);
		$exectype=trim($_POST["exectype"]);
		
		if($exectype!=0&&$exectype!=1){
			echo json_encode(array('error'=>'處理失敗'));
			exit();
		}
		if(empty($OrderList)||preg_match("/[^0-9,]+/",$OrderList)){
			echo json_encode(array('error'=>'處理失敗'));
			exit();
		}
		if($exectype==0){
			$sql="UPDATE `{$wpdb->prefix}ship_custom_order` SET `execFlag`= 'T' WHERE `orderId`in(".$OrderList.") AND `execFlag`= 'F'";
		}
		else{
			$sql="UPDATE `{$wpdb->prefix}ship_custom_order` SET `execFlag`= 'F' WHERE `orderId`in(".$OrderList.") AND `execFlag`= 'T'";
		}
		$intReturn=$wpdb->query($sql);
		if($intReturn==0){
			echo json_encode(array('error'=>'處理失敗'));
			exit();
		}
		else{
			echo json_encode(array('success'=>'處理成功'));
			exit();
		}
	}
	
	function sendCustomShipOrder(){
		include GMSC_DIR.'/SDK/ECPay.Payment.Integration.php';
		global $wpdb;
		$userNm=trim($_POST["userNm"]);
		$userPhone=trim($_POST["userPhone"]);
		$beginpos=trim($_POST["beginpos"]);
		$endpos=trim($_POST["endpos"]);
		$price=trim($_POST["price"]);

		if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$userNm)) {
			echo ("<script>alert('購買失敗,請輸入中文姓名');</script>");
			exit();
		}

		if(!is_numeric($price)||$price==0){
			echo ("<script>alert('購買失敗,金額錯誤');</script>");
			exit();
		}
		else if(!varifyGMSCphone($userPhone)){
			echo ("<script>alert('購買失敗,電話格式錯誤');</script>");
			exit();
		}
		else if(empty($beginpos)||empty($endpos)){
			echo ("<script>alert('購買失敗,地址錯誤');</script>");
			exit();
		}
		else if(empty($userNm)){
			echo ("<script>alert('購買失敗,姓名錯誤');</script>");
			exit();
		}
		$rent=ceil_dec((int)$price*0.0275,0);
		if($rent<15){
			$rent=15;
		}
		try {
			
			$obj = new ECPay_AllInOne();
			//服務參數
			$obj->ServiceURL  = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";  //服務位置
			//https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5
			$obj->HashKey     = get_option('plugin_google_map_ecpay_hashkey') ;                                          //測試用Hashkey，請自行帶入ECPay提供的HashKey 
			$obj->HashIV      = get_option('plugin_google_map_ecpay_hashIV') ;                                          //測試用HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID  = get_option('plugin_google_map_ecpay_merchantID');                                                    //測試用MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';                                                          //CheckMacValue加密類型，請固定填入1，使用SHA256加密


			//基本參數(請依系統規劃自行調整)
			$MerchantTradeNo = get_option('plugin_google_map_ecpay_orderNm').time() ;
			$MerchantTradeDate = date('Y/m/d H:i:s',strtotime('+8 hours'));
			$obj->Send['ClientBackURL']=get_option('plugin_google_map_ecpay_returnUrl');
			$obj->Send['ReturnURL']         = GMSC_URL.'/includes/ECPay-class_ecpay-resultrecieve.php';
			$obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                           //訂單編號
			$obj->Send['MerchantTradeDate'] = $MerchantTradeDate;                        //交易時間
			$obj->Send['TotalAmount']       = $price+$rent;                        //交易金額
			$obj->Send['TradeDesc']="運費支付";
			$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::ALL ;                  //付款方式:全功能

			//訂單的商品資料
			array_push($obj->Send['Items'], array('Name' => "運費：".$beginpos."->".$endpos, 'Price' => (int)$price,
												  'Currency' => "元", 'Quantity' => 1, 'URL' => ""),
					   array('Name' => "手續費", 'Price' => (int)$rent,'Currency' => "元", 'Quantity' => 1, 'URL' => "")
					  );

			$sql = "INSERT INTO {$wpdb->prefix}ship_custom_order (`MerchantTradeNo`,`orderTime`,`orderUserNm`,`orderUserPhone`,`orderPrice`,`orderFee`,`beginPos`,`endPos`) VALUES('".$MerchantTradeNo."','".$MerchantTradeDate."','".$userNm."','".$userPhone."','".(int)$price."','".(int)$rent."','".$beginpos."','".$endpos."')";
			$intReturn=$wpdb->query($sql);
			if($intReturn==0){
				echo "<script>alertify.log('購買失敗!');</script>";
				exit();
			}
			//產生訂單(auto submit至ECPay)
			$obj->CheckOut();

		} catch (Exception $e) {
			echo $e->getMessage();
		} 
	}
	public static function instance(){
		if(is_null(self::$_instance))self::$_instance=new self();
		return self::$_instance;
	}
}