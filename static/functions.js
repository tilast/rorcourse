/**
 * Created by tilastman on 24.08.13.
 */

// include templates
var taskTemplate = getTemplate("task");
var projectTemplate = getTemplate("project");
var projectTitleInput = getTemplate("titleInput");
var noTasks = getTemplate("noTasks");
var addProject = getTemplate("addProject");
var taskContentEdit = getTemplate("taskContentEdit");
var noProjects = getTemplate("noProjects");
var bigLoader = getTemplate("bigLoader");
var smallLoader = getTemplate("smallLoader");

$(document).on("ready", function() {
    // load main content
    var jsonContent = connectServer({
        action: 'view',
        type: 'projects'
    }, function() {
        $(".js-projects").append(bigLoader);
    }, function() {
        $(".js-projects").find(".js-big-loader").remove();
    });

    var startContent = '';
    // if server gave us data
    if(jsonContent.success) {
        // loop all projects
        for(var i = 0, length = jsonContent.content.length; i < length; i++) {
            // get a project
            var project = jsonContent.content[i];
            // get the project template
            var projectTmp = projectTemplate;
            // ... and give them data
            projectTmp = projectTmp
                .replace("[%--project_id--%]", project['project_id'])
                .replace("[%--project_name--%]", project['name']);
            var tasks = '';
            // then take tasks
            for(var u = 0, tLength = project['tasks'].length; u < tLength; u++) {
                var taskTmp = taskTemplate;
                var task = project['tasks'][u];
                tasks += taskTmp
                    .replace("[%--task_id--%]", task['task_id'])
                    .replace("[%--task_content--%]", task['content'])
                    .replace("[%--task_checked--%]", task['status'] ? 'checked' : '')
                    .replace("[%--task_checked_style--%]", task['status'] ? 'checkbox__img__checked' : '');
            }

            // if we don't have tasks for this project - take noTasks template
            if(!tasks) {
                tasks = noTasks;
            }

            projectTmp = projectTmp.replace("[%--project_tasks--%]", tasks);
            startContent += projectTmp;
        }
    }
    // if we don't have projects
    if(!jsonContent || !jsonContent.content.length) {
        $(".js-projects").append(noProjects);
    }

    // load start content on page and delegate events
    $(".js-projects")
        .append(startContent)
        .on("click", ".js-checkbox", function() {
            var task_id = null;
            var result = null;
            if($(this).find(".js-checkbox__elem").is(":checked")) {
                task_id = parseInt($(this).parents(".js-task").data("task_id"));
                result = connectServer({
                    action: 'status',
                    id: task_id,
                    status: 1
                });
                if(result.success) {
                    $(this).find(".js-checkbox__img").addClass("checkbox__img__checked");
                }
            } else {
                task_id = parseInt($(this).parents(".js-task").data("task_id"));
                result = connectServer({
                    action: 'status',
                    id: task_id,
                    status: 0
                });
                if(result.success) {
                    $(this).find(".js-checkbox__img").removeClass("checkbox__img__checked");
                }
            }
        })
        .on("click", ".js-edit-project, .js-project-title", function() {
            if(!$(this).parents(".js-project").find(".js-project-title__edit").val()) {
                var title = $(this).parents(".js-project").find(".js-project-title");
                title.css("display", "none");
                var $value = title.text();
                var titleTmp = projectTitleInput;
                title.after(titleTmp.replace("[%--title--%]", $value));
                $(this).parents('.js-project').find(".js-project-title__edit").focus();
            } else {
                $(this).parents(".js-project").find(".js-project-title").css("display", "block");
                $(this).parents(".js-project").find(".js-project-title__edit__form").remove();
            }
        })
        .on("blur", ".js-project-title__edit", function() {
            $(this).parents(".js-project").find(".js-project-title").css("display", "block");
            $(this).parents(".js-project").find(".js-project-title__edit__form").remove();
        })
        .on("submit", ".js-project-title__edit__form", function(e) {
            e = e || window.event;

            e.preventDefault();
            if($(this).find(".js-project-title__edit").val() != $(this).find(".js-project-title")) {
                var $project_id = parseInt($(this).parents(".js-project").data("project_id"));
                var $value = $(this).find(".js-project-title__edit").val();

                var result = connectServer({
                    action: 'edit',
                    type: 'projects',
                    id: $project_id,
                    name: $value
                });

                if(result.success) {
                    $(this).parents(".js-project").find(".js-project-title").css("display", "block").html($value);
                    $(this).remove();
                }
            }

            return false;
        })
        .on("click", ".js-delete-project", function() {
            var really = confirm("Do you really want to delete project?");
            if(really) {
                var result = connectServer({
                    action: 'delete',
                    type: 'projects',
                    id: $(this).parents(".js-project").data("project_id")
                });
                if(result.success) {
                    $(this).parents(".js-project").remove();
                    if(!$(".js-project").length) {
                        $(".js-projects").append(noProjects);
                    }
                }
            }
        })
        .on("click", ".js-new-task__add", function(e) {
            e = e || window.event;
            e.preventDefault();

            var $value = $(this).parents(".js-new-task").find(".js-new-task__value").val();
            var $project_id = parseInt($(this).parents(".js-project").data("project_id"));


            var self = $(this).parents(".js-new-task");
            if($value && $project_id) {
                var result = connectServer({
                    action: 'add',
                    type: 'tasks',
                    id: $project_id,
                    content: $value
                });

                if(result.success) {
                    var addHtml = taskTemplate;
                    $(".js-project[data-project_id="+ $project_id +"]").append(
                        addHtml
                            .replace("[%--task_id--%]", result.content)
                            .replace("[%--task_content--%]", $value)
                            .replace("[%--task_checked--%]", '')
                            .replace("[%--task_checked_style--%]", '')
                    ).find(".js-no-task").remove();
                    $(this).parents(".js-new-task").find(".js-new-task__value").val("");
                } else {
                    $(this).parents(".js-new-task").find(".js-new-task__value").addClass('project__new-task__wrong');
                }
            }

            return false;
        })
        .on("click", ".js-edit-task", function() {
            var task = $(this).parents(".js-task");
            var taskContent = task.find(".js-task-content");
            if(!task.find('.js-task-edit__form').html()) {
                var $value = taskContent.html().replace(/[\t\f\r]+/g, '');
                taskContent.css('display', 'none').after(taskContentEdit.replace("[%--task_content--%]", $value));
                task.find(".js-task-edit-input").focus();
            }

            return false;
        })
        .on("submit", ".js-task-edit__form", function(e) {
            e = e || window.event;
            e.preventDefault();
            var $value = $(this).find(".js-task-edit-input").val();
            var $task_id = parseInt($(this).parents(".js-task").data("task_id"));
            var self = $(this);
            if($value) {
                var result = connectServer({
                    action: 'edit',
                    type: 'tasks',
                    id: $task_id,
                    content: $value
                }, function() {
                    self.append(smallLoader.replace("[%--small_loader_context--%]", "small-loader__task-edit"));
                });

                if(result.success) {
                    $(this).blur();
                    $(this).parents(".js-task").find(".js-task-content").html($value);
                }
            }
            $(this).find(".js-task-edit-input").blur();
            $(this).parents(".js-task").find(".js-task-content").html($value);

            return false;
        })
        .on("click", ".js-delete-task", function() {
            var really = confirm("Do you really want to delete task?");
            if(really) {
                var $task_id = parseInt($(this).parents(".js-task").data("task_id"));

                var result = connectServer({
                    action: 'delete',
                    type: 'tasks',
                    id: $task_id
                });

                if(result.success) {
                    var project = $(this).parents(".js-project");
                    $(this).parents(".js-task").remove();
                    if(!project.find('.js-task').length) {
                        project.append(noTasks);
                    }
                }
            }

            return false;
        })
        .on("click", ".js-task-edit-input", function() {
            return false;
        })
        .on("blur", ".js-task-edit-input", function() {
            $(this).parents('.js-task')
                .find('.js-task-content').css({display: 'inline-block'});

            $(this).parent().remove();
            return false;
        })
    ;

    $('footer')
        .on("click", ".js-add-project", function() {
            if(!$(".js-add-project-to-list").html()) {
                $(this).parent().append(addProject);
                $(".js-new-project-name").focus();
                $("html, body").animate({"scrollTop": $(this).offset().top + 'px'}, 1000);
            }
        })
        .on("submit", ".js-add-project-to-list", function(e) {
            e = e || e.window;
            e.preventDefault();

            var $value = $(this).find(".js-new-project-name").val();
            var result = connectServer({
                action: 'add',
                type: 'projects',
                name: $value
            });

            if(result.success) {
                $(".js-projects").append(
                    projectTemplate.replace('[%--project_id--%]', result.content)
                                   .replace('[%--project_name--%]', $value)
                                   .replace('[%--project_tasks--%]', noTasks)
                );

                $(this).find(".js-new-project-name").blur();

                $("html, body").animate({"scrollTop": $(".js-add-project").offset().top + 'px'}, 1000);
            }
        })
        .on("blur", ".js-new-project-name", function() {
            $(this).parent().remove();
        });

    $(".js-task-queries-button").on("click", function() {
        $(".js-task-queries").toggle();
    });

    return false;
});

/**
 * function take an object
 * @param obj - setting of connection
 * @param loader - preloader
 * @param removeLoader - delete preloader after execution
 * @return json
 * */
function connectServer(obj, loader, removeLoader) {
    var result = null;

    $.ajax({
        url: 'ajax',
        type: 'POST',
        dataType: 'json',
        data: obj,
        global: false,
        async: false,
        beforeSend: loader ?
                    loader :
                    null
    })
    .done(function(data) {
        result = data;
    })
    .fail(function(error) {
        result = false;
    });

    if(removeLoader) {
        removeLoader();
    }

    return result;
}

/**
 * take name of template and returns template html
 * @param name
 * @returns html
 */
function getTemplate(name) {
    var result = null;
    $.ajax({
        url: 'view/'+ name +'.html',
        cache: true,
        global: false,
        async: false,
        success: function(html) {
            result = html;
        }
    });

    return result;
}