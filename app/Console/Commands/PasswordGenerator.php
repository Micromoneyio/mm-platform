<?php
 /*
 * User: bazil
 * Date: 13.05.18
 * Time: 22:39
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Hash;

class PasswordGenerator extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new password hash';

    /**
     * Execute the console command.
     */
    public function handle() {
        $password = $this->secret('What is the password?');
        $hash = Hash::make($password);

        $this->info('Password hash: ' . $hash);
    }
}
