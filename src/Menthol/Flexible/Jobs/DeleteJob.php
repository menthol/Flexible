<?php namespace Menthol\Flexible\Jobs;

use Illuminate\Foundation\Application;
use Illuminate\Queue\Jobs\Job;
use Menthol\Flexible\Config;

/**
 * Class DeleteJob
 *
 * @package Menthol\Flexible\Jobs
 */
class DeleteJob {

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @param Application $app
     * @param Config
     */
    public function __construct(Application $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param Job $job
     * @param mixed $models
     */
    public function fire(Job $job, $models)
    {
        $loggerContainerBinding = $this->config->get('logger', 'menthol.flexible.logger');
        $logger = $this->app->make($loggerContainerBinding);

        foreach ($models as $model)
        {
            list($class, $id) = explode(':', $model);

            $logger->info('Deleting ' . $class . ' with ID: ' . $id . ' from Elasticsearch');

            $model = new $class;

            $model->deleteDoc($id);
        }

        $job->delete();
    }

}
