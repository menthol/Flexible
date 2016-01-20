Introduction
------------

Flexible is a Laravel package that aims to seamlessly integrate Elasticsearch functionality with the Eloquent ORM.

Features
--------

  - Plug 'n Play searching functionality for Eloquent models
  - Automatic creation/indexing based on Eloquent model properties and relations
  - Aggregations, Suggestions, Autocomplete, Highlighting, etc. It's all there!
  - Load Eloquent models based on Elasticsearch queries
  - Automatic reindexing on updates of (related) Eloquent models

Installation
------------

Add Flexible to your composer.json file:

```"menthol/flexible": "dev-master"```

Add the service provider to your Laravel application config:

```PHP
'Menthol\Flexible\FlexibleServiceProvider'
```

Credits
-------
This package is very much inspired by these excellent packages that already exist.

* [Larasearch](https://github.com/iverberk/larasearch)
* [Searchkick](https://github.com/ankane/searchkick)
* [Elasticsearch Rails](https://github.com/elasticsearch/elasticsearch-rails)
