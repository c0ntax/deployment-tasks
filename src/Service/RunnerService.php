<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Service;

use C0ntax\DeploymentTasks\Contracts\TaskServiceInterface;
use C0ntax\DeploymentTasks\Entity\Task;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Class Runner
 */
class RunnerService
{
    /** @var TaskServiceInterface */
    private $taskService;

    /**
     * RunnerService constructor.
     *
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->setTaskService($taskService);
    }

    /**
     * @param string $taskType
     *
     * @return Task[]
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws ProcessFailedException
     * @throws RuntimeException
     */
    public function run(string $taskType): array
    {
        $outputTasks = [];
        foreach ($this->getTaskService()->getTasks($taskType) as $task) {
            $this->runTask($task);
            $outputTasks[] = $task;
        }

        return $outputTasks;
    }

    /**
     * @param Task $task
     *
     * @throws ProcessFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws RuntimeException
     */
    private function runTask(Task $task): void
    {
        $process = new Process($task->getCmd());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $task->setRun(true);
        $task->setSuccess(true);

        $this->getTaskService()->rememberTask($task);
    }

    /**
     * @return TaskServiceInterface
     */
    private function getTaskService(): TaskServiceInterface
    {
        return $this->taskService;
    }

    /**
     * @param TaskServiceInterface $taskService
     *
     * @return RunnerService
     */
    private function setTaskService(TaskServiceInterface $taskService): RunnerService
    {
        $this->taskService = $taskService;

        return $this;
    }
}
