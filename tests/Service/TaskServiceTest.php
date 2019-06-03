<?php

namespace C0ntax\DeploymentTasks\Tests\Service;

use C0ntax\DeploymentTasks\Service\TaskService;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{

    public function testGetTasksConcrete()
    {
        $taskService = $this->createTaskService();

        $postTasks = $taskService->getTasks(TaskService::TASK_TYPE_POST);
        self::assertCount(1, $postTasks);
        self::assertSame('Post/task20180603105600.sh', $postTasks[0]->getId());
        self::assertSame('./Post/task20180603105600.sh', $postTasks[0]->getCmd());
        self::assertFalse($postTasks[0]->isRun());
        self::assertFalse($postTasks[0]->isSuccess());

        $preTasks = $taskService->getTasks(TaskService::TASK_TYPE_PRE);
        self::assertCount(2, $preTasks);

        self::assertSame('Pre/task20180602090000.sh', $preTasks[0]->getId());
        self::assertSame('./Pre/task20180602090000.sh', $preTasks[0]->getCmd());
        self::assertFalse($preTasks[0]->isRun());
        self::assertFalse($preTasks[0]->isSuccess());

        self::assertSame('Pre/task20180603105600.sh', $preTasks[1]->getId());
        self::assertSame('./Pre/task20180603105600.sh', $preTasks[1]->getCmd());
        self::assertFalse($preTasks[1]->isRun());
        self::assertFalse($preTasks[1]->isSuccess());
    }

    public function testGetTasks()
    {
        self::markTestIncomplete('Add a whole load of mocked test cases here');
    }

    /**
     * @param array|null $mocks
     *
     * @return TaskService
     * @throws \RuntimeException
     */
    private function createTaskService(?array $mocks = []): TaskService
    {
        $taskFilesystem = array_key_exists('taskFilesystem', $mocks) ? $mocks['taskFilesystem'] : $this->createTaskFilesystem();
        $memoryFilesystem = array_key_exists('memoryFilesystem', $mocks) ? $mocks['memoryFilesystem'] : $this->createMemoryFilesystem();

        return new TaskService($taskFilesystem, $memoryFilesystem);
    }

    /**
     * @return Filesystem
     * @throws \RuntimeException
     */
    private function createTaskFilesystem(): Filesystem
    {
        $filesystemAdapter = new Local(realpath(__DIR__.'/../Fixtures/DeploymentTasks'));

        return new Filesystem($filesystemAdapter);
    }

    /**
     * @return Filesystem
     * @throws \RuntimeException
     */
    private function createMemoryFilesystem(): Filesystem
    {
        $filesystemAdapter = new Local(realpath(__DIR__.'/../Fixtures/MemoryTasks'));

        return new Filesystem($filesystemAdapter);
    }
}
