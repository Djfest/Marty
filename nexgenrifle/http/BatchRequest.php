<?php namespace Marty\NexGenRifle\Http;

class BatchRequest
{
    protected $operations;
    protected $transaction;

    public function __construct(array $operations)
    {
        $this->operations = $operations;
        $this->transaction = true;
    }

    public function setTransaction(bool $useTransaction)
    {
        $this->transaction = $useTransaction;
        return $this;
    }

    public function validate()
    {
        foreach ($this->operations as $operation) {
            if (!isset($operation['method']) || !isset($operation['resource'])) {
                throw new \ApplicationException('Invalid operation format - method and resource are required');
            }

            if (!in_array(strtoupper($operation['method']), ['GET', 'POST', 'PUT', 'DELETE'])) {
                throw new \ApplicationException('Invalid HTTP method: ' . $operation['method']);
            }
        }
    }

    public function getOperations()
    {
        return $this->operations;
    }

    public function useTransaction()
    {
        return $this->transaction;
    }
}
