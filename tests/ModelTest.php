<?php

require_once __DIR__ . "/test_base.php";

class ModelTest extends \PHPUnit_Framework_TestCase
{

    public function testDelete()
    {
        Application::first()->delete();
    }

    public function testFetchOne()
    {
        $this->assertInstanceOf(Application::class, Application::first());
        $this->assertInstanceOf(Author::class, Author::first());
        $this->assertInstanceOf(Tag::class, Tag::first());
        $this->assertInstanceOf(ApplicationTag::class, ApplicationTag::first());
    }

    public function testManyToMany()
    {
        foreach (Tag::all() as $tag) {
            /** @var Tag $tag */
            foreach ($tag->getApplications() as $application) {
                $this->assertInstanceOf(Application::class, $application);
                foreach ($application->getTags() as $applicationTag) {
                    $this->assertInstanceOf(Tag::class, $applicationTag);
                }
            }
        }
    }

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

    public function testUpdate()
    {
        $title = md5(microtime());
        $application = Application::first();
        $application->title = $title;
        $application->maintainer_id = mt_rand(11, 12);
        $application->save();
        $this->assertEquals($title, $application->title);
    }

    public function testSave()
    {
        $application = new Application();
        $title = md5(microtime());
        $application->save([
            'author_id' => mt_rand(11, 12),
            'maintainer_id' => mt_rand(11, 12),
            'title' => $title,
            'slogan' => 'Pilulka rulez',
        ]);
        $this->assertEquals($title, $application->title);
    }

}