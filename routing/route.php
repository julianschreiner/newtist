<?php

/**
 * Routing 
 * 
 */
class Route
{
	public $matches = [];

	function __construct()
	{
		# code...
	}

	public static function parse($regex, $callback){
		$regex = str_replace('/', '\/', $regex);
    	$is_match = preg_match('/^' . ($regex) . '$/', $_SERVER['REQUEST_URI'], $matches, PREG_OFFSET_CAPTURE);
 		
 		$match = $matches[0][0] ?? '';

    	if ($is_match) { 
    		$callback(true, $matches); 
    	}
	}
}