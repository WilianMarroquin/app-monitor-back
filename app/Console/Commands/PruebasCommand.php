<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PruebasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebas:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $user = User::find(3);

        $token = $user->createToken('Monitor-CSharp-Key', ['monitor:access']);

        dd($token->plainTextToken);

        dd($user->toArray());


        dd($user->getMedia('avatars')->last()->getUrl('thumb24'));

    }
}
