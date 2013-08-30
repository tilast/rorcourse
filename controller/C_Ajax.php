<?php
class C_Ajax extends Controller
{
	private $main;
	private $content;
    function __construct()
    {
		$this->main = M_Main::Instance();
    }
    public function onInput()
    {
		sleep(2);
		$action = (isset($_POST['action'])) ? $_POST['action'] : null;
		$type = (isset($_POST['type'])) ? $_POST['type'] : null;
		$id = (isset($_POST['id'])) ? $_POST['id'] : null;
		if($action == 'view') {
			switch($type) {
				case 'projects' :
					$this->content = $this->main->getProjects($id, true);
				break;
				case 'tasks' :
					$this->content = $this->main->getTasks($id);
				break;
			}
		} elseif($action == 'add') {
			switch($type) {
				case 'projects' :
					$this->content = $this->main->addProject($_POST['name']);
				break;
				case 'tasks' :
					$this->content = $this->main->addTask($id, $_POST['content']);
				break;
			}
		} elseif($action == 'edit') {
			switch($type) {
				case 'projects' :
					$this->content = $this->main->editProject($id, $_POST['name']);
					break;
				case 'tasks' :
					$this->content = $this->main->editTask($id, $_POST['content']);
					break;
			}
		} elseif($action == 'delete') {
			switch($type) {
				case 'projects' :
					$this->content = $this->main->deleteProject($id);
					break;
				case 'tasks' :
					$this->content = $this->main->deleteTask($id);
					break;
			}
		} elseif($action == 'status') {
			$this->content = $this->main->statusTask($id, $_POST['status']);
		}
    }
    public function onOutput()
    {
		$success = $this->content ? true : false;
		$result = array('success' => $success);
		$result['content'] = $this->content;

		echo json_encode($result, true);
	}
}