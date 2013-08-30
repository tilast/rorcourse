<?php foreach($projects as $project) { ?>
	<div class="project js-project" data-project_id="<?=$project['project_id']?>">
		<div class="project__header">
			<div class="project__header__calendar"></div>
			<span class="project__header__title"><?=$project['name']?></span>
			<div class="project__header__right">
				<button class="project__header__right__item project__header__right__edit js-edit-project"></button>
				<button class="project__header__right__item project__header__right__delete js-delete-project"></button>
			</div>
		</div>
		<div class="project__new-task js-new-task">
			<div class="project__new-task__plus"></div>
			<input type="text" placeholder="Input task" class="project__new-task__field js-new-task__value">
			<button class="project__new-task__button js-new-task__add">Add Task</button>
		</div>
		<div class="project__tasks js-tasks">
			<?php foreach($project['tasks'] as $task) { ?>
				<div class="task js-task" data-task_id="<?=$task['task_id']?>">
					<label class="js-checkbox">
						<div class="task__left">
							<input type="checkbox" class="checkbox__elem js-checkbox__elem js-task-check">
							<i class="js-checkbox__img checkbox__img task__checkbox"></i>
						</div>
						<div class="task__center">
							<?=$task['content']?>
						</div>
					</label>
					<div class="task__right">
						<div class="task__right__item">A</div>
						<div class="task__right__item">B</div>
						<div class="task__right__item">C</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>