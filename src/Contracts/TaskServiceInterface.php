<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Contracts;

use C0ntax\DeploymentTasks\Entity\Task;

/**
 * Interface TaskServiceInterface
 */
interface TaskServiceInterface
{
    /**
     * Given a pre or post task type, return a list of task objects.
     *
     * @param string $taskType
     *
     * @return Task[]
     */
    public function getTasks(string $taskType): array;
}
