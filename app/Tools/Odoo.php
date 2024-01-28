<?php

namespace App\Tools;

use Ripcord\Ripcord;

class Odoo
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $db;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;
    /**
     * @var int
     */
    protected $uid;

    /**
     * @var \Ripcord\Client\Client
     */
    protected $client;

    /**
     * Last response
     * @var mixed scalar or array
     */
    public $response;

    /**
     * Ripcord constructor.
     *
     * @param $config array
     */
    public function __construct($config = [])
    {
        if ($config) {
            $this->initialize($config);
        }
    }

    /**
     * Initialize function
     *
     * @param array $config
     * @return void
     */
    public function initialize($config)
    {
        $this->url = $config['url'];
        $this->db = $config['db'];
        $this->username = $config['user'];
        $this->password = $config['password'];
        $this->connect();
    }

    /**
     * Create connection.
     */
    public function connect()
    {
        $common = Ripcord::client("$this->url/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);
        $this->client = Ripcord::client("$this->url/xmlrpc/2/object");
    }

    /**
     * @param string $model
     * @param string $method
     * @param array|null $args argument list, ordered. sequential-array (Python-List) containing, for each numeric index, scalar or array
     * @param array|null $kwargs extra argument list, named. associative-array  (Python-Dictionary) containing, for each keyword, scalar or array
     * @return mixed
     *
     * @author Thomas Bondois
     */
    public function model_execute_kw(string $model, string $method, $args = null, $kwargs = null)
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            $method,
            $args,
            $kwargs
        );
        return $this->setResponse($response);
    }

    /**
     * @param string $model
     * @param string $method
     * @param mixed ...$args
     * @return mixed
     * @author Thomas Bondois <thomas.bondois@agence-tbd.com>
     */
    public function model_execute_splat(string $model, string $method, ...$args)
    {
        $response = $this->client->execute(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            $method,
            $args
        );
        return $this->setResponse($response);
    }

    /**
     * Search models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param integer $offset Offset
     * @param integer $limit Max results
     * @param string $order
     * @param array $context Array of context
     *
     * @return array Array of model id's
     * @throws AuthException|ResponseException
     */
    public function search(string $model, array $criteria = [], $offset = 0, $limit = 0, $order = '', array $context = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'search',
            [$criteria],
            ['offset' => $offset, 'limit' => $limit, 'order' => $order, 'context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Search_count models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param array $context Array of context
     *
     * @return int
     * @throws AuthException|ResponseException
     */
    public function search_count(string $model, array $criteria = [], array $context = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'search_count',
            [$criteria],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Read model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model (external) id's
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     * @param array $context Array of context
     *
     * @return array An array of models
     * @throws AuthException|ResponseException
     */
    public function read(string $model, array $ids, array $fields = [], array $context = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'read',
            [$ids],
            ['fields' => $fields, 'context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Search and Read model(s)
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     * @param integer $limit Max results
     * @param integer $offset
     * @param string $order
     * @param array $context Array of context
     *
     * @return array An array of models
     * @throws AuthException|ResponseException
     */
    public function search_read(string $model, array $criteria, array $fields = [], int $limit = 20, $offset = 0, $order = '', array $context = [])
    {
        echo
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'search_read',
            [$criteria],
            [
                'fields' => $fields,
                'limit'  => $limit,
                'offset'  => $offset,
                'order'  => $order,
                'context' => $context,
            ]
        );
        return $this->setResponse($response);
    }

    /**
     * @see https://www.odoo.com/documentation/11.0/reference/orm.html#odoo.models.Model.fields_get
     *
     * @param string $model
     * @param array $fields
     * @param array $attributes
     *
     * @return mixed
     * @throws AuthException|ResponseException
     *
     * @author Thomas Bondois
     */
    public function fields_get(string $model, array $fields = [], array $attributes = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'fields_get',
            $fields,
            ['attributes' => $attributes]
        );
        return $this->setResponse($response);
    }

    /**
     * Create model
     *
     * @param string $model Model
     * @param array $data Array of fields with data (format: ['field' => 'value'])
     * @param array $context Array of context
     *
     * @return int Created model id
     * @throws AuthException|ResponseException
     */
    public function create(string $model, array $data, array $context = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'create',
            [$data],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Update model(s)
     *
     * @param string $model Model
     * @param array $ids Model ids to update
     * @param array $fields A associative array (format: ['field' => 'value'])
     * @param array $context Array of context
     *
     * @return array
     * @throws AuthException
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function write(string $model, array $ids, array $fields, array $context = [])
    {
        $response = $this->client->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            $model,
            'write',
            [
                $ids,
                $fields,
            ],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Throw exceptions in case the reponse contains error declarations
     * @TODO check keys "status", "status_message" and raised exception "Error"
     *
     * @param mixed $response
     * @return bool
     * @throws Exception
     * @author Thomas Bondois
     */
    public function checkResponse($response)
    {
        if (is_array($response)) {
            if (isset($response['faultCode'])) {
                $faultCode = $response['faultCode'];
                $faultString = $response['faultString'] ?? '';
                throw new \Exception($faultString, $faultCode);
            }
            if (isset($response['status'])) {
                $status = $response['status'];
                $statusMessage = $response['status_message'] ?? $response['statusMessage'] ?? '';
                throw new \Exception($statusMessage, $status);
            }
        }
        return true;
    }

    /**
     * @param mixed $response scalar or array
     * @return mixed|null response
     * @throws Exception
     */
    public function setResponse($response)
    {
        $this->response = null;

        if ($this->checkResponse($response)) {
            $this->response = $response;
        }
        return $this->response;
    }

    /**
     * get last response
     * @return mixed scalar or array
     */
    public function getResponse()
    {
        return $this->response;
    }
}
