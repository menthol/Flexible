<?php namespace Menthol\Flexible\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
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
        $directoryModels = [];

        if ($directories = $this->option('dir')) {
            Utils::findSearchableModels($directories);

            foreach ($directoryModels as $model) {
                $instance = $this->getModelInstance($model);
                $this->reindexModel($instance);
            }
        }

        if (empty($directoryModels)) {
            $this->info('No models found.');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('model', InputOption::VALUE_OPTIONAL, 'Eloquent model to reindex', null)
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Directory to scan for searchable models', null),
            array('batch', null, InputOption::VALUE_OPTIONAL, 'The number of records to index in a single batch', 750),
        );
    }

    /**
     * Reindex a model to Elasticsearch
     *
     * @param Model $model
     */
    protected function reindexModel(Model $model)
    {
        $mapping = $this->option('mapping') ? json_decode(File::get($this->option('mapping')), true) : null;

        $this->info('---> Reindexing ' . get_class($model));

        $model->reindex(
            $this->option('relations'),
            $this->option('batch'),
            $mapping,
            function ($batch) {
                $this->info("* Batch ${batch}");
            }
        );
    }

    /**
     * Simple method to create instances of classes on the fly
     * It's primarily here to enable unit-testing
     *
     * @param string $model
     */
    protected function getModelInstance($model)
    {
        return new $model;
    }

}
