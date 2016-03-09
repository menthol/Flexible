<?php namespace Menthol\Flexible\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;
use Menthol\Flexible\Utilities\ElasticSearchHelper;
use Menthol\Flexible\Utilities\ModelDiscovery;
use Menthol\Flexible\Utilities\QueryHelper;
use Menthol\Flexible\Utilities\TransformModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ReindexCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'flexible:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex Eloquent models to Elasticsearch.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $models = [];
        foreach ($this->argument('dir') as $dir) {
            $models = ModelDiscovery::discover(base_path($dir), $models, 'Menthol\Flexible\Traits\IndexableTrait');
        }

        if (count($models) === 0) {
            $this->info('No indexable models found.');

            return;
        }

        foreach ($models as $model) {
            $this->reindexModel($model);
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['dir', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Directory to scan for searchable models', null],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['batch', 'b', InputOption::VALUE_OPTIONAL, 'The number of records to index in a single batch', 750],
            ['simplified', 's', InputOption::VALUE_NONE, 'Use simplified batch display'],
        ];
    }

    /**
     * Reindex a model to Elasticsearch
     *
     * @param String $modelName
     */
    protected function reindexModel($modelName)
    {
        $this->info('---> Reindexing ' . $modelName);

        $this->output->write('<comment>Prepare index :</comment> ');
        ElasticSearchHelper::prepareIndex($modelName);
        $this->output->writeln("ok");

        $keys = QueryHelper::newQueryWithoutScopes($modelName, true, false)->lists('id');
        $numberOfModels = count($keys);
        $startTime = microtime(true);
        foreach (array_chunk($keys, $this->option('batch')) as $chunkDelta => $chuckKeys) {
            /** @var Model[]|IndexableTrait[]|Collection $models */
            $models = QueryHelper::findMany($modelName, $chuckKeys, true);
            $params = [];

            foreach ($models as $model) {
                if ($model->flexibleIsIndexable()) {
                    $params['body'][] = [
                        'index' => [
                            '_index' => $model->getFlexibleIndexName() . '_tmp',
                            '_type' => $model->getFlexibleType(),
                            '_id' => $model->getKey(),
                        ],
                    ];

                    $params['body'][] = TransformModel::transform($model);
                }
            }

            ElasticSearchHelper::bulk($params);

            $processed = min(($chunkDelta + 1) * $this->option('batch'), $numberOfModels);
            $percentage = $processed / $numberOfModels;
            $displayablePercentage = round($percentage * 100);

            if ($this->option('simplified')) {
                $this->output->writeln("Index <info>{$modelName}</info> {$displayablePercentage}% [{$processed} / {$numberOfModels}]");
            } else {
                $timeRemaining = ((microtime(true) - $startTime) / $percentage) - (microtime(true) - $startTime);
                $remainingString = Carbon::now()->addSeconds(round($timeRemaining))->diffForHumans(null, true);
                $this->output->write("\r\033[KIndex <info>{$modelName}</info> {$displayablePercentage}% [{$processed} / {$numberOfModels}] remaining {$remainingString}");
            }
        }

        if (!$this->option('simplified')) {
            $this->output->writeln("\r\033[KIndex <info>{$modelName}</info> {$displayablePercentage}% [{$processed} / {$numberOfModels}]");
            $this->output->writeln('');
        }
        $duration = Carbon::now()->addSeconds(round(microtime(true) - $startTime))->diffForHumans(null, true);
        $this->output->writeln("<info>Takes : {$duration}</info>");

        $this->output->write('<comment>Finalize index :</comment> ');
        ElasticSearchHelper::finalizeIndex($modelName);
        $this->output->writeln("ok\n");
    }

}
