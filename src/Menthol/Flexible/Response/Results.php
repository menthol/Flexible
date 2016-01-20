<?php namespace Menthol\Flexible\Response;

use Illuminate\Support\Collection;
use Menthol\Flexible\Response;

class Results extends Collection {

    /**
     * Contains an Elasticsearch response wrapper
     *
     * @var \Menthol\Flexible\Response
     */
    private $response;

    /**
     * Construct a collection of Result objects based on the hits
     * in the Elasticsearch response
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        parent::__construct(
            array_map(
                function ($hit)
                {
                    return new Result($hit);
                },
                $this->response->getHits()
            )
        );
    }

}
