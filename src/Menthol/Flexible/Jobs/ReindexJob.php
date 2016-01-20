<?php namespace Menthol\Flexible\Jobs;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Jobs\Job;
use Exception;

/**
 * Class ReindexJob
 *
 * @package Menthol\Flexible\Jobs
 */
class ReindexJob {

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Application $app
     * @param Repository $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function fire(Job $job, $models)
    {
        $loggerContainerBinding = $this->config->get('flexible.logger');
        $logger = $this->app->make($loggerContainerBinding);

        foreach ($models as $model)
        {
            list($class, $id) = explode(':', $model);

            $logger->info('Indexing ' . $class . ' with ID: ' . $id);

            try
            {
                $model = $class::findOrFail($id);
                $model->refreshDoc($model);
            } catch (Exception $e)
            {
                $logger->error('Indexing ' . $class . ' with ID: ' . $id . ' failed: ' . $e->getMessage());

                $job->release(60);
            }
        }

        $job->delete();
    }

}