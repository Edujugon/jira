<?php

class JIRATest extends \PHPUnit_Framework_TestCase
{

    /** @var  \Edujugon\JIRA\JIRA */
    protected $jira;

    protected function setUp()
    {
        $this->jira = new Edujugon\JIRA\JIRA();
    }

    /** @test */
    public function has_username()
    {
        $name = 'John Doe';
        $this->jira->setUsername($name);

        $this->assertEquals($name,$this->jira->getUserName());
    }

    /** @test */
    public function has_password()
    {
        $pass = 'secret';
        $this->jira->setPassword($pass);

        $this->assertEquals($pass,$this->jira->getPassword());
    }

    /** @test */
    public function has_url()
    {
        $url = 'http://my-jira-url';
        $this->jira->setUrl($url);

        $this->assertEquals($url,$this->jira->getUrl());
    }

    /** @test */
    public function has_version()
    {
        $version = '3';
        $this->jira->setVersion($version);

        $this->assertEquals($version,$this->jira->getVersion());
    }

    /** @test */
    public function has_project_key()
    {
        $key = 'DD';
        $this->jira->setProjectByKey($key);

        $this->assertEquals($key,$this->jira->getProject('key'));
    }

    /** @test */
    public function has_project_id()
    {
        $id = '1462';
        $this->jira->setProjectById($id);

        $this->assertEquals($id,$this->jira->getProject('id'));
    }

    /** @test */
    public function has_issue_type_name()
    {
        $name = 'Task';
        $this->jira->setIssueTypeByName($name);

        $this->assertEquals($name,$this->jira->getIssueType('name'));
    }

    /** @test */
    public function has_issue_type_id()
    {
        $id = '1';
        $this->jira->setIssueTypeById($id);

        $this->assertEquals($id,$this->jira->getIssueType('id'));
    }

    /** @test */
    public function has_summary()
    {
        $summary = 'issue title';
        $this->jira->setSummary($summary);

        $this->assertEquals($summary,$this->jira->getSummary());
    }

    /** @test */
    public function has_description()
    {
        $description = 'issue description';
        $this->jira->setDescription($description);

        $this->assertEquals($description,$this->jira->getDescription());
    }
    
    /** @test */
    public function add_new_description_line()
    {
        $description = 'issue description';
        $newLine = 'new line';
        $this->jira->setDescription($description);
        $this->jira->addDescriptionNewLine($newLine);

        $this->assertEquals($description . ' \\\ ' . $newLine ,$this->jira->getDescription());
    }

    /** @test */
    public function build_a_correct_uri()
    {
        $url = 'http://my-jira-url';
        $version = '3';
        $this->jira->setUrl($url)->setVersion($version);

        $this->assertEquals('http://my-jira-url/rest/api/3/create',$this->invokeMethod($this->jira,'getUri',['create']));
    }

    /** @test */
    public function credentials_in_base_64()
    {
        $username = 'JonhDoe';
        $pass = 'secret';

        $this->jira->setUsername($username);
        $this->jira->setPassword($pass);

        $credentials = base64_encode($username . ':' . $pass);

        $this->assertEquals($credentials,$this->invokeMethod($this->jira,'getCredentials'));

    }

    /** @test */
    public function concat_strings_by_slash()
    {
        $string1 = 'JonhDoe';
        $string2 = 'secret';

        $this->assertEquals($string1 . '/' . $string2,$this->invokeMethod($this->jira,'joinBySlash',[$string1,$string2]));
    }

    /** @test */
    public function project_has_only_name()
    {
        $key = 'EDU';

        $this->jira->setProjectByKey($key);

        $this->assertEquals(['key' => $key],$this->invokeMethod($this->jira,'removeEmptyValues',[$this->jira->getProject()]));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}