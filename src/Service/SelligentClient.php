<?php

namespace Proglab\SelligentClientBundle\Service;

use Proglab\SelligentClientBundle\Models\SoapClient;

class SelligentClient
{
    /**
     * @var SelligentClient
     */
    private $selligent = null;
    private $soap_client = null;

    public function __construct(string $individual_url, string $broadcast_url, string $login, string $password)
    {
        $this->soap_client = new SoapClient($individual_url, $broadcast_url, $login, $password);
    }

    public function getSystemStatus()
    {
        return $this->selligent->getSystemStatus();
    }

    public function getOneByFilter($filters, $listId)
    {
        return $this->soap_client->getOneByFilter($filters, $listId);
    }

    public function createRow($data, $listId)
    {
        return $this->soap_client->createRow($data, $listId);
    }

    public function updateRow($data, $id, $listId)
    {
        return $this->soap_client->updateRow($data, $id, $listId);
    }

    public function triggerCampaign($gate = null, $properties = [])
    {
        return $this->soap_client->triggerCampaign($gate, $properties);
    }

    public function triggerCampaignForUser($userId = null, $gate = null, $listId = null, $params = [])
    {
        return $this->soap_client->triggerCampaignForUser($userId, $gate, $listId, $params);
    }
}
