<?php

namespace Edujugon\JIRA;

use Edujugon\JIRA\Exceptions\JIRAException;
use GuzzleHttp\Client;

class JIRA
{

    /** @var Client */
    protected $client;

    /** @var  string */
    protected $password;

    /** @var  string */
    protected $username;

    /** @var string */
    protected $url;

    /** @var string */
    protected $uri = '/rest/api/';

    /** @var string */
    protected $version = '2';

    /** @var array */
    protected $project = ['id' => '', 'key' => ''];

    /** @var array */
    protected $issueType = ['id' => '', 'name' => ''];

    /** @var  string */
    protected $summary;

    /** @var  string */
    protected $description;

    /** @var  string */
    private $response;


    public function __construct($username = null, $password = null, $url = null, $version = null)
    {
        $this->client = new Client();

        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
        $this->version = $version ?: $this->version;
    }

    /**
     * Set username
     *
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get Username
     *
     * @return null|string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get Password
     *
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set url
     *
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set version
     *
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get JIRA API version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set project key
     *
     * @param string $key
     * @return $this
     */
    public function setProjectByKey($key)
    {
        $this->project['key'] = $key;

        return $this;
    }

    /**
     * Set project id
     *
     * @param string $id
     * @return $this
     */
    public function setProjectById($id)
    {
        $this->project['id'] = $id;

        return $this;
    }

    /**
     * Get JIRA project data
     * If key passed it returns project key value
     *
     * @param null $key (name|id)
     * @return array|mixed
     */
    public function getProject($key = null)
    {
        return (is_null($key) && !array_key_exists($key,$this->project)) ?
            $this->project :
            $this->project[$key];
    }

    /**
     * Set issue type by name
     *
     * @param string $name
     * @return $this
     */
    public function setIssueTypeByName($name)
    {
        $this->issueType['name'] = $name;

        return $this;
    }

    /**
     * Set issue type by id
     *
     * @param string $id
     * @return $this
     */
    public function setIssueTypeById($id)
    {
        $this->issueType['id'] = $id;

        return $this;
    }

    /**
     * Get JIRA issue type
     * If key passed, it returns issue type key value
     *
     * @param null $key (name|id)
     * @return array|mixed
     */
    public function getIssueType($key = null)
    {
        return (is_null($key) && !array_key_exists($key,$this->issueType)) ?
            $this->issueType :
            $this->issueType[$key];
    }

    /**
     * Set summary (title)
     *
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set summary (title)
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add a new line to description
     * @param string|null $line
     * @return $this
     */
    public function addDescriptionNewLine($line = '')
    {
        $this->description .= ' \\\ ' . $line;

        return $this;
    }

    public function createIssue()
    {
        $this->basicValidations();

        $this->send('POST', $this->getUri('issue'), $this->createBody());

        $this->description = '';
        $this->summary = '';

        return $this;
    }

    protected function send($method, $uri, $body)
    {
        $this->response = $this->client->request(
            $method,
            $uri,
            [
                'headers' =>
                    [
                        'Authorization' =>
                            [
                                'Basic ' . $this->getCredentials()
                            ]
                    ],
                'json' => $body
            ]
        )->getBody()->getContents();
    }

    /**
     * Generate uri
     *
     * @param string $extra
     * @return string
     */
    protected function getUri($extra = null)
    {
        return $this->joinBySlash($this->joinBySlash($this->joinBySlash($this->url, $this->uri), $this->version), $extra);
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Generate base 64 credential
     * @return string
     */
    private function getCredentials()
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    /**
     * @param $data
     * @param bool $prefix
     * @param bool $suffix
     * @return string
     */
    private function addSlash($data, $prefix = true, $suffix = true)
    {
        if ($prefix) $data = substr($data, 0, 1) !== '/' ? '/' . $data : $data;
        if ($suffix) $data = substr($data, -1) !== '/' ? $data . '/' : $data;
        return $data;
    }

    /**
     * Concat to string adding a slash if required
     *
     * @param $prefix
     * @param $suffix
     * @return string
     */
    private function joinBySlash($prefix, $suffix)
    {
        if (substr($prefix, -1) === '/' && substr($suffix, 0, 1) === '/') {
            $prefix = rtrim($prefix, "/");
        } elseif (substr($prefix, -1) !== '/' && substr($suffix, 0, 1) !== '/') {
            $prefix = $prefix . '/';
        }
        return $prefix . $suffix;
    }

    /**
     * Remote empty values from an array
     *
     * @param $array
     * @return array
     */
    protected function removeEmptyValues($array)
    {
        return array_filter($array, function ($value) {
            return !empty($value);
        });
    }

    /**
     * Build json body
     *
     * @return array
     */
    protected function createBody()
    {
        return ['fields' =>
            [
                'project' => $this->removeEmptyValues($this->project),
                'summary' => $this->summary,
                'description' => $this->description,
                'issuetype' => $this->removeEmptyValues($this->issueType),
            ]
        ];
    }

    /**
     * Validator
     *
     * @throws \Exception
     */
    private function basicValidations()
    {
        if (empty($this->username))
            throw new JIRAException('You must provide an username');
        if (empty($this->password))
            throw new JIRAException('You must provide a password');
        if (empty($this->url))
            throw new JIRAException('You must provide an url');
    }

}