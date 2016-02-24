<?php

return [

    'elasticsearch' => [

        /**
         * Configuration array for the low-level Elasticsearch client. See
         * http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_configuration.html
         * for additional options.
         */

        'params' => [
            'hosts' => ['localhost:9200'],
            'sniffOnStart' => false,
            'retries' => null,
        ],

        'analyzers' => [
            'autocomplete',
            'suggest',
            'text_start',
            'text_middle',
            'text_end',
            'word_start',
            'word_middle',
            'word_end'
        ],

        /**
         * Default configuration array for Elasticsearch indices based on Eloquent models
         * CREDIT: Analyzers, Tokenizers and Filters are copied and renamed from the Searchkick
         * project to get started quickly.
         */

        'defaults' => [
            'index' => [
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
                ],
                'mappings' => [
                    '_default_' => [
                        # https://gist.github.com/kimchy/2898285
                        'dynamic_templates' => [
                            [
                                'string_template' => [
                                    'match' => '*',
                                    'match_mapping_type' => 'string',
                                    'mapping' => [
                                        # http://www.elasticsearch.org/guide/reference/mapping/multi-field-type/
                                        'type' => 'multi_field',
                                        'fields' => [
                                            # analyzed field must be the default field for include_in_all
                                            # http://www.elasticsearch.org/guide/reference/mapping/multi-field-type/
                                            # however, we can include the not_analyzed field in _all
                                            # and the _all index analyzer will take care of it
                                            '{name}' => ['type' => 'string', 'index' => 'not_analyzed'],
                                            'analyzed' => ['type' => 'string', 'index' => 'analyzed']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],

        'index_prefix' => ''
    ],
];
