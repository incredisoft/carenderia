<link rel="stylesheet" type="text/css" href="css/slideshow.css"/>
<script type="text/javascript">
	$(document).ready(function(){
		slideShow();
	});
	
	function slideShow() {
		//Set the opacity of all images to 0
		$('#gallery a').css({opacity: 0.0});
		
		//Get the first image and display it (set it to full opacity)
		$('#gallery a:first').css({opacity: 1.0});
		
		//Set the caption background to semi-transparent
		$('#gallery .caption').css({opacity: 0.7});

		//Resize the width of the caption according to the image width
		$('#gallery .caption').css({width: $('#gallery a').find('img').css('width')});
		
		//Get the caption of the first image from REL attribute and display it
		$('#gallery .caption_text').html($('#gallery a:first').find('img').attr('rel'))
		.animate({opacity: 0.7}, 400);
		
		//Call the gallery function to run the slideshow, 6000 = change to next image after 6 seconds
		setInterval('gallery()',6000);
	}
	
	function gallery() {
		//if no IMGs have the show class, grab the first image
		var current = ($('#gallery a.show')?  $('#gallery a.show') : $('#gallery a:first'));

		//Get next image, if it reached the end of the slideshow, rotate it back to the first image
		var next = ((current.next().length) ? ((current.next().hasClass('caption'))? $('#gallery a:first') :current.next()) : $('#gallery a:first'));	
		
		//Get next image caption
		var caption = next.find('img').attr('rel');	
		
		//Set the fade in effect for the next image, show class has higher z-index
		next.css({opacity: 0.0})
		.addClass('show')
		.animate({opacity: 1.0}, 1000);

		//Hide the current image
		current.animate({opacity: 0.0}, 1000)
		.removeClass('show');
		
		//Set the opacity to 0 and height to 1px
		$('#gallery .caption').animate({opacity: 0.0}, { queue:false, duration:0 }).animate({height: '1px'}, { queue:true, duration:300 });	
		
		//Animate the caption, opacity to 0.7 and heigth to 100px, a slide up effect
		$('#gallery .caption').animate({opacity: 0.7},100 ).animate({height: '50px'},500 );
		
		//Display the content
		$('#gallery .caption_text').html(caption);
	}
</script>
<div id="body" style="margin-bottom: -50px;">
	<div id="main-content" class="content">
		<table border="0" cellspacing="10px" style="margin-top: 35px;">
			<tr>
				<td class="menu-cell" style="width: 470px; height: 215px; background: #3D3DFF;" colspan="2">
					<div id="gallery">
						<a href="#" class="show"><img src="images/slideshow/1.png" alt="" width="460" height="215" title="" alt="" rel="<h3>Beef with Onions</h3>Beef cuts with soy and onion sauce served with rice and drink."/></a>
						<a href="#"><img src="images/slideshow/2.png" alt="" width="460" height="215" title="" alt="" rel="<h3>Fried Chicken</h3>Juicy inside and perfect crisp in the outside with rice and drink."/></a>
						<a href="#"><img src="images/slideshow/3.png" alt="" width="460" height="215" title="" alt="" rel="<h3>Breaded Pork Chop</h3>Flavorfully breaded pork chop served with rice and drink."/></a>
						<div class="caption"><div class="caption_text"></div></div>
					</div>
					<div class="clear"></div>
				</td>
				<td class="menu-cell" style="width: 235px; height: 215px; background: #993399;">	
					<div style="position:absolute; margin-top:-90px; margin-left:55px;">
						<img src="images/menu/menu.png" style="position:absolute;"/>
						<a href="index.php?page=order"><div class="menu-heading" style="margin-left:-55px; margin-top:90px";>Order</div></a>
					</div>
				</td>
				<td class="menu-cell" style="width: 235px; height: 215px; background: #47FF47;">
					<div style="position:absolute; margin-top:-90px; margin-left:10px;">
						<img src="images/menu/services.png" style="position:absolute;"/>
						<a href="#"><div class="menu-heading" style=" margin-top:90px; margin-left:-10px;">Services</div></a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="menu-cell" style="width: 235px; height: 215px; background: #FF850A;">
					<div style="position:absolute;margin-top:-90px; margin-left:55px;">
						<img src="images/menu/downloads.png" style="position:absolute;"/>
						<a href="#"><div class="menu-heading" style="margin-top:90px; margin-left:-55px;">Downloads</div></a>
					</div>
				</td>
				<td class="menu-cell" style="width: 235px; height: 215px; background: #FFFF47;">
					<div style="position:absolute;margin-top:-90px; margin-left:25px;">
						<img src="images/menu/contact_us.png" style="position:absolute;"/>
						<a href="#"><div class="menu-heading" style="margin-top:90px; margin-left:-25px; width:100px">Contact Us</div></a>
					</div>
				</td>
				<td class="menu-cell" style="width: 470px; height: 215px; background: #FF3D3D;" colspan="2">
					<div style="position:absolute;margin-top:-40px; margin-left:18px;">
						<img src="images/menu/food.png" style="position:absolute;"/>
						<a href="#"><div class="menu-heading" style="margin-top:40px; margin-left:-18px; width:110px;">What's new?</div></a>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>