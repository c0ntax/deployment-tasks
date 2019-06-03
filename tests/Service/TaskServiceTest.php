<?php

namespace C0ntax\DeploymentTasks\Tests\Service;

use C0ntax\DeploymentTasks\Contracts\TaskServiceInterface;
use C0ntax\DeploymentTasks\Entity\Task;
use C0ntax\DeploymentTasks\Exception\Service\TaskService\TaskNotRememberedException;
use C0ntax\DeploymentTasks\Exception\Service\TaskService\TaskNotRunException;
use C0ntax\DeploymentTasks\Exception\Service\TaskService\TaskNotSuccessException;
use C0ntax\DeploymentTasks\Service\TaskService;
use Gaufrette\Adapter\Local;
use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{

    public function testGetTasksConcrete()
    {
        $taskService = $this->createTaskService();

        $postTasks = $taskService->getTasks(TaskService::TASK_TYPE_POST);
        self::assertCount(1, $postTasks);
        self::assertSame('Post/task20180603105600.sh', $postTasks[0]->getId());
        self::assertRegExp('/\/Post\/task20180603105600.sh$/', $postTasks[0]->getCmd()[0]);
        self::assertFalse($postTasks[0]->isRun());
        self::assertFalse($postTasks[0]->isSuccess());

        $preTasks = $taskService->getTasks(TaskService::TASK_TYPE_PRE);
        self::assertCount(2, $preTasks);

        self::assertSame('Pre/task20180602090000.sh', $preTasks[0]->getId());
        self::assertRegExp('/\/Pre\/task20180602090000.sh$/', $preTasks[0]->getCmd()[0]);
        self::assertFalse($preTasks[0]->isRun());
        self::assertFalse($preTasks[0]->isSuccess());

        self::assertSame('Pre/task20180603105600.sh', $preTasks[1]->getId());
        self::assertRegExp('/\/Pre\/task20180603105600.sh$/', $preTasks[1]->getCmd()[0]);
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

    public function testRememberTaskRunException()
    {
        $id = uniqid(TaskServiceInterface::TASK_TYPE_PRE.'/', true);

        $this->expectException(TaskNotRunException::class);
        $this->expectExceptionMessage(sprintf('Task %s has not been run yet', $id));

        $task = new Task($id, [$id], false, true);

        $taskService = $this->createTaskService();
        $taskService->rememberTask($task);
    }

    public function testRememberTaskSuccessException()
    {
        $id = uniqid(TaskServiceInterface::TASK_TYPE_PRE.'/', true);

        $this->expectException(TaskNotSuccessException::class);
        $this->expectExceptionMessage(sprintf('Task %s has not been successful', $id));

        $task = new Task($id, [$id], true, false);

        $taskService = $this->createTaskService();
        $taskService->rememberTask($task);
    }

    public function testRememberTaskRememberException()
    {
        $id = uniqid(TaskServiceInterface::TASK_TYPE_PRE.'/', true);

        $this->expectException(TaskNotRememberedException::class);
        $this->expectExceptionMessage(sprintf('Task %s could not be committed to memory', $id));

        $task = new Task($id, [$id], true, true);

        $memoryFilesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $memoryFilesystem
            ->expects(self::once())
            ->method('write')
            ->with($id)
            ->willThrowException(new FileAlreadyExists($id));

        $taskService = $this->createTaskService(['memoryFilesystem' => $memoryFilesystem]);
        $taskService->rememberTask($task);
    }

    public function testRememberTask()
    {
        $id = uniqid(TaskServiceInterface::TASK_TYPE_PRE.'/', true);

        $task = new Task($id, [$id], true, true);

        $memoryFilesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $memoryFilesystem
            ->expects(self::once())
            ->method('write')
            ->with($id);

        $taskService = $this->createTaskService(['memoryFilesystem' => $memoryFilesystem]);
        $taskService->rememberTask($task);
    }

    /**
     * @return Filesystem
     * @throws \RuntimeException
     */
    private function createTaskFilesystem(): Filesystem
    {
        $filesystemAdapter = new \C0ntax\DeploymentTasks\Gaufrette\Adapter\Local(realpath(__DIR__.'/../Fixtures/DeploymentTasks'));

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
