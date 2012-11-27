<script type="text/javascript">
	var ctr = 0;
	var sendTo = "evalle012006@gmail.com";
	
	$(document).ready(function(){
		$("#order-options-div").hide();
		$("#order-form-div").hide();
		
		loadMenuImages('beef');
		
		$("#btnProceed").click(function(e){
			e.preventDefault();
			if( $("#orders").val() ){
				$("#order-form-div").dialog({
					autoOpen: false,
					resizable: false,
					stack: true,
					minWidth: 500,
					minHeight: 250,
					zIndex: 9999,
					modal: true,
					open: function () { 
						$("#order-form").validate({
							debug: false,
							rules: {
								name : "required",
								contactno : "required",
								email : { required: true, email: true },
								address: "required"
							}
						}).resetForm(); 
					}, 
					buttons: [  {
									text: "Ok",
									click: function(){
										if (!$("#order-form").valid()){
											return false; 
										}else{
											var orders = $("#orders").val().split("|");
											var data = '';
											$.each( orders, function(key, val){
												if( val ){
													data = data + val + "<br>";
												}
											});
											
											data = data + "Subtotal:  " + $("#subtotal").text() + "<br>" + "Delivery Charge: 40.00" + "<br>" + "Total: " + $("#total").text() + "<br>";
											
											alert(data);
											
											var msg = "<html>" +
														"<head><title>Carenderia Reservation</title></head>" +
														"<body>"+ data +"</body>" +
													  "</html>"
											
											var send = ScriptUtil.define('services/common/EmailService.php', 'send');
											send( sendTo, msg, $("#email").val() ).invoke(function(response){
												if( response.isError ){
													alert(response.message);
													return;
												}
												
												alert("Success...");
											});
											
											
											$("#order-form-div").dialog('close');
											return false;
										}
									}
								},
								{
									text: "Cancel",
									click: function(){
										$("#order-form-div").dialog('close');
										return false;
									}
								}
					]
				});
				
				$("#order-form-div").dialog('open');
			}else{
				alert("Please order at least 1 food.");
			}
			
			return false;
		});
	});
	
	function loadMenuImages( menuType ){
		var getMenus = ScriptUtil.define('services/carenderia/MenuService.php', 'getMenus');
		getMenus('images/menu/'+ menuType).invoke(function(response){
			if(response.isError){
				alert(response.message);
				return;
			}
			
			var resources = response.data;
			$(".menu-div").empty();
			$(".menu-div").attr("id", menuType);
			$(".menu-div").append("<table id='menu-table' cellspacing='2px' border='0' cellpadding='0'></table>");
			for(var i=0; i < resources.length; i++){
				var file = resources[i];
				if( i % 3 == 0 ){
					$("#menu-table").append("<tr><td height='180px' width='180px'><img src='"+ file.location +"' style='height:145px;width:180px;' onclick='popupOrderOptions(\""+ file.name + "|" + file.location + "|" + file.description +"\")'>"+file.name.toUpperCase()+"</td</tr>");
				}else{
					$("#menu-table tr:last").append("<td height='180px' width='180px'><img src='"+ file.location +"' style='height:145px;width:180px;' onclick='popupOrderOptions(\""+ file.name + "|" + file.location + "|" + file.description +"\")'>"+file.name.toUpperCase()+"</td>");
				}
			}
		});
	}
	
	function popupOrderOptions( order ){
		var ordr = order.split("|");
		
		$("#order-option-content").empty();
		$("#order-option-content").append(
			"<img id='order-img' src='"+ ordr[1] +"' width='200px'/><br>" +
			"<div id='order-desc'><i>"+ ordr[2] + "</i><br><br><span style='font-size: 15px; font-weight: bold;'>" + ordr[0].toUpperCase() +"</span></div><br>" + 
			"<div id='order-qty-div'>" +
				"Quantity: <input type='text' id='order-qty' name='order-qty' value='1' size='5'/>&nbsp;&nbsp;" +
				"<button onclick='minusQty()'>&nbsp;&nbsp;-&nbsp;&nbsp;</button>" +
				"<button onclick='addQty()'>&nbsp;&nbsp;+&nbsp;&nbsp;</button>" +
			"</div>"
		);
		
		$("#order-options-div").dialog({
			autoOpen: false,
			resizable: false,
			stack: true,
			minWidth: 500,
			minHeight: 350,
			zIndex: 9999,
			modal: true,
			buttons: [  {
							text: "Ok",
							click: function(){
								var foodtype = $("input[name='foodtype']:checked").val().split("|");
								var subtotal = (parseFloat(foodtype[1]) * parseFloat($("#order-qty").val())).toFixed(2);
								$("#bill-div").append(
									"<div id='order-"+ ctr +"'>" +  
										"<a href='#' onclick='deleteOrder(\""+ 'order-' + ctr +"\", \""+ ordr[0].toUpperCase() + " x " + $("#order-qty").val() + " " + foodtype[0] + "*" + subtotal + "|" +"\")'>" +
										"<img src='images/close.png' style='margin-left:5px; margin-top:5px;'/></a>&nbsp;&nbsp;&nbsp;<b>" + 
										ordr[0].toUpperCase() + "</b> x " + $("#order-qty").val() +"<span style='position: absolute; right: 20px;'>Php " + subtotal  +"</span><br>" +
										"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>" + foodtype[0] + "</i>" +
									"</div>"
								);
								$("#orders").val( $("#orders").val() + ordr[0].toUpperCase() + " x " + $("#order-qty").val() + " " + foodtype[0] + "|" );
								
								$("#subtotal").text((parseFloat($("#subtotal").text()) + parseFloat(subtotal)).toFixed(2));
								if( $("#total").text() == "0.00" ){
									$("#total").text((parseFloat($("#total").text()) + parseFloat(subtotal) + 40).toFixed(2));
								}else{
									$("#total").text((parseFloat($("#total").text()) + parseFloat(subtotal)).toFixed(2));
								}
								ctr++;
								$("#order-options-div").dialog('close');
								return false;
							}
						},
						{
							text: "Cancel",
							click: function(){
								$("#order-options-div").dialog('close');
								return false;
							}
						}
			]
		});
		
		$("#order-options-div").dialog('open');
	}
	
	function minusQty(){
		if(!$("#order-qty").val() || $("#order-qty").val() <= "1"){
			$("#order-qty").val("1");
		}else{
			$("#order-qty").val( parseInt($("#order-qty").val()) - 1 );
		}
	}
	
	function addQty(){
		if(!$("#order-qty").val()){
			$("#order-qty").val("1");
		}else{
			$("#order-qty").val( parseInt($("#order-qty").val()) + 1 );
		}
	}
	
	
	function deleteOrder( orderid, orderdesc ){
		var popupDialog = $("<div>Are you sure you want to remove this?</div>");
		popupDialog.dialog({
			autoOpen: false,
			resizable: false,
			stack: true,
			minWidth: 300,
			minHeight: 100,
			zIndex: 9999,
			modal: true,
			buttons: [  {
							text: "Yes",
							click: function(){
								var ordr = orderdesc.split("*")
								$("#subtotal").text( (parseFloat($("#subtotal").text()) - parseFloat(ordr[1])).toFixed(2));
								$("#total").text( (parseFloat($("#total").text()) - parseFloat(ordr[1])).toFixed(2));
								$("#orders").val($("#orders").val().replace( ordr[0], "" ));
								$( "#"+orderid ).remove();
								popupDialog.dialog('close');
								return false;
							}
						},
						{
							text: "No",
							click: function(){
								popupDialog.dialog('close');
								return false;
							}
						}
			]
		});
		
		popupDialog.dialog('open');
	}
	
	function addCommas(nStr){
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	
</script>
<div id="body">
	<div id="main-content" class="content">
		<div id="left_order" class="order-inner-div">
			<div class="order-title">Select your food.</div>
			<div id="order-menu-div">
				<ul class="menu-list">
					<b>
						<li><a href="#" onclick="loadMenuImages('beef')">Beef</a></li>
						<li><a href="#" onclick="loadMenuImages('pork')">Pork</a></li>
						<li><a href="#" onclick="loadMenuImages('chicken')">Chicken</a></li>
						<li><a href="#" onclick="loadMenuImages('extra')">Extra</a></li>
					</b>
				</ul>
				<div id="menu">
					<div class="menu-container">
						<div class="menu-div"></div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="right_order" class="order-inner-div">
			<div class="order-title">Please review your orders.</div>
			<div id="bill-div"></div>
			<div id="total-div">
				<hr>
				<span style="margin-left: 10px;">Subtotal</span><span style="position: absolute; right:20px;" id="subtotal">0.00</span><br>
				<span style="margin-left: 10px;">Delivery Charge</span><span style="position: absolute; right:20px;">40.00</span><br>
				<span style="margin-left: 10px;">Total</span><span style="position: absolute; right:20px;" id="total">0.00</span><br>
			</div>
			<div id="button-div"><input id="btnProceed" type="submit" value="Proceed"></div>
		</div>
		<input type="hidden" id="orders" name="orders" value="" />
	</div>
</div>

<div id="order-options-div" title="Order Options">
	<div class='inner-options-div'>
		<div id="order-option-content"></div>
		<div id="order-type-div">
			<span>Food Type</span><br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="alacarte" name="foodtype" checked value="Ala Carte|25.00">&nbsp;&nbsp;Ala Carte - Php 25.00</input><br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="cm1" name="foodtype" value="CM1( 1 viand 1 rice 1 drink )|39.00">&nbsp;&nbsp;CM1( 1 viand 1 rice 1 drink ) - Php 39.00</input><br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="cm2" name="foodtype" value="CM2( 1 viand 1 dessert 1 rice 1 drink )|49.00">&nbsp;&nbsp;CM2( 1 viand 1 dessert 1 rice 1 drink ) - Php 49.00</input><br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="cm3" name="foodtype" value="CM3 ( 1 viand 1 noodles / veggies 1 rice 1 drink )|59.00">&nbsp;&nbsp;CM3 ( 1 viand 1 noodles / veggies 1 rice 1 drink ) - Php 59.00</input><br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="cm4" name="foodtype" value="CM4 ( 2 viands 1 rice 1 drink )|69.00">&nbsp;&nbsp;CM4 ( 2 viands 1 rice 1 drink ) - Php 69.00</input><br>
		</div>
	</div>
</div>

<div id="order-form-div">
	Fields marked with an (*) are REQUIRED. <br>
	<form id="order-form" action="" method="post">
		<table border="0">
			<tr>
				<td style="text-align: right;">Name * :</td><td><input type="text" id="name" name="name" size="25"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Contact No. * :</td><td><input type="text" id="contactno" name="contactno" size="25"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Email Address * :</td><td><input type="text" id="email" name="email" size="25"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Address * :</td><td><input type="text" id="address" name="address" size="25"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Landmarks :</td><td><input type="text" id="landmark" name="landmark" size="25"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Remarks :</td><td><textarea id="remarks" name="remarks" style="width: 181px; height: 30px;"></textarea></td>
			</tr>
		</table>
	</form>
</div>