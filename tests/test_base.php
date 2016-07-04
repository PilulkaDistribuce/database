<?php

use Pilulka\Database\Model;

$pdo = new PDO('sqlite::memory:');
$sql = file_get_contents(__DIR__ . "/software.sql");
$pdo->exec($sql);

$db = new \Pilulka\Database\ConnectionResolver();
$db->addService('memory', new \Pilulka\Database\Connection($pdo));
$db->setDefault('memory');
Model::setConnectionResolver($db);

class Application extends Model
{
    protected $table = 'application';

    public function getAuthor()
    {
        return $this->getRelated(Author::class);
    }

    /**
     * @return Author|null
     */
    public function getMaintainer()
    {
        return $this->getRelated(Author::class, 'maintainer');
    }

    public function getApplicationTags($filter=[])
    {
        return $this->getRelatedMany(ApplicationTag::class, $filter);
    }

    public function getTags($filter=[])
    {
        $tags = Tag::where(
            'id',
            $this->getApplicationTags()->getIterator()->select('tag_id')
        );
        if($filter) {
            $tags->where($filter);
        }
        return $tags->all();
    }
}

class Author extends Model
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

class Tag extends Model
{
    protected $table = 'tag';

    /**
     * @param array $filter
     * @return ModelCollection | ApplicationTag[]
     */
    public function getApplicationTag($filter=[])
    {
        return $this->getRelatedMany(ApplicationTag::class, $filter);
    }

    /**
     * @param array $filter
     * @return Application[]
     */
    public function getApplications($filter=[])
    {
        $applications = Application::where(
            'id',
            $this->getApplicationTag()->getIterator()->select('application_id')
        );
        if($filter) {
            $applications->where($filter);
        }
        return $applications->all();
    }

}

class ApplicationTag extends Model
{
    protected $table = 'application_tag';

    public function getApplication()
    {
        return $this->getRelated(Application::class);
    }

    public function getTag()
    {
        return $this->getRelated(Tag::class);
    }

}
