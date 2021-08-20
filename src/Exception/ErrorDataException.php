<?php

namespace Proglab\SelligentClientBundle\Exception;

use Throwable;

class ErrorDataException extends \InvalidArgumentException
{
    private array $datas;

    public function getDatas(): array
    {
        return $this->datas;
    }

    public function setDatas(array $datas): void
    {
        $this->datas = $datas;
    }

    public function __construct(string $message = '', int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
