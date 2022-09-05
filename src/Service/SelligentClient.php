<?php

namespace ShelfUtilities\SelligentClientBundle\Service;

use ShelfUtilities\SelligentClientBundle\Models\SoapClient;

class SelligentClient
{
    private $soap_client = null;

    public function __construct(string $individual_url, string $broadcast_url, string $login, string $password)
    {
        $this->soap_client = new SoapClient($individual_url, $broadcast_url, $login, $password);
    }

    public function getSystemStatus()
    {
        return $this->soap_client->getSystemStatus();
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
