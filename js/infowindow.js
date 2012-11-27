function infoWindow( infoTitle, infoMessage, target ){
	var $dialog = $('<div id="info-div" title="'+ infoTitle +'">'+
						'<img src="images/info.png" width="32px" height="32px"/>' +
						'<div>' + infoMessage + '</div>' +
						'<span><a href="index.php">CARenderia Atbp.</a></span>' +
					'</div>'
	);
	
	
	$dialog.dialog({
		resizable: false,
		stack: true,
		minWidth: 350,
		minHeight: 200,
		buttons: [  {
						text: "Ok",
						click: function(){
							if( target ){
								$("#" + target).focus();
								$("#" + target).addClass("invalid-field");
							}
							$dialog.remove();
							return true;
						}
					}
		]
	});
	
	return $dialog;
}