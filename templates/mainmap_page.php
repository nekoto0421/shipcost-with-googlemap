<div id="map"></div>
<!-- Replace the value of the key parameter with your own API key. -->

<div class="jumbotron" style="margin-top:30px; font-size:1.3em; font-weight:bold;">
	<a class="btn btn-large btn-success" href="javascript:void(0);" onclick="javascript:introJs().start();">開啟教學</a>
</div>

<div style="margin-top:30px;">
	<i class="fas fa-map-marker-alt custom-marker-alt animated infinite bounce" style="margin-left:5%;animation-duration: 5s;margin-top:30px;"></i>
	<input data-step="1" data-intro="先輸入運送起始點" id="origin-input" class="controls oriplaceinput" type="text"
		   placeholder="輸入起始地點">
	<i class="fas fa-map-marker-alt custom-marker-alt animated infinite bounce" style="margin-left:5%;animation-duration: 5s;margin-top:30px;"></i>
	<input data-step="2" data-intro="再輸入運送目的地" id="destination-input" class="controls destninput" type="text"
		   placeholder="輸入結束地點">
</div>

<div class="resultdiv"><i class="fas fa-truck animated infinite pulse" style="color:#227700;"></i>&nbsp;&nbsp;估計運送價格&nbsp;:&nbsp;<span data-step="3" data-intro="您此趟運送所需的費用" class="estimateprice shadow">?</span>&nbsp;&nbsp;元</div>

<br>
<br>
<form id="googleMapOrderForm" style="font-family:微軟正黑體;font-size:1.3em;padding:30px;font-weight:bold;" data-step="4" data-intro="若有需求請填寫資料下訂">
	<div>起始地點&nbsp;&nbsp;:&nbsp;&nbsp;<span class="begspan"></span><br><br>結束地點&nbsp;&nbsp;:&nbsp;&nbsp;<span class="endspan"></span></div>
	<br>
	<div>費用&nbsp;&nbsp;:&nbsp;&nbsp;$<span class="pricespan"></span></div>
	<br>	
	<span>姓名&nbsp;&nbsp;:&nbsp;&nbsp;</span><input type="text" id="name" name="name" style="width:300px;display:inline-block;font-size:1.1em;">
	<span class="phonespan">手機&nbsp;&nbsp;:&nbsp;&nbsp;</span><input type="text" id="phone" name="phone" style="width:300px;display:inline-block;font-size:1.1em;">
	<br>
	<button id="sendCustomShipOrder" type="button" style="float:right;">送出訂單</button>
	<br>
</form>
<div id="resultdiv"></div>