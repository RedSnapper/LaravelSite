<?php

namespace App\Providers;

use App\ScoutEngines\ElasticsearchEngine;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Elasticsearch\ClientBuilder;
use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class ElasticsearchProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        resolve(EngineManager::class)->extend('elasticsearch', function($app) {

			$builder = $this->getBuilder();

			$handler = $this->getHandler();

			$handler && $builder->setHandler($handler);

			return new ElasticsearchEngine($builder->build(), config('scout.elasticsearch.index'));
        });
    }

    protected function getBuilder():ClientBuilder{
		return ElasticBuilder::create()
		  ->setHosts(config('scout.elasticsearch.hosts'));
	}


	protected function getHandler() {

		$connection = config('scout.elasticsearch.default');

		if ($connection == "aws") {

			$connection = config("scout.elasticsearch.connections.aws");

			$provider = CredentialProvider::fromCredentials(
			  new Credentials($connection['key'], $connection['secret'])
			);
			return new ElasticsearchPhpHandler($connection['region'], $provider);
		}
		return null;
	}


}
