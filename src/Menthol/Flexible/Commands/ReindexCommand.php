<?php namespace Menthol\Flexible\Commands;

use Illuminate\Console\Command;
use Menthol\Flexible\Utilities\ModelDiscovery;
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
            ['batch', null, InputOption::VALUE_OPTIONAL, 'The number of records to index in a single batch', 750],
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
    }

}
