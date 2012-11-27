<?php
	class ClientRequest
	{
		public static function GetRequestModel($name)
		{
			return new RequestModel($name);
		}		
		
		public static function CreateXmlResponseMessage($error, $message, $data)
		{
			$response = "<response>";
			$response .= "<error><![CDATA[$error]]></error>";
			$response .= "<message><![CDATA[$message]]></message>";
			$response .= "<data>";
			
			if( $data != null )
			{
				foreach (array_keys($data) as $key)
				{
					$value = $data[$key];
					$response .="<$key><![CDATA[$value]]></$key>";
				}
			}
			
			$response .= "</data>";
			$response .= "</response>";
			return $response;
		}
	}
	
	class RequestModel
	{
		var $name;
		
		public function __construct($name)
		{
			$this->name = $name;
		}
		
		public function get($propertyName)
		{
			if($this->name == null)
				return isset($_GET[$propertyName]) ? $_GET[$propertyName] : null;
			else 
				return isset($_GET[$this->name.'.'.$propertyName]) ? $_GET[$this->name.'.'.$propertyName] : null;
		}
		
		public function post($propertyName)
		{
			if($this->name == null)
				return isset($_POST[$propertyName]) ? $_POST[$propertyName] : null;
			else 
				return isset($_POST[$this->name.'.'.$propertyName]) ? $_POST[$this->name.'.'.$propertyName] : null;
		}
		
		public function file($propertyName)
		{
			if($this->name == null)
				return isset($_FILES[$propertyName]) ? $_FILES[$propertyName] : null;
			else 
				return isset($_FILES[$this->name.'.'.$propertyName]) ? $_FILES[$this->name.'.'.$propertyName] : null;
		}
	}
?>