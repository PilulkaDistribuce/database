# Pilulka database package

[![Author](https://img.shields.io/badge/author-@martinkrizan-blue.svg?style=flat-square)](https://twitter.com/martinkrizan)
[![Build Status](https://img.shields.io/travis/pilulkacz/database/master.svg?style=flat-square)](https://travis-ci.org/pilulkacz/database)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7c094887-fde9-40c8-bd46-f2cdcadc53c8/big.png)](https://insight.sensiolabs.com/projects/7c094887-fde9-40c8-bd46-f2cdcadc53c8)

Pilulka Database is database abstraction layer based on the of NotORM library. Let you to create models to use them in domain logic and use NotORM as clever query builder.
 
## Usage example

Let's define some models
```php
class Application extends Model
{
    protected $table = 'application';

    public function getAuthor()
    {
        return $this->getRelated(Author::class);
    }

    public function getApplicationTags($filter=[])
    {
        return $this->getRelatedMany(ApplicationTag::class, $filter);
    }

// ...

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

// ...

}   

class Tag extends Model
{
    protected $table = 'tag';

// ...

}
```
and now use them
```php
// very simple usage
$applications = Application::where('author.full_name like ?', '%David');
foreach($applications as $application) {
    echo "{$application->title} created by {$application->getAuthor()->name}\n";
}
```
this usage makes only 2 requests to database (no matter how many applications are loaded).

## Documentation

Coming soon. 
