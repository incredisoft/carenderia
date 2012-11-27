<?php
class ResourceService
{
	function getResources($location, $type)
	{
		$resources = array();
		if(!is_dir("../".$location))
			throw new Exception("Location not exist.");
			
		if ( $handle = opendir("../".$location) )
		{
			while (($file = readdir($handle)) !== false ) {
				if(preg_match($type, $file)){
					$flocation = "$location/$file";
					array_push($resources, array('filename' => "$file", 'location' => "$location/$file"));
				}
			}
			return $resources;
		}
		
		throw new Exception("Location not exist.");
	}
	
	function getContents($file){
		if(!is_file("../$file"))
			throw new Exception("Not a file");
		
		$content = file_get_contents("../$file");
		return $content;
	}
	
	function parseConfigString($content){
		$array = Array();

        $lines = explode("\n", $content );
        
        foreach( $lines as $line ) {
            $statement = preg_match(
"/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match );

            if( $statement ) {
                $key    = $match[ 'key' ];
                $value    = $match[ 'value' ];
                
                # Remove quote
                if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ) {
                    $value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
                }
                
                $array[ $key ] = $value;
            }
        }
        return $array;
	
	}
}
?>