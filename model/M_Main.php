<?php

class M_Main
{
	private static $instance;
	public static function Instance()
	{
		if(self::$instance == null)
			self::$instance = new M_Main();

		return self::$instance;
	}
	private $db;
	function __construct()
	{
		$this->db = M_DB::Instance();
	}

	public function getProjects($project_id = null, $select_tasks = false) {
		$where = ($project_id) ? 'WHERE project_id=' . sprintf("%d", $project_id) : '';
		//$join = ($select_tasks) ? "INNER JOIN tasks WHERE projects.project_id=tasks.project_id" : '';
		$query = "SELECT * FROM projects $where";

		$projects = $this->db->Select($query);

		if($select_tasks) {
			foreach($projects as $key => $project) {
				$projects[$key]['tasks'] = $this->getTasks(null, $project['project_id']);
			}
		}

		/*if($select_tasks) {
			foreach($projects as $project) {
				if(!isset($result[$project['project_id']])) {
					$result[$project['project_id']] = array(
						'project_id' => $project['project_id'],
						'name' => $project['name']
					);
				}
				$result[$project['project_id']]['tasks'][] = array(
					'task_id' => $project['task_id'],
					'content' => $project['content'],
					'priority' => $project['priority'],
				);
			}
		} else {
			$result = $projects;
		}*/

		return $projects;
	}

	public function getTasks($task_id = null, $project_id = null) {
		if($task_id) {
			$where = 'WHERE task_id=' . sprintf("%d", $task_id);
		} elseif($project_id) {
			$where = 'WHERE project_id=' . sprintf("%d", $project_id);
		} else {
			$where = '';
		}

		return $this->db->Select("SELECT * FROM tasks $where");
	}

	public function addProject($name) {
		if(!trim($name)) {
			return false;
		}

		$object = array(
			'name' => trim($name)
		);

		return $this->db->Insert("projects", $object);
	}

	public function addTask($project_id, $content, $priority = null) {
		if(!trim($content) || !trim($project_id)) {
			return false;
		}

		$object = array(
			'project_id' => trim($project_id),
			'content' => htmlspecialchars(trim($content)),
			'priority' => trim($priority)
		);

		return $this->db->Insert("tasks", $object);
	}

	public function editProject($project_id, $name) {
		$object = array(
			'name' => trim($name)
		);
		$where = 'project_id=' . sprintf("%d", $project_id);

		return $this->db->Update('projects', $object, $where);
	}
	public function editTask($task_id, $content, $priority = null) {
		$object = array(
			'content' => htmlspecialchars(trim($content)),
			'priority' => $priority
		);
		$where = 'task_id=' . sprintf("%d", $task_id);

		return $this->db->Update('tasks', $object, $where);
	}
	public function statusTask($task_id, $status) {
		$object = array(
			'status' => !!$status
		);

		$where = "task_id="  . sprintf("%d", $task_id);

		return $this->db->Update('tasks', $object, $where);
	}

	public function deleteProject($project_id) {
		return $this->db->Delete('projects', sprintf("project_id=%d", $project_id)) && $this->db->Delete('tasks', sprintf("project_id=%d", $project_id));
	}
	public function deleteTask($task_id) {
		return $this->db->Delete('tasks', sprintf("task_id=%d", $task_id));
	}

	/**
	 * bad function
	 * only for test task
	 * @param $num
	 * @return array
	 */
	public function customQuery($num = null) {
		$result = array();

		$queries[2] = "SELECT COUNT('task_id')
					AS count, tasks.project_id, projects.name
					FROM tasks
					LEFT JOIN projects
					ON projects.project_id=tasks.project_id
					GROUP BY projects.project_id
					ORDER BY count
					DESC";

		$queries[3] = "SELECT COUNT('task_id')
					AS count, tasks.project_id, projects.name
					FROM tasks
					LEFT JOIN projects
					ON projects.project_id=tasks.project_id
					GROUP BY projects.name
					ORDER BY projects.name";

		$queries[4] = "SELECT *
		 			FROM tasks
		 			INNER JOIN projects
		 			WHERE projects.name
		 			LIKE 'n%'
		 			AND projects.project_id=tasks.project_id";

		$queries[5] = "SELECT COUNT('project_id')
					AS count, tasks.project_id, projects.name
					FROM tasks
					JOIN projects
					ON projects.project_id=tasks.project_id
					WHERE projects.name
					LIKE '%a%'
					GROUP BY projects.project_id";

		$queries[6] = "SELECT *
					FROM tasks
					JOIN tasks
					AS tasks2
					ON tasks.content=tasks2.content
					WHERE tasks.task_id <> tasks2.task_id
					ORDER BY content";

		$queries[8] = "SELECT COUNT(*)
					AS count, projects.project_id, projects.name
					FROM tasks
					JOIN projects
					ON projects.project_id=tasks.project_id
					WHERE tasks.status=1
					GROUP BY projects.project_id";

		if($num == null) {
			foreach($queries as  $key => $query) {
				$result[$key] = array(
					"result" => $this->db->Select($query),
					"query" => $query
				);
			}
		} elseif(isset($queries[$num])) {
			$result[$num] = array(
				"result" => $this->db->Select($queries[$num]),
				"query" => $queries[$num]
			);
		}

		return $result;
	}
}