<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Service;

use C0ntax\DeploymentTasks\Contracts\TaskServiceInterface;
use C0ntax\DeploymentTasks\Entity\Task;
use Gaufrette\FilesystemInterface;

/**
 * Class TaskService
 *
 * Wired into the more Symfony-facing gaufrette filesystem library, this TaskService will fetch all the Tasks that are
 * required. You can always write your own to use Flysystem if you like
 */
class TaskService implements TaskServiceInterface
{
    public const TASK_TYPE_PRE = 'Pre';
    public const TASK_TYPE_POST = 'Post';

    /** @var FilesystemInterface */
    private $taskFilesystem;

    /** @var FilesystemInterface */
    private $memoryFilesystem;

    /**
     * TaskService constructor.
     *
     * @param FilesystemInterface $taskFilesystem
     * @param FilesystemInterface $memoryFilesystem
     */
    public function __construct(FilesystemInterface $taskFilesystem, FilesystemInterface $memoryFilesystem)
    {
        $this
            ->setTaskFilesystem($taskFilesystem)
            ->setMemoryFilesystem($memoryFilesystem);
    }

    /**
     * Given a taskType, get a list of all tasks that have not been run
     *
     * @param string $taskType
     *
     * @return Task[]
     */
    public function getTasks(string $taskType): array
    {
        $taskKeys = $this->getToRunTaskKeys($taskType);

        $tasks = [];
        foreach ($taskKeys as $taskKey) {
            $tasks[] = new Task($taskKey, './'.$taskKey);
        }

        return $tasks;
    }

    /**
     * @param string $taskType
     *
     * @return array
     */
    private function getToRunTaskKeys(string $taskType): array
    {
        $localTaskKeys = $this->getLocalTaskKeys($taskType);
        $memoryTaskKeys = $this->getMemoryTaskKeys($taskType);

        // Now to get where they don't overlap

        return array_diff($localTaskKeys, $memoryTaskKeys);
    }

    /**
     * @param string $taskType
     *
     * @return array
     */
    private function getLocalTaskKeys(string $taskType): array
    {
        return $this->getTaskKeys($this->getTaskFilesystem(), $taskType);
    }

    /**
     * @param string $taskType
     *
     * @return array
     */
    private function getMemoryTaskKeys(string $taskType): array
    {
        return $this->getTaskKeys($this->getMemoryFilesystem(), $taskType);
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param string              $taskType
     *
     * @return array
     */
    private function getTaskKeys(FilesystemInterface $filesystem, string $taskType): array
    {
        $allFileKeysRaw = $filesystem->listKeys($taskType.'/');

        $allFileKeys = array_key_exists('keys', $allFileKeysRaw) ? $allFileKeysRaw['keys'] : $allFileKeysRaw;
        sort($allFileKeys);

        return $allFileKeys;
    }

    private function getRunTasks(string $taskType): array
    {

    }

    /**
     * @return FilesystemInterface
     */
    private function getTaskFilesystem(): FilesystemInterface
    {
        return $this->taskFilesystem;
    }

    /**
     * @param FilesystemInterface $taskFilesystem
     *
     * @return TaskService
     */
    private function setTaskFilesystem(FilesystemInterface $taskFilesystem): TaskService
    {
        $this->taskFilesystem = $taskFilesystem;

        return $this;
    }

    /**
     * @return FilesystemInterface
     */
    private function getMemoryFilesystem(): FilesystemInterface
    {
        return $this->memoryFilesystem;
    }

    /**
     * @param FilesystemInterface $filesystem
     *
     * @return TaskService
     */
    private function setMemoryFilesystem(FilesystemInterface $filesystem): TaskService
    {
        $this->memoryFilesystem = $filesystem;

        return $this;
    }
}
