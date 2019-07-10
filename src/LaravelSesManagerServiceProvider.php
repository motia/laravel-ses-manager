<?php

namespace Motia\LaravelSesManager;

use Illuminate\Support\ServiceProvider;

class LaravelSesManagerServiceProvider extends ServiceProvider {
	public function register() { 
		$this->loadMigrationsFrom(__DIR__.'/../migrations');

	    $this->mergeConfigFrom(
	      $this->getConfigFilePath(),
	      'ses-manager'
	    );
  }
  
  public function boot()
  {
    $this->publishes([
        $this->getConfigFilePath() => config_path('ses-manager.php'),
    ]);
  }

  private function getConfigFilePath()
  {
    return __DIR__ . '/config.php';
  }
}
