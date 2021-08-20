<?php

namespace Proglab\SelligentClientBundle\Models;

use App\Exception\ErrorDataException;

class SoapClient
{
    protected $client = null;

    protected $individual_url;
    protected $broadcast_url;
    protected $login;
    protected $password;

    protected $lid = '';
    protected $filter = [];
    protected $maxcount = 10;
    protected $constraint = '';

    protected $uid = '';
    protected $properties = [];
    protected $gate = '';

    public function __construct(string $individual_url, string $broadcast_url, string $login, string $password)
    {
        $this->individual_url = $individual_url;
        $this->broadcast_url = $broadcast_url;
        $this->login = $login;
        $this->password = $password;
    }

    protected function getHeader()
    {
        return new \SoapHeader('http://tempuri.org/', 'AutomationAuthHeader', [
                'Login' => $this->login,
                'Password' => $this->password,
                'connection_timeout' => 0,
                'default_socket_timeout' => 0,
                'exceptions' => true,
                'trace' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'stream_context' => stream_context_create([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                ]),
            ]
        );
    }

    public function getClient(): \SoapClient
    {
        return $this->client;
    }

    public function setClient(\SoapClient $client): void
    {
        $this->client = $client;
    }

    public function getLid(): string
    {
        return $this->lid;
    }

    public function setLid(string $lid): void
    {
        $this->lid = $lid;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    public function getMaxcount(): int
    {
        return $this->maxcount;
    }

    public function setMaxcount(int $maxcount): void
    {
        $this->maxcount = $maxcount;
    }

    public function getConstraint(): string
    {
        return $this->constraint;
    }

    public function setConstraint(string $constraint): void
    {
        $this->constraint = $constraint;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getGate(): string
    {
        return $this->gate;
    }

    public function setGate(string $gate): void
    {
        $this->gate = $gate;
    }

    public function addProperty($key, $value)
    {
        $this->properties[] = ['Key' => $key, 'Value' => $value];

        return $this;
    }

    public function addFilter($key, $value)
    {
        $this->filter[] = ['Key' => $key, 'Value' => $value];

        return $this;
    }

    public function createRow($data, $listId)
    {
        $this->properties = [];
        if (array_key_exists('created', $data)) {
            $data['CREATED_DT'] = $data['created'];
            unset($data['created']);
        }

        $this->setLid($listId);
        $this->properties = [];
        foreach ($data as $key => $value) {
            $this->addProperty(strtoupper($key), $value);
        }

        if (!array_key_exists('CREATED_DT', $data) || (isset($data['CREATED_DT']) && empty($data['CREATED_DT']))) {
            $this->addProperty('CREATED_DT', date('Y-m-d H:i:s'));
        }

        $result = $this->call('CreateUser', [
            'List' => $this->lid,
            'Changes' => $this->properties,
        ]);

        if (isset($result->ErrorStr) && '' !== $result->ErrorStr) {
            $exception = new ErrorDataException('Selligent Error : '.$result->ErrorStr.' '.json_encode(['List' => $this->lid, 'Datas' => $this->properties]));
            $exception->setDatas(['List' => $this->lid, 'Datas' => $this->properties]);
            throw $exception;
        }

        return $result;
    }

    public function getOneByFilter($filters, $listId)
    {
        $this->filter = [];
        $this->setLid($listId);
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $this->addFilter(strtoupper($key), $value);
        }

        if (empty($this->lid)) {
            throw new ErrorDataException('No listID are set.');
        }
        $result = $this->call('GetUserByFilter', [
            'List' => $this->lid,
            'Filter' => $this->filter,
        ]);

        if (isset($result->ErrorStr) && !empty($result->ErrorStr)) {
            if ('Unable to retrieve user!' == $result->ErrorStr) {
                return [];
            }

            $exception = new ErrorDataException('Selligent Error : '.$result->ErrorStr.' '.json_encode(['List' => $this->lid, 'Filters' => $this->filter, 'result' => $result]));
            $exception->setDatas(['List' => $this->lid, 'Datas' => $this->properties]);
            throw $exception;
        } else {
            if (isset($result->ResultSet)) {
                return $this->fetchUserResultSet($result->ResultSet->Property);
            }
        }

        return [];
    }

    public function updateRow($data, $id, $listId)
    {
        $this->properties = [];
        $this->setLid($listId);
        $this->setUid($id);

        foreach ($data as $key => $value) {
            $this->addProperty($key, $value);
        }

        if ('' == $this->lid || 0 == sizeof($this->properties) || '' == $this->uid) {
            throw new \Exception('Not all properties are set for this method.');
        }

        $result = $this->call('UpdateUser', [
            'List' => $this->lid,
            'Changes' => $this->properties,
            'UserID' => $this->uid,
        ]);

        return $result;
    }

    public function call($method, $param = '')
    {
        if (is_null($this->client)) {
            try {
                $this->client = new \SoapClient($this->individual_url);
                $this->client->__setSoapHeaders($this->getHeader());
            } catch (\Exception $e) {
                throw new \Exception('Could not connect: '.$e->getMessage());
            }
        }
        if ($this->client) {
            return $this->client->{$method}($param);
        } else {
            throw new \Exception('SOAP Client unavailable');
        }
    }

    public function getSystemStatus()
    {
        $result = $this->call('GetSystemStatus', null);

        if (isset($result->GetSystemStatusResult)) {
            return $result->GetSystemStatusResult;
        } else {
            return 'unable to retrieve status';
        }
    }

    protected function fetchUserResultSet($resultSet)
    {
        $return = [];
        foreach ($resultSet as $result) {
            $return[$result->Key] = $result->Value;
        }

        return $return;
    }
}