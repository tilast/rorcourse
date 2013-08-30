<?php

class C_Base extends Controller
{
    protected $db;
    protected $main;
	protected $content;

    function __construct()
    {
        $this->main = M_Main::Instance();
    }
    public function onInput()
    {
		$this->main->badDebugFunction();
        $this->content = $this->main->getProjects(null, true);
    }
    public function onOutput()
    {
		$global_header = $this->Template('view/global_header.php');
        $index = $this->Template('view/index.php', array("global_header" => $global_header, 'projects' => $this->content));
		echo $index;
    }
}