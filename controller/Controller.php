<?php

/**
 * Main Controller
 */
abstract class Controller 
{
	function __construct() {
	}
	protected function onInput() 
    {
	}
	protected function onOutput() 
    {
	}
	
	public function isGet($p = null) 
    {
        if($p == null)
        {
    		//return $_SERVER['REQUEST_METHOD'] == "GET";
            return count($_GET) ? true : false;
	    } else {
			return isset($_GET[$p]) ? true :false;
		}
    }
	public function isPost() 
    {
		//return $_SERVER['REQUEST_METHOD'] == "POST";
        return count($_POST) ? true : false;
	}
    
	public function Request()
    {
		$this->onInput();
		$this->onOutput();	
	}
    public function C($string)
    {
        return iconv('utf-8', 'windows-1251', $string);
    }

	protected function Template($filename, $t_vars = array())
    {
		foreach($t_vars as $k => $v)
        {
            $$k = $v;
		}
		ob_start();
			include($filename);
        return ob_get_clean();
		//$content = iconv('windows-1251', 'utf-8', $content);
		//return json_encode(array('content' => $content));
	}
	protected function ajaxTemplate($filename, $t_vars = array())
    {
		foreach($t_vars as $k => $v) 
        {
            $$k = $v;
		}
		ob_start();
			include($filename);
        $content = ob_get_clean();
		return json_encode(array('content' => $content));
		//return $content;
	}
}