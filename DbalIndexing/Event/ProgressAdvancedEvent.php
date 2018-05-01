<?php declare(strict_types=1);

namespace Shopware\DbalIndexing\Event;

use Symfony\Component\EventDispatcher\Event;

class ProgressAdvancedEvent extends Event
{
    public const NAME = 'indexing.progress.advanced';

    /**
     * @var int
     */
    protected $step;

    public function __construct(int $step = 1)
    {
        $this->step = $step;
    }

    public function getStep(): int
    {
        return $this->step;
    }
}
