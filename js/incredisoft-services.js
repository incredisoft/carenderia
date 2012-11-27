var ScriptUtil = function ()
{
	return {
		getPageParameter: function getPageParameter(name)
        {
			name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
			var regexS = "[\\?&]"+name+"=([^&#]*)";
			var regex = new RegExp( regexS );
			var results = regex.exec( window.location.href );
			if( results == null )
					return "";
			else
					return decodeURI(results[1]);
        },
	
		submit: function invoke(path, params, method)
		{
			method = method || "post"; // Set method to post by default, if not specified.			

			// The rest of this code assumes you are not using a library.
			// It can be made less wordy if you use one.
			var form = document.createElement("form");
			form.setAttribute("method", method);
			form.setAttribute("id", "dynamicForm");
			form.setAttribute("action", path);
			
			for(var key in params)
			{
				if(params.hasOwnProperty(key)) {
					var hiddenField = document.createElement("input");
					hiddenField.setAttribute("type", "hidden");
					hiddenField.setAttribute("name", key);
					hiddenField.setAttribute("value", params[key]);
					form.appendChild(hiddenField);
				 }
			}
			
			document.body.appendChild(form);
			form.submit();
		},
		
		post: function post(servicename, methodname, parameters, handler)
		{
			var params ={
				servicename: servicename, 
				methodname: methodname, 
				parameters: parameters
			}
			
			$.post('scripts/service-script.php', params, function(data){
				var response = null;
				try{
					response = $.parseJSON(data);
				}catch(e){
					alert(e.message);
					return;
				}
				
				try{
					handler(response);
				}catch(e){
					alert(e.message);
				}
			});
		}
	}
}();

var ServiceMethod = function(servicename, methodname, parameters)
{
	this.servicename = servicename;
	this.methodname = methodname;
	this.parameters = parameters;
	
	this.invoke = function(handler)
	{
		ScriptUtil.post(this.servicename, this.methodname, this.parameters, handler);
	};
};

ScriptUtil.define = function(servicename, methodname)
{
	return function(){
		return new ServiceMethod(servicename, methodname, arguments);
	};
};

ScriptUtil.iterate = function(list, onitem){
	for(var i=0; i < list.length; i++){
		onitem(i, list[i]);
	}
};