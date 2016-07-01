<?php
$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');
$sql = file_get_contents(__DIR__ . "/software.sql");
//$pdo->query($sql)->execute();

$db = new \Pilulka\Database\ConnectionResolver();
$db->addService('memory', new \Pilulka\Database\Connection($pdo));
$db->setDefault('memory');
\Pilulka\Database\Model::setConnectionResolver($db);

class Application extends \Pilulka\Database\Model
{
    protected $table = 'application';

    public function getAuthor()
    {
        return $this->getRelated(Author::class);
    }

    public function getMaintainer()
    {
        return $this->getRelated(Author::class, 'maintainer');
    }
}

class Author extends \Pilulka\Database\Model
{
    protected $table = 'author';

    /**
     * @param array $filter
     * @return Application[]
     */
    public function getApplications($filter = [])
    {
        return $this->getRelatedMany(Application::class, $filter);
    }

    public function getMaintainedApplications($filter = [])
    {
        return $this->getRelatedMany(Application::class, $filter, 'maintainer_id');
    }

}

class Tag extends \Pilulka\Database\Model
{
    protected $table = 'tag';
}

class ApplicationTag extends \Pilulka\Database\Model
{
    protected $table = 'application_tag';
}

