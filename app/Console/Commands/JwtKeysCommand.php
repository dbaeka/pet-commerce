<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class JwtKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:keys
                                      {--force : Overwrite keys they already exist}
                                      {--length=4096 : The length of the private key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the encryption keys for JWT authentication';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $key = RSA::createKey((int)$this->option('length') ?: 4096);

        list($publicKey, $privateKey) = [
            config('jwt.public_key'),
            config('jwt.private_key')
        ];

        if ((file_exists($publicKey) || file_exists($privateKey)) && !$this->option('force')) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');
            return -1;
        }

        file_put_contents($publicKey, (string)$key->getPublicKey());
        file_put_contents($privateKey, (string)$key);

        $this->info('Encryption keys generated successfully.');
        return 0;
    }
}
