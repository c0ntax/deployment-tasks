<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Contracts;

use C0ntax\DeploymentTasks\Entity\Task;

/**
 * Interface TaskServiceInterface
 */
interface TaskServiceInterface
{
    public const TASK_TYPE_PRE = 'Pre';
    public const TASK_TYPE_POST = 'Post';

    /**
     * Given a pre or post task type, return a list of task objects.
     *
     * @param string $taskType
     *
     * @return Task[]
     */
    public function getTasks(string $taskType): array;

    /**
     * Commits a task to memory
     *
     * @param Task $task
     */
    public function rememberTask(Task $task): void;

    /**
     * Returns the path to where all the Tasks are configured to be stored
     *
     * @return string
     */
    public function getTasksDirectory(): string;
}
