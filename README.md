# Flexible [![Build Status](https://travis-ci.org/menthol/Flexible.svg?branch=master)](https://travis-ci.org/menthol/Flexible) [![Latest Stable Version](https://poser.pugx.org/menthol/flexible/v/stable)](https://packagist.org/packages/menthol/flexible) [![Total Downloads](https://poser.pugx.org/menthol/flexible/downloads)](https://packagist.org/packages/menthol/flexible) [![Latest Unstable Version](https://poser.pugx.org/menthol/flexible/v/unstable)](https://packagist.org/packages/menthol/flexible) [![License](https://poser.pugx.org/menthol/flexible/license)](https://packagist.org/packages/menthol/flexible)

Flexible is a Laravel 4 and 5 package that aims to seamlessly integrate Elasticsearch functionality with the Eloquent ORM.

# WIP
The API can be changed (and will) without warning until the first beta tag.


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
