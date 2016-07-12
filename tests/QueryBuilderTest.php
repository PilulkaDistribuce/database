<?php

use Pilulka\Database\QueryBuilder;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  QueryBuilder */
    private $builder;

    public function setUp()
    {
        $this->builder = Application::getQuery();
    }

    public function testSimpleSelect()
    {
        $this->assertEquals(
            "SELECT * FROM application",
            (string)$this->builder->table('application')
        );
    }



}