<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasks\Gaufrette\Adapter;

/**
 * Class Local
 *
 * Annoyingly (although I can see why it's done that way) the Local adapter that comes with Gaufrette does not expose
 * the directory, but it's something that we need, so we'll use this! ;-)
 */
class Local extends \Gaufrette\Adapter\Local
{
    /**
     * @return bool|string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
