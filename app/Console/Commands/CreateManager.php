<?php
	/**
	 * Created by PhpStorm.
	 * User: nikolkrash
	 * Date: 21.07.2018
	 * Time: 15:20
	 */

	namespace App\Console\Commands;

	use App\Services\RoleService;
	use App\Services\UserService;
	use Illuminate\Console\Command;
	use Faker\Factory as Faker;

	class CreateManager extends Command {

		/**
		 * @var UserService
		 */
		private $_userService;


		/**
		 * @var RoleService
		 */
		private $_rolesService;

		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'manager:create {--email=} {--pass=}';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Create new manager. Options: {--email=} {--pass=}?';

		/**
		 * CreateManager constructor.
		 *
		 * @param UserService $_userService
		 * @param RoleService $roleService
		 */
		public function __construct(UserService $_userService, RoleService $roleService) {
			$this->_userService = $_userService;
			$this->_rolesService = $roleService;
			parent::__construct();
		}

		public function handle() {
			$data = [
				'email' => $this->option('email'),
				'active' => 1,
				'roles'	=> [1],
			];

			$existedManager = $this->_userService->getUserByEmail($data['email']);
			if ($existedManager !== null) {
				$this->error('Email is exist in db');
				return;
			}

			$password = $this->option('pass');
			if (is_null($password)) {
				$password = Faker::create('en')->password;
			}
			$data['password'] = $password;

			try {
				$manager = $this->_userService->createManagerByParams($data);
				$this->_rolesService->updateUserRoles($manager, $data['roles']);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}

			$this->alert("Create new manager.");
			$this->info('Email: ' . $data['email']);
			$this->info('Password: ' . $data['password']);
		}


	}