<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Exceptions;

use RuntimeException;
use TTBooking\Derevo\Node;

class TreeOverflowException extends RuntimeException
{
    protected ?Node $node = null;

    /**
     * @param  Node  $node
     * @return $this
     */
    public function setNode(Node $node): self
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return Node|null
     */
    public function getNode(): ?Node
    {
        return $this->node;
    }
}
