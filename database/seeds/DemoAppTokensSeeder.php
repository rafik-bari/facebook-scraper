<?php

use Illuminate\Database\Seeder;

class DemoAppTokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $demo_tokens = [
            [
                'app_id' => '1737016636537622',
                'app_secret' => 'db425834b55ca577bd7fbcaa00fafa52'
            ],
            [
                'app_id' => '1183170848373947',
                'app_secret' => 'fbd267b1bd6d96264cdc91c5ecec5746'
            ],
            [
                'app_id' => '970462143052002',
                'app_secret' => '8970704197f0dd57bf1af685c6d606be'
            ],
            [
                'app_id' => '1742135339404384',
                'app_secret' => '7c44206c285fb7d5697d25206f56f70f'
            ],
            [
                'app_id' => '145455599201615',
                'app_secret' => 'd8c0a7288749d3ff4eda73a9c354a822'
            ],
            [
                'app_id' => '248760765500828',
                'app_secret' => 'c130d07499fe020840a58a9f37c2ed6b'
            ],
            [
                'app_id' => '1201035789917043',
                'app_secret' => '3938c3fbec05df0683118f67c64f0bde'
            ],
            [
                'app_id' => '631064233723978',
                'app_secret' => '4d0b5e1d89ac7c58818b6a949125cef5'
            ]
        ];

        foreach ($demo_tokens as $token) {
            $appToken = new \App\AppToken();
            $appToken->fill($token);
            $appToken->save();
        }
    }
}
