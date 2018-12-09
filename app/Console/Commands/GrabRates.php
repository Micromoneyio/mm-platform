<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Components\Rater\Facade as Rater;

class GrabRates extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'rate:grab {--force}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Grab current currency rate';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$force = $this->option('force');
		try {
			Rater::updateRates(isset($force));
			$this->info('Successful rates updated');
		} catch (\Exception $exception) {
			/** @todo: log critical exception */
			$this->error('Exception: ' . $exception->getMessage());
		}

		return true;
	}
}
