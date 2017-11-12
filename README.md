# JIRA

Easy to use wrapper for Jira REST api

##  Installation

##### Type in console:

```
composer require edujugon/jira
```

##  Usage samples

```php
$jira = new Edujugon\JIRA\JIRA($username,$password,$url);
```

#### Set the project to interact with

You can set the project either by `key` or `id`.
The available methods are:

```php
$jira->setProjectByKey('PI');
```
or
```php
$jira->setProjectById('162');
```

#### Set the issue type

You can set the issue type either by `name` or `id`.
The available methods are:

```php
$jira->setIssueTypeByName('Task');
```
or
```php
$jira->setIssueTypeById('1');
```

### Create an issue

```php
$jira->setProjectByKey('PI')
    ->setIssueTypeByName('Task')
    ->setSummary('Issue title')
    ->setDescription('First line of the description')
    ->addDescriptionNewLine('Another line')
    ->addDescriptionNewLine('One line more :)')
    ->createIssue();
```

### More options soon

Enjoy :)