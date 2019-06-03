# c0ntax/deployment-tasks

## Introduction

This is a simple (framework agnostic) library for running tasks once (and only once) at deployment time. Out of the box, it's designed to work with the [Knp Gaufrette](https://github.com/KnpLabs/Gaufrette) library so that you can persist
the tasks that have run anywhere you want (ideally, a database if your project has access to it)

## Installation

```bash
composer req c0ntax/deployment-tasks
```

Err, that's it

## Configuration

Configuration is simple. All you need to do is inject two configured Gaufrette Filesystems into the TaskService. They are:
1. The local project filesystem where your tasks will be stored
2. The location of your task memory that keeps a track of what has been run

```php
use C0ntax\DeploymentTasks\Service\TaskService;
use Gaufrette\Filesystem;
use C0ntax\DeploymentTasks\Gaufrette\Adapter\Local;
use Gaufrette\Adapter\DoctrineDbal;

$taskFilesystem = new Filesystem(new Local('/path/to/tasks'));
$memoryFilesystem = new Filesystem(new DoctrineDbal(...));

$taskService = new TaskService($taskFilesystem, $memoryFilesystem);
```

You also need to make sure that you've set up the task directories. For example:

```text
/path
  /to
    /tasks
      /Pre
        /task1232198312.sh
        /task3210948323.sh
      /Post
        /tasks0298340932.sh
```

## Execution

Once it's configured, then you just have to pass the TaskService as defined above into the RunnerService

```php
use C0ntax\DeploymentTasks\Contracts\TaskServiceInterface;
use C0ntax\DeploymentTasks\Service\RunnerService;

$runnerService = new RunnerService($taskService);
// To run pre deployment tasks (i.e. the codebase has been build and staged, but is not yet live)
$runnerService->run(TaskServiceInterface::TASK_TYPE_PRE);

// To run post deployment tasks (i.e. the codebase is now live and taking requests)
$runnerService->run(TaskServiceInterface::TASK_TYPE_POST);
```
