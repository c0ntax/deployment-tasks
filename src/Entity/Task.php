<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Entity;

/**
 * Class Task
 */
class Task
{
    /** @var string */
    private $id;

    /** @var array */
    private $cmd;

    /** @var bool */
    private $run = false;

    /** @var bool */
    private $success = false;

    /**
     * Task constructor.
     *
     * @param string $id
     * @param array  $cmd
     * @param bool   $run
     * @param bool   $success
     */
    public function __construct(string $id, array $cmd, bool $run = false, bool $success = false)
    {
        $this
            ->setId($id)
            ->setCmd($cmd)
            ->setRun($run)
            ->setSuccess($success);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getCmd(): array
    {
        return $this->cmd;
    }

    /**
     * @return bool
     */
    public function isRun(): bool
    {
        return $this->run;
    }

    /**
     * @param bool $run
     *
     * @return Task
     */
    public function setRun(bool $run): Task
    {
        $this->run = $run;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     *
     * @return Task
     */
    public function setSuccess(bool $success): Task
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Task
     */
    private function setId(string $id): Task
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param array $cmd
     *
     * @return Task
     */
    private function setCmd(array $cmd): Task
    {
        $this->cmd = $cmd;

        return $this;
    }
}
