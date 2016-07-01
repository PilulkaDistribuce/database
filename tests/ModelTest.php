<?php

require_once __DIR__ . "/test_base.php";

class ModelTest extends \PHPUnit_Framework_TestCase
{

    public function testRelation()
    {
        foreach (Application::all() as $application) {
            /** @var Application $application */
            $this->assertInstanceOf(Author::class, $application->getAuthor());
        }
    }

    public function testRelationVia()
    {
        foreach (Application::where('NOT maintainer_id', null)->all() as $application) {
            /** @var Application $application */
            $this->assertInstanceOf(Author::class, $application->getMaintainer());
        }
    }

    public function testManyRelation()
    {
        foreach (Author::all() as $author) {
            /** @var Author $author */
            foreach ($author->getApplications() as $application) {
                $this->assertInstanceOf(Application::class, $application);
            }
        }
    }

    public function testManyRelationMappedBy()
    {
        foreach (Author::all() as $author) {
            /** @var Author $author */
            foreach ($author->getMaintainedApplications() as $application) {
                $this->assertInstanceOf(Application::class, $application);
            }
        }
    }

    public function testSave()
    {
        $application = new Application();
        $title = md5(microtime());
        $application->save([
            'author_id' => 11,
            'maintainer_id' => 12,
            'title' => $title,
            'slogan' => 'Pilulka rulez',
        ]);
        $this->assertEquals($title, $application->title);
    }

}