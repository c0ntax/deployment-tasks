<?php

namespace C0ntax\DeploymentTasks\Tests\Service;

use C0ntax\DeploymentTasks\Contracts\TaskServiceInterface;
use C0ntax\DeploymentTasks\Entity\Task;
use C0ntax\DeploymentTasks\Service\RunnerService;
use PHPUnit\Framework\TestCase;

class RunnerServiceTest extends TestCase
{

    public function testRun()
    {
        $taskType = TaskServiceInterface::TASK_TYPE_PRE;
        
        $tasks = [
            new Task('Pre/task20180602090000.sh', [__DIR__.'/../Fixtures/DeploymentTasks/Pre/task20180602090000.sh']),
            new Task('Pre/task20180603105600.sh', [__DIR__.'/../Fixtures/DeploymentTasks/Pre/task20180603105600.sh'])
        ];
        
        $taskService = $this->getMockBuilder(TaskServiceInterface::class)->getMock();
        $taskService
            ->expects(self::once())
            ->method('getTasks')
            ->with($taskType)
            ->willReturn($tasks);
        
        $taskService
            ->expects(self::at(1))
            ->method('rememberTask')
            ->with($tasks[0]);

        $taskService
            ->expects(self::at(2))
            ->method('rememberTask')
            ->with($tasks[1]);

        $runnerService = $this->createRunnerService(['taskService' => $taskService]);
        $outputTasks = $runnerService->run($taskType);

        self::assertCount(2, $outputTasks);
        foreach ($outputTasks as $outputTask) {
            self::assertTrue($outputTask->isRun());
            self::assertTrue($outputTask->isSuccess());
        }
    }

    public function createRunnerService(?array $mocks = []): RunnerService
    {
        $taskService = array_key_exists('taskService', $mocks) ? $mocks['taskService'] : $this->getMockBuilder(TaskServiceInterface::class)->getMock();

        return new RunnerService($taskService);
    }
}
