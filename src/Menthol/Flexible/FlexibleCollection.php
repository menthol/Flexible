<?php namespace Menthol\Flexible;


class FlexibleCollection extends \Illuminate\Database\Eloquent\Collection
{
    private $flexibleRawResults = null;

    public function setFlexibleRawResults($flexibleRawResults)
    {
        $this->flexibleRawResults = $flexibleRawResults;
    }

    public function getFlexibleRawResults()
    {
        return $this->flexibleRawResults;
    }

    public function getFlexibleAggregations()
    {
        return array_get($this->flexibleRawResults, 'aggregations');
    }

    public function getFlexibleAggregation($name)
    {
        if (isset($this->flexibleRawResults['aggregations'][$name])) {
            return $this->flexibleRawResults['aggregations'][$name];
        };
    }

    public function getFlexibleTotal() {
        if (isset($this->flexibleRawResults['hits']['total'])) {
            return $this->flexibleRawResults['hits']['total'];
        }

        return null;
    }
}
