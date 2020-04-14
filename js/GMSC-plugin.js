// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.
var map, infoWindow;
var basePrice=GMSC_vars.basePrice;
var baseDistance=GMSC_vars.basedistance;
var extraPrice=GMSC_vars.extraPrice;
var extraDistance=GMSC_vars.extradistance;
jQuery( document ).ready(function() {
    jQuery("#googleMapOrderForm").css("background-color","#"+GMSC_vars.formbkcolor);
	jQuery("#googleMapOrderForm").css("color","#"+GMSC_vars.formfontcolor);
});
function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		zoom: 15,
		disableDefaultUI: GMSC_vars.disableDefaultUIflag,
		styles: JSON.parse(GMSC_vars.mapStyle)
	});
	infoWindow = new google.maps.InfoWindow;

	// Try HTML5 geolocation.
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
			};
			/*
			var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
			var beachMarker = new google.maps.Marker({
				position: {lat: position.coords.latitude, lng: position.coords.longitude},
				map: map,
				icon: image,
				title: "您的位置"
			});
            */
			infoWindow.setPosition(pos);
			infoWindow.setContent('<i class="fas fa-map-marker-alt custom-marker-alt"></i><span style="color:#444;font-size:1.5em;font-weight:bold;">您目前的位置</span>');
			infoWindow.open(map);
			map.setCenter(pos);
		}, function() {
			handleLocationError(true, infoWindow, map.getCenter());
		});
	} else {
		// Browser doesn't support Geolocation
		handleLocationError(false, infoWindow, map.getCenter());
	}
	new AutocompleteDirectionsHandler(map);
}

/**
 * @constructor
 */
function AutocompleteDirectionsHandler(map) {
	this.map = map;
	this.originPlaceId = null;
	this.destinationPlaceId = null;
	this.travelMode = GMSC_vars.travelMode;
	this.directionsService = new google.maps.DirectionsService;
	this.directionsRenderer = new google.maps.DirectionsRenderer;
	this.directionsRenderer.setMap(map);

	var originInput = document.getElementById('origin-input');
	var destinationInput = document.getElementById('destination-input');
	var modeSelector = document.getElementById('mode-selector');

	var originAutocomplete = new google.maps.places.Autocomplete(originInput);
	// Specify just the place data fields that you need.
	originAutocomplete.setFields(['place_id']);

	var destinationAutocomplete =
		new google.maps.places.Autocomplete(destinationInput);
	// Specify just the place data fields that you need.
	destinationAutocomplete.setFields(['place_id']);
	this.setupClickListener('changemode-driving', 'DRIVING');

	this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
	this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');
}

// Sets a listener on a radio button to change the filter type on Places
// Autocomplete.
AutocompleteDirectionsHandler.prototype.setupClickListener = function(
id, mode) {
	var radioButton = document.getElementById(id);
	var me = this;
};

AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(
autocomplete, mode) {
	var me = this;
	autocomplete.bindTo('bounds', this.map);

	autocomplete.addListener('place_changed', function() {
		var place = autocomplete.getPlace();

		if (!place.place_id) {
			window.alert('Please select an option from the dropdown list.');
			return;
		}
		if (mode === 'ORIG') {
			me.originPlaceId = place.place_id;
		} else {
			me.destinationPlaceId = place.place_id;
		}
		me.route();
	});
};

AutocompleteDirectionsHandler.prototype.route = function() {
	if (!this.originPlaceId || !this.destinationPlaceId) {
		return;
	}
	var me = this;

	this.directionsService.route(
		{
			origin: {'placeId': this.originPlaceId},
			destination: {'placeId': this.destinationPlaceId},
			travelMode: this.travelMode
		},
		function(response, status) {
			if (status === 'OK') {
				var routeinfo = response.routes[0].legs[0];
				let distance = routeinfo.distance.text;
				let duration = routeinfo.duration.text;
				let start_address = routeinfo.start_address;
				let end_address = routeinfo.end_address;

				let distance_unit=distance.split(" ")[1];
				let distance_amount=distance.split(" ")[0];
				let duration_amount=0;
				if(distance.split(" ").length==4){
					duration_amount=duration.split(" ")[0]*60+duration.split(" ")[2];
				}
				else{
					if(duration.split(" ")[1]=="小時"){
						duration_amount=duration.split(" ")[0]*60;
					}
					else{
						duration_amount=duration.split(" ")[0];
					}
				}
				if(distance_unit!="公里"){
					distance_amount=distance_amount*0.001;
				}
				else{
					distance_amount=distance_amount;
				}

				console.log("距離:"+distance_amount+"公里");
				console.log("預估時間:"+duration_amount+"分鐘");
				console.log("開始地點:"+start_address);
				console.log("結束地點:"+end_address);
				let html=""
				me.directionsRenderer.setDirections(response);

				let price=0;
				console.log(basePrice);
				console.log(baseDistance);
				console.log(extraPrice);
				console.log(extraDistance);

				if(parseInt(distance_amount)<=baseDistance){
					price=basePrice;
				}
				else{
					price=parseInt(basePrice)+parseInt((parseInt(distance_amount)-baseDistance)/extraDistance*extraPrice);
				}
				jQuery(".estimateprice").html("0");
				jQuery(".estimateprice").animateNumbers(price, true, 1000);
				jQuery(".begspan").text(start_address);
				jQuery(".endspan").text(end_address);
				jQuery(".pricespan").text(price);
			} else {
				window.alert('Directions request failed due to ' + status);
			}
		});
};

(function($) {
	$.fn.animateNumbers = function(stop, commas, duration, ease) {
		return this.each(function() {
			var $this = $(this);
			var start = parseInt($this.text().replace(/,/g, ""));
			commas = (commas === undefined) ? true : commas;
			$({value: start}).animate({value: stop}, {
				duration: duration == undefined ? 1000 : duration,
				easing: ease == undefined ? "swing" : ease,
				step: function() {
					$this.text(Math.floor(this.value));
					if (commas) { $this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")); }
				},
				complete: function() {
					if (parseInt($this.text()) !== stop) {
						$this.text(stop);
						if (commas) { $this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")); }
					}
				}
			});
		});
	};
})(jQuery);

jQuery(document).on('click','#sendCustomShipOrder',function(){
	let data={};
	data={action:'sendOrder'};
	data["userNm"]=jQuery("#name").val();
	data["userPhone"]=jQuery("#phone").val();
	data["beginpos"]=jQuery(".begspan").text();
	data["endpos"]=jQuery(".endspan").text();
	data["price"]=jQuery(".pricespan").text();
	
	if(data["userNm"]==""||data["userPhone"]==""){
		alert("姓名及電話不可留空!");
		return;
	}
	
	if(data["beginpos"]==""||data["endpos"]==""||data["price"]==""){
		alert("請先選擇路徑");
		return;
	}
	$target=jQuery(this);
	jQuery.ajax({
		type:'POST',
		data:data,
		dataType:'text',
		async:false,
		url:GMSC_vars.ajaxurl,
	}).always(function(response){
		//console.log('always', response);
	}).done(function(response){
		console.log('done', response);
		if(response){
			jQuery("#resultdiv").html(response);
		}

	}).fail(function(response, textStatus, errorThrown){
		console.log('fail', response);
	});
})