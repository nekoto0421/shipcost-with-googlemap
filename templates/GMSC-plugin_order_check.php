<?php
	wp_register_script('pm-datatable_script', GMSC_URL.'/js/datatables.min.js');
	wp_enqueue_script('pm-datatable_script');
	wp_register_style('pm-datatable_style', GMSC_URL.'/css/datatables.min.css');
	wp_enqueue_style('pm-datatable_style');
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->prefix}ship_custom_order";
	$dbResult = $wpdb->get_results($sql);
?>
<div class="wrap">
	<h2>訂單檢視</h2>
	<br>
	<a class="button button-primary OrderExec" value="0">標示為已處理</a>&nbsp;&nbsp;<a class="button button-primary OrderExec" value="1">標示為未處理</a>
	<br>
	<br>
	<table  id="orderTable" class="cell-border order-column hover" style="width:80%;text-align:center;border:1px solid #DDD;">
		<thead>
			<tr>
				<th class="sorting_disabled">動作</th>
				<th>訂購者名稱</th>
				<th>訂購者手機</th>
				<th>起始點</th>
				<th>結束點</th>
				<th>訂購價格</th>
				<th>訂購時間</th>
				<th>是否付款</th>
				<th>是否處理</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if($dbResult){
					foreach($dbResult as $value){
						$html="<tr><td>";
						$html.="<label for='".$value->orderId."'><input name='OrderList' type='checkbox' id='".$value->orderId."' value='".$value->orderId."'></label></td>";
						$html.="<td>".$value->orderUserNm."</td>";
						$html.="<td>".$value->orderUserPhone."</td>";
						$html.="<td>".$value->beginPos."</td>";
						$html.="<td>".$value->endPos."</td>";
						$html.="<td>".$value->orderPrice."</td>";
						$html.="<td>".$value->orderTime."</td>";
						if($value->payFlag=='T'){
							$html.="<td>已付款</td>";
						}
						else{
                            $html.="<td>未付款</td>";
						}
						if($value->execFlag=='T'){
							$html.="<td>已處理</td>";
						}
						else{
                            $html.="<td>未處理</td>";
						}
						$html.="</tr>";
						echo $html;
					}
				}	
			?>
		</tbody>
	</table>	
</div>
<script>
	jQuery( document ).ready(function() {
		jQuery('#orderTable').DataTable(opt);
	});
	var opt={"oLanguage":{"sProcessing":"資料處理中...",
			  "sLengthMenu":"顯示 _MENU_ 項結果",
			  "sZeroRecords":"沒有匹配結果",
			  "sInfo":"顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
			  "sInfoEmpty":"顯示第 0 至 0 項結果，共 0 項",
			  "sInfoFiltered":"(從 _MAX_ 項結果過濾)",
			  "sSearch":"搜尋:",
			  "sScrollX": "100%",
			  "oPaginate":{"sFirst":"首頁",
						   "sPrevious":"上頁",
						   "sNext":"下頁",
						   "sLast":"尾頁"}
			 },
		 "order": [[ 6, "desc" ]],
		 "aoColumnDefs": [
			 {
				 "bSortable": false,
				 "aTargets": ["sorting_disabled"]
			 }
	      ]
	};

	function GetCheckedValue(checkBoxName)
	{   
		return jQuery('input[name=' + checkBoxName + ']:checked').map(function ()
																	  {
			return jQuery(this).val();
		})
			.get().join(',');
	}

	jQuery(document).on('click','.OrderExec',function(){
		let data={};
		data={action:'googleMapOrderExec'};
		data["exectype"]=jQuery(this).attr("value");
		let OrderList=GetCheckedValue("OrderList");
		if(OrderList==''){
			alert("請選擇要處理的資料");
			return;
		}
		data["OrderList"]=OrderList;
		jQuery.ajax({
			type:'POST',
			data:data,
			dataType:'json',
			url:"<?php echo admin_url('admin-ajax.php');?>",
		}).always(function(response){
			//console.log('always', response);

		}).done(function(response){
			console.log('done', response);
			jQuery("#waiticon").css("display","none");  
			if(response){
				if(response.error){
					alert(response.error);
				}else if(response.success){
					alert(response.success);
					window.location.reload();
				}
			}

		}).fail(function(response, textStatus, errorThrown){
			console.log('fail', response);

		});
	})
</script>