<?php
	/**
	 * Created by PhpStorm.
	 * User: nikolkrash
	 * Date: 14.07.2018
	 * Time: 15:02
	 */

	namespace App\Console\Commands;

	use App\Events\User\UserUpdateBalanceEvent;
	use App\Exceptions\InvalidQueryRange;
	use App\Models\User;
	use App\Services\BalanceService;
	use App\Services\UserService;
	use Illuminate\Console\Command;


	class BalanceProcessor extends Command {


		/**
		 * @var UserService
		 */
		private $_userService;

		/**
		 * @var BalanceService
		 */
		private $_balanceService;

		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'balance:process';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Process check the balance of users.';

		/**
		 * BalanceProcessor constructor.
		 *
		 * @param UserService $_userService
		 * @param BalanceService $_balanceService
		 */
		public function __construct(UserService $_userService, BalanceService $_balanceService) {
			$this->_userService = $_userService;
			$this->_balanceService = $_balanceService;
			parent::__construct();
		}

		public function handle() {
			$this->info('Process check the balance of users.');
			try {
				/** @var User[] $users */
				$users = $this->_userService->loadUsersByParams([], null, null, null, 'customer');
				$this->info('Load users: ' . count($users));
				foreach ($users as $user) {
					event(new UserUpdateBalanceEvent($user));
					$this->info('Update user #' . $user->id);
				}
			} catch (InvalidQueryRange $e) {
				$this->error($e->getMessage());
			};

		}



	}