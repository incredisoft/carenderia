<?php
include('data-util.php');

function import($scriptname)
{
	if(file_exists ("../$scriptname"))
		eval("include_once('../".$scriptname."');");
	else
		throw new Exception("Service not found $scriptname");
}

function _getInstanceName($name)
{
	$arr = explode('.', $name.'');
	$arrname = explode('/', $arr[0]);
	return $arrname[count($arrname) - 1];
}

try{
	$serviceName = $_POST['servicename'];
	$methodName = $_POST['methodname'];
	$parameters = array();

	if(isset($_POST['parameters']))
		$parameters = $_POST['parameters'];
	
	import($serviceName);
	$classname = _getInstanceName($serviceName);

	$serviceObject = new $classname;
	$result = call_user_func_array(array( $serviceObject, $methodName ), $parameters);
	DbManager::getInstance()->commit();
	echo json_encode(array('isError'=> false, 'data'=> $result, 'message' => 'success'));
	flush();
}catch(Exception $e){
	DbManager::getInstance()->rollback();
	echo json_encode(array('isError'=> true, 'message'=> $e->getMessage()));
	flush();
}
?>