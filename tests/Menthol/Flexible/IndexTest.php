<?php namespace Menthol\Flexible;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Config;
use Mockery as m;
use AspectMock\Test as am;

class IndexTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        m::close();
        am::clean();
    }

    /**
     * @test
     */
    public function it_should_import()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         */
        list($index) = $this->getMocks();
        $test = $this;

        /**
         *
         * Expectation
         *
         */
        /* @var \Mockery\Mock $model */
        $model = m::mock('Illuminate\Database\Eloquent\Model')->makePartial();

        $model->shouldReceive('with->skip->take->get')
            ->twice()
            ->andReturn([
                $model, $model, $model
            ], []);

        $model->shouldReceive('getEsId')
            ->times(3)
            ->andReturn(1);

        $model->shouldReceive('transform')
            ->times(3)
            ->andReturn(['mock', 'data']);

        $index->shouldReceive('bulk')
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->import($model, [], 750, function ($batch) use ($test)
        {
            $test->assertEquals(1, $batch);
        });
    }

    /**
     * @test
     */
    public function it_should_set_name()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         */
        list($index) = $this->getMocks('bar_');

        /**
         *
         * Assertion
         *
         */
        $this->assertEquals($index, $index->setName('Mock'));
        $this->assertEquals('bar_mock', $index->getName());
    }

    /**
     * @test
     */
    public function it_should_only_prepend_prefix_once()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         */
        list($index) = $this->getMocks('baz_');

        /**
         * Assertions
         */
        $this->assertEquals($index, $index->setName('baz_MockMe'));
        $this->assertEquals('baz_mockme', $index->getName());
    }

    /**
     * @test
     */
    public function it_should_get_name()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         */
        list($index) = $this->getMocks();

        /**
         *
         * Assertion
         *
         */
        $this->assertEquals('husband', $index->getName());
    }

    /**
     * @test
     */
    public function it_should_create_an_index()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();
        $test = $this;

        /**
         *
         * Expectation
         *
         */
        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.analyzers')
            ->andReturn([
                'autocomplete',
                'suggest',
                'text_start',
                'text_middle',
                'text_end',
                'word_start',
                'word_middle',
                'word_end'
            ]);

        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.defaults.index')
            ->andReturn([
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'flexible_keyword' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "flexible_stemmer"]
                            ],
                            'default_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_index_shingle", "flexible_stemmer"]
                            ],
                            'flexible_search' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_search_shingle", "flexible_stemmer"]
                            ],
                            'flexible_search2' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_stemmer"]
                            ],
                            'flexible_autocomplete_index' => [
                                'type' => "custom",
                                'tokenizer' => "flexible_autocomplete_ngram",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_autocomplete_search' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_word_search' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_suggest_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_suggest_shingle"]
                            ],
                            'flexible_text_start_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "flexible_edge_ngram"]
                            ],
                            'flexible_text_middle_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "flexible_ngram"]
                            ],
                            'flexible_text_end_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "reverse", "flexible_edge_ngram", "reverse"]
                            ],
                            'flexible_word_start_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_edge_ngram"]
                            ],
                            'flexible_word_middle_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_ngram"]
                            ],
                            'flexible_word_end_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "reverse", "flexible_edge_ngram", "reverse"]
                            ]
                        ],
                        'filter' => [
                            'flexible_index_shingle' => [
                                'type' => "shingle",
                                'token_separator' => ""
                            ],
                            'flexible_search_shingle' => [
                                'type' => "shingle",
                                'token_separator' => "",
                                'output_unigrams' => false,
                                'output_unigrams_if_no_shingles' => true
                            ],
                            'flexible_suggest_shingle' => [
                                'type' => "shingle",
                                'max_shingle_size' => 5
                            ],
                            'flexible_edge_ngram' => [
                                'type' => "edgeNGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ],
                            'flexible_ngram' => [
                                'type' => "nGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ],
                            'flexible_stemmer' => [
                                'type' => "snowball",
                                'language' => "English"
                            ]
                        ],
                        'tokenizer' => [
                            'flexible_autocomplete_ngram' => [
                                'type' => "edgeNGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ]
                        ]
                    ]
                ]]);

        $client->shouldReceive('indices->create')
            ->andReturnUsing(function ($params) use ($test)
            {
                $test->assertEquals(json_decode(
                        '{
                          "index": "husband",
                          "body": {
                            "settings": {
                              "number_of_shards": 1,
                              "number_of_replicas": 0,
                              "analysis": {
                                "analyzer": {
                                  "flexible_keyword": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "default_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_index_shingle",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_search": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_search_shingle",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_search2": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_autocomplete_index": {
                                    "type": "custom",
                                    "tokenizer": "flexible_autocomplete_ngram",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_autocomplete_search": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_word_search": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_suggest_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_suggest_shingle"
                                    ]
                                  },
                                  "flexible_text_start_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_edge_ngram"
                                    ]
                                  },
                                  "flexible_text_middle_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_ngram"
                                    ]
                                  },
                                  "flexible_text_end_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "reverse",
                                      "flexible_edge_ngram",
                                      "reverse"
                                    ]
                                  },
                                  "flexible_word_start_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_edge_ngram"
                                    ]
                                  },
                                  "flexible_word_middle_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_ngram"
                                    ]
                                  },
                                  "flexible_word_end_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "reverse",
                                      "flexible_edge_ngram",
                                      "reverse"
                                    ]
                                  }
                                },
                                "filter": {
                                  "flexible_index_shingle": {
                                    "type": "shingle",
                                    "token_separator": ""
                                  },
                                  "flexible_search_shingle": {
                                    "type": "shingle",
                                    "token_separator": "",
                                    "output_unigrams": false,
                                    "output_unigrams_if_no_shingles": true
                                  },
                                  "flexible_suggest_shingle": {
                                    "type": "shingle",
                                    "max_shingle_size": 5
                                  },
                                  "flexible_edge_ngram": {
                                    "type": "edgeNGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  },
                                  "flexible_ngram": {
                                    "type": "nGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  },
                                  "flexible_stemmer": {
                                    "type": "snowball",
                                    "language": "English"
                                  }
                                },
                                "tokenizer": {
                                  "flexible_autocomplete_ngram": {
                                    "type": "edgeNGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  }
                                }
                              }
                            },
                            "mappings": {
                              "_default_": {
                                "properties": {
                                  "name": {
                                    "type": "multi_field",
                                    "fields": {
                                      "name": {
                                        "type": "string",
                                        "index": "not_analyzed"
                                      },
                                      "analyzed": {
                                        "type": "string",
                                        "index": "analyzed"
                                      },
                                      "autocomplete": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_autocomplete_index"
                                      },
                                      "suggest": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_suggest_index"
                                      },
                                      "text_start": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_start_index"
                                      },
                                      "text_middle": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_middle_index"
                                      },
                                      "text_end": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_end_index"
                                      },
                                      "word_start": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_start_index"
                                      },
                                      "word_middle": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_middle_index"
                                      },
                                      "word_end": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_end_index"
                                      }
                                    }
                                  },
                                  "wife": {
                                    "type": "object",
                                    "properties": {
                                      "name": {
                                        "type": "multi_field",
                                        "fields": {
                                          "name": {
                                            "type": "string",
                                            "index": "not_analyzed"
                                          },
                                          "analyzed": {
                                            "type": "string",
                                            "index": "analyzed"
                                          },
                                          "autocomplete": {
                                            "type": "string",
                                            "index": "analyzed",
                                            "analyzer": "flexible_autocomplete_index"
                                          }
                                        }
                                      },
                                      "children": {
                                        "type": "object",
                                        "properties": {
                                          "name": {
                                            "type": "multi_field",
                                            "fields": {
                                              "name": {
                                                "type": "string",
                                                "index": "not_analyzed"
                                              },
                                              "analyzed": {
                                                "type": "string",
                                                "index": "analyzed"
                                              },
                                              "text_start": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_start_index"
                                              },
                                              "text_middle": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_middle_index"
                                              },
                                              "text_end": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_end_index"
                                              },
                                              "word_start": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_start_index"
                                              },
                                              "word_middle": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_middle_index"
                                              },
                                              "word_end": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_end_index"
                                              }
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            },
                            "index": "husband",
                            "type": "Husband"
                          }
                        }', true)
                    ,
                    $params);
            });

        $proxy->shouldReceive('getType')->andReturn('Husband');
        $proxy->shouldReceive('getConfig')->andReturn([
            'autocomplete' => ['name', 'wife.name'],
            'suggest' => ['name'],
            'text_start' => ['name', 'wife.children.name'],
            'text_middle' => ['name', 'wife.children.name'],
            'text_end' => ['name', 'wife.children.name'],
            'word_start' => ['name', 'wife.children.name'],
            'word_middle' => ['name', 'wife.children.name'],
            'word_end' => ['name', 'wife.children.name']
        ]);

        /**
         *
         * Assertion
         *
         */
        $index->create();
    }

    /**
     * @test
     */
    public function it_should_create_an_index_with_a_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');
        $test = $this;

        /**
         *
         * Expectation
         *
         */
        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.analyzers')
            ->andReturn([
                'autocomplete',
                'suggest',
                'text_start',
                'text_middle',
                'text_end',
                'word_start',
                'word_middle',
                'word_end'
            ]);

        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.defaults.index')
            ->andReturn([
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'flexible_keyword' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "flexible_stemmer"]
                            ],
                            'default_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_index_shingle", "flexible_stemmer"]
                            ],
                            'flexible_search' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_search_shingle", "flexible_stemmer"]
                            ],
                            'flexible_search2' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["standard", "lowercase", "asciifolding", "flexible_stemmer"]
                            ],
                            'flexible_autocomplete_index' => [
                                'type' => "custom",
                                'tokenizer' => "flexible_autocomplete_ngram",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_autocomplete_search' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_word_search' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding"]
                            ],
                            'flexible_suggest_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_suggest_shingle"]
                            ],
                            'flexible_text_start_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "flexible_edge_ngram"]
                            ],
                            'flexible_text_middle_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "flexible_ngram"]
                            ],
                            'flexible_text_end_index' => [
                                'type' => "custom",
                                'tokenizer' => "keyword",
                                'filter' => ["lowercase", "asciifolding", "reverse", "flexible_edge_ngram", "reverse"]
                            ],
                            'flexible_word_start_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_edge_ngram"]
                            ],
                            'flexible_word_middle_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "flexible_ngram"]
                            ],
                            'flexible_word_end_index' => [
                                'type' => "custom",
                                'tokenizer' => "standard",
                                'filter' => ["lowercase", "asciifolding", "reverse", "flexible_edge_ngram", "reverse"]
                            ]
                        ],
                        'filter' => [
                            'flexible_index_shingle' => [
                                'type' => "shingle",
                                'token_separator' => ""
                            ],
                            'flexible_search_shingle' => [
                                'type' => "shingle",
                                'token_separator' => "",
                                'output_unigrams' => false,
                                'output_unigrams_if_no_shingles' => true
                            ],
                            'flexible_suggest_shingle' => [
                                'type' => "shingle",
                                'max_shingle_size' => 5
                            ],
                            'flexible_edge_ngram' => [
                                'type' => "edgeNGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ],
                            'flexible_ngram' => [
                                'type' => "nGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ],
                            'flexible_stemmer' => [
                                'type' => "snowball",
                                'language' => "English"
                            ]
                        ],
                        'tokenizer' => [
                            'flexible_autocomplete_ngram' => [
                                'type' => "edgeNGram",
                                'min_gram' => 1,
                                'max_gram' => 50
                            ]
                        ]
                    ]
                ]]);


        $client->shouldReceive('indices->create')
            ->andReturnUsing(function ($params) use ($test)
            {
                $test->assertEquals(json_decode(
                        '{
                          "index": "bar_husband",
                          "body": {
                            "settings": {
                              "number_of_shards": 1,
                              "number_of_replicas": 0,
                              "analysis": {
                                "analyzer": {
                                  "flexible_keyword": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "default_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_index_shingle",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_search": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_search_shingle",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_search2": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "standard",
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_stemmer"
                                    ]
                                  },
                                  "flexible_autocomplete_index": {
                                    "type": "custom",
                                    "tokenizer": "flexible_autocomplete_ngram",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_autocomplete_search": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_word_search": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding"
                                    ]
                                  },
                                  "flexible_suggest_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_suggest_shingle"
                                    ]
                                  },
                                  "flexible_text_start_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_edge_ngram"
                                    ]
                                  },
                                  "flexible_text_middle_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_ngram"
                                    ]
                                  },
                                  "flexible_text_end_index": {
                                    "type": "custom",
                                    "tokenizer": "keyword",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "reverse",
                                      "flexible_edge_ngram",
                                      "reverse"
                                    ]
                                  },
                                  "flexible_word_start_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_edge_ngram"
                                    ]
                                  },
                                  "flexible_word_middle_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "flexible_ngram"
                                    ]
                                  },
                                  "flexible_word_end_index": {
                                    "type": "custom",
                                    "tokenizer": "standard",
                                    "filter": [
                                      "lowercase",
                                      "asciifolding",
                                      "reverse",
                                      "flexible_edge_ngram",
                                      "reverse"
                                    ]
                                  }
                                },
                                "filter": {
                                  "flexible_index_shingle": {
                                    "type": "shingle",
                                    "token_separator": ""
                                  },
                                  "flexible_search_shingle": {
                                    "type": "shingle",
                                    "token_separator": "",
                                    "output_unigrams": false,
                                    "output_unigrams_if_no_shingles": true
                                  },
                                  "flexible_suggest_shingle": {
                                    "type": "shingle",
                                    "max_shingle_size": 5
                                  },
                                  "flexible_edge_ngram": {
                                    "type": "edgeNGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  },
                                  "flexible_ngram": {
                                    "type": "nGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  },
                                  "flexible_stemmer": {
                                    "type": "snowball",
                                    "language": "English"
                                  }
                                },
                                "tokenizer": {
                                  "flexible_autocomplete_ngram": {
                                    "type": "edgeNGram",
                                    "min_gram": 1,
                                    "max_gram": 50
                                  }
                                }
                              }
                            },
                            "mappings": {
                              "_default_": {
                                "properties": {
                                  "name": {
                                    "type": "multi_field",
                                    "fields": {
                                      "name": {
                                        "type": "string",
                                        "index": "not_analyzed"
                                      },
                                      "analyzed": {
                                        "type": "string",
                                        "index": "analyzed"
                                      },
                                      "autocomplete": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_autocomplete_index"
                                      },
                                      "suggest": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_suggest_index"
                                      },
                                      "text_start": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_start_index"
                                      },
                                      "text_middle": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_middle_index"
                                      },
                                      "text_end": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_text_end_index"
                                      },
                                      "word_start": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_start_index"
                                      },
                                      "word_middle": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_middle_index"
                                      },
                                      "word_end": {
                                        "type": "string",
                                        "index": "analyzed",
                                        "analyzer": "flexible_word_end_index"
                                      }
                                    }
                                  },
                                  "wife": {
                                    "type": "object",
                                    "properties": {
                                      "name": {
                                        "type": "multi_field",
                                        "fields": {
                                          "name": {
                                            "type": "string",
                                            "index": "not_analyzed"
                                          },
                                          "analyzed": {
                                            "type": "string",
                                            "index": "analyzed"
                                          },
                                          "autocomplete": {
                                            "type": "string",
                                            "index": "analyzed",
                                            "analyzer": "flexible_autocomplete_index"
                                          }
                                        }
                                      },
                                      "children": {
                                        "type": "object",
                                        "properties": {
                                          "name": {
                                            "type": "multi_field",
                                            "fields": {
                                              "name": {
                                                "type": "string",
                                                "index": "not_analyzed"
                                              },
                                              "analyzed": {
                                                "type": "string",
                                                "index": "analyzed"
                                              },
                                              "text_start": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_start_index"
                                              },
                                              "text_middle": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_middle_index"
                                              },
                                              "text_end": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_text_end_index"
                                              },
                                              "word_start": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_start_index"
                                              },
                                              "word_middle": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_middle_index"
                                              },
                                              "word_end": {
                                                "type": "string",
                                                "index": "analyzed",
                                                "analyzer": "flexible_word_end_index"
                                              }
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            },
                            "index": "bar_husband",
                            "type": "Husband"
                          }
                        }', true)
                    ,
                    $params);
            });

        $proxy->shouldReceive('getType')->andReturn('Husband');
        $proxy->shouldReceive('getConfig')->andReturn([
            'autocomplete' => ['name', 'wife.name'],
            'suggest' => ['name'],
            'text_start' => ['name', 'wife.children.name'],
            'text_middle' => ['name', 'wife.children.name'],
            'text_end' => ['name', 'wife.children.name'],
            'word_start' => ['name', 'wife.children.name'],
            'word_middle' => ['name', 'wife.children.name'],
            'word_end' => ['name', 'wife.children.name']
        ]);

        /**
         *
         * Assertion
         *
         */
        $index->create();
    }

    /**
     * @test
     */
    public function it_should_delete_an_index()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->delete')
            ->with(['index' => 'husband'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->delete();
    }

    /**
     * @test
     */
    public function it_should_delete_an_index_with_a_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->delete')
            ->with(['index' => 'bar_husband'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->delete();
    }

    /**
     * @test
     */
    public function it_should_check_that_an_index_exists()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->exists')
            ->with(['index' => 'husband'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->exists();
    }

    /**
     * @test
     */
    public function it_should_check_that_an_index_exists_with_a_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->exists')
            ->with(['index' => 'bar_husband'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->exists();
    }

    /**
     * @test
     */
    public function it_should_check_that_an_alias_exists()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->existsAlias')
            ->with(['name' => 'Alias'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->aliasExists('Alias');
    }

    /**
     * @test
     */
    public function it_should_check_that_an_alias_exists_with_a_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->existsAlias')
            ->twice()
            ->with(['name' => 'bar_alias'])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->aliasExists('alias');
        $index->aliasExists('bar_alias');
    }

    /**
     * @test
     */
    public function it_should_store_a_record()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('index')
            ->with([
                'index' => 'husband',
                'type' => 'Husband',
                'id' => 1,
                'body' => 'data'
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->store([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
    }

    /**
     * @test
     */
    public function it_should_store_a_record_with_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('index')
            ->twice()
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'id' => 1,
                'body' => 'data'
            ])
            ->andReturn();
        /**
         *
         * Assertion
         *
         */
        $index->store([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
        $index->setName('bar_Husband');
        $index->store([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
    }

    /**
     * @test
     */
    public function it_should_retrieve_a_record()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('get')
            ->with([
                'index' => 'husband',
                'type' => 'Husband',
                'id' => 1,
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->retrieve([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
    }

    /**
     * @test
     */
    public function it_should_retrieve_a_record_iwth_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('get')
            ->twice()
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'id' => 1,
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->retrieve([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
        $index->setName('bar_Husband');
        $index->retrieve([
            'type' => 'Husband',
            'id' => 1,
            'data' => 'data'
        ]);
    }

    /**
     * @test
     */
    public function it_should_remove_a_record()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('delete')
            ->with([
                'index' => 'husband',
                'type' => 'Husband',
                'id' => 1
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->remove([
            'type' => 'Husband',
            'id' => 1
        ]);
    }

    /**
     * @test
     */
    public function it_should_remove_a_record_with_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('delete')
            ->twice()
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'id' => 1
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->remove([
            'type' => 'Husband',
            'id' => 1
        ]);
        $index->setName('bar_Husband');
        $index->remove([
            'type' => 'Husband',
            'id' => 1
        ]);
    }

    /**
     * @test
     */
    public function it_should_inspect_tokens()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->analyze')
            ->with([
                'index' => 'husband',
                'text' => 'text',
                'option1' => 1,
                'option2' => 2
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->tokens('text', [
            'option1' => 1,
            'option2' => 2
        ]);
    }

    /**
     * @test
     */
    public function it_should_inspect_tokens_with_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->analyze')
            ->twice()
            ->with([
                'index' => 'bar_husband',
                'text' => 'text',
                'option1' => 1,
                'option2' => 2
            ])
            ->andReturn();

        /**
         *
         * Assertion
         *
         */
        $index->tokens('text', [
            'option1' => 1,
            'option2' => 2
        ]);
        $index->setName('bar_Husband');
        $index->tokens('text', [
            'option1' => 1,
            'option2' => 2
        ]);
    }

    /**
     * @test
     */
    public function it_should_get_and_set_params()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        $index->setParams(['mock' => 'data']);
        $this->assertEquals(['mock' => 'data'], $index->getParams());
    }

    /**
     * @test
     * @expectedException \Menthol\Flexible\Exceptions\ImportException
     */
    public function it_should_store_a_records_in_bulk_with_errors()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('getType')->andReturn('Husband');

        $client->shouldReceive('bulk')
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'body' => 'records'
            ])
            ->andReturn([
                'errors' => true,
                'items' => [
                    [
                        'index' => [
                            'error' => true
                        ]
                    ]
                ]
            ]);

        /**
         *
         * Assertion
         *
         */
        $index->bulk('records');
    }

    /**
     * @test
     * @expectedException \Menthol\Flexible\Exceptions\ImportException
     */
    public function it_should_store_records_having_prefix_in_bulk_with_errors()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('getType')->andReturn('Husband');

        $client->shouldReceive('bulk')
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'body' => 'records'
            ])
            ->andReturn([
                'errors' => true,
                'items' => [
                    [
                        'index' => [
                            'error' => true
                        ]
                    ]
                ]
            ]);

        /**
         *
         * Assertion
         *
         */
        $index->bulk('records');
        $index->setName('bar_Husband');
        $index->bulk('records');;
    }

    /**
     * @test
     * @expectedException \Menthol\Flexible\Exceptions\ImportException
     */
    public function it_should_store_records_having_explicit_prefix_in_bulk_with_errors()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('getType')->andReturn('Husband');

        $client->shouldReceive('bulk')
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'body' => 'records'
            ])
            ->andReturn([
                'errors' => true,
                'items' => [
                    [
                        'index' => [
                            'error' => true
                        ]
                    ]
                ]
            ]);

        /**
         *
         * Assertion
         *
         */
        $index->setName('bar_Husband');
        $index->bulk('records');;
    }

    /**
     * @test
     */
    public function it_should_store_a_records_in_bulk_without_errors()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('getType')->andReturn('Husband');

        $client->shouldReceive('bulk')
            ->with([
                'index' => 'husband',
                'type' => 'Husband',
                'body' => 'records'
            ])
            ->andReturn([
                'errors' => false
            ]);

        /**
         *
         * Assertion
         *
         */
        $index->bulk('records');
    }

    /**
     * @test
     */
    public function it_should_store_a_records_having_index_prefix_in_bulk_without_errors()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('getType')->andReturn('Husband');

        $client->shouldReceive('bulk')
            ->twice()
            ->with([
                'index' => 'bar_husband',
                'type' => 'Husband',
                'body' => 'records'
            ])
            ->andReturn([
                'errors' => false
            ]);

        /**
         *
         * Assertion
         *
         */
        $index->bulk('records');
        $index->setName('bar_Husband');
        $index->bulk('records');
    }

    /**
     * @test
     */
    public function it_should_clean_old_indices()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->getAliases')
            ->andReturn([
                'index_123456789101112' => [
                    'aliases' => []
                ]
            ]);

        $client->shouldReceive('indices->delete')
            ->with([
                'index' => 'index_123456789101112'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::clean('index');
    }

    /**
     * @test
     */
    public function it_should_clean_old_indices_with_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->getAliases')
            ->twice()
            ->andReturn([
                'bar_index_123456789101112' => [
                    'aliases' => []
                ]
            ]);

        $client->shouldReceive('indices->delete')
            ->twice()
            ->with([
                'index' => 'bar_index_123456789101112'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::clean('index');
        Index::clean('bar_index');
    }

    /**
     * @test
     */
    public function it_should_update_aliases()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->updateAliases')
            ->with([
                'body' => ['actions' => []]
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::updateAliases(['actions' => []]);
    }

    /**
     * @test
     */
    public function it_should_update_aliases_with_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->updateAliases')
            ->twice()
            ->with([
                'body' => ['actions' => [['add' => ['index' => 'bar_Husband', 'alias' => 'bar_Father']]]]
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::updateAliases(['actions' => [['add' => ['index' => 'Husband', 'alias' => 'Father']]]]);
        Index::updateAliases(['actions' => [['add' => ['index' => 'bar_Husband', 'alias' => 'bar_Father']]]]);
    }

    /**
     * @test
     */
    public function it_should_get_aliases()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->getAlias')
            ->with([
                'name' => 'mock'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::getAlias('mock');
    }

    /**
     * @test
     */
    public function it_should_get_aliases_with_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->getAlias')
            ->twice()
            ->with([
                'name' => 'bar_mock'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::getAlias('mock');
        Index::getAlias('bar_mock');
    }

    /**
     * @test
     */
    public function it_should_refresh()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks();

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->refresh')
            ->with([
                'index' => 'mock'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::refresh('mock');
    }

    /**
     * @test
     */
    public function it_should_refresh_with_an_index_prefix()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $index
         * @var \Mockery\Mock $proxy
         * @var \Mockery\Mock $client
         */
        list($index, $proxy, $client) = $this->getMocks('bar_');

        // Mock the self::$client variable
        am::double('Menthol\Flexible\Index', ['self::$client' => $client]);

        /**
         *
         * Expectation
         *
         */
        $client->shouldReceive('indices->refresh')
            ->twice()
            ->with([
                'index' => 'bar_mock'
            ]);

        /**
         *
         * Assertion
         *
         */
        Index::refresh('mock');
        Index::refresh('bar_mock');
    }

    /**
     * Construct an Index mock
     *
     * @return array
     */
    private function getMocks($index_prefix = '')
    {
        /**
         *
         * Expectation
         *
         */
        Facade::clearResolvedInstances();

        $client = m::mock('Elasticsearch\Client');

        App::shouldReceive('make')
            ->with('Elasticsearch')
            ->andReturn($client);

        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.index_prefix', '')
            ->andReturn($index_prefix);

        $proxy = m::mock('Menthol\Flexible\Proxy');
        $proxy->shouldReceive('getModel->getTable')
            ->andReturn('Husband');

        $index = m::mock('Menthol\Flexible\Index', [$proxy], [m::BLOCKS => ['setName', 'setProxy']])->makePartial();

        return [$index, $proxy, $client];
    }

}
