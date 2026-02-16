<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instrument;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instruments=[
        [
            'name'=> 'Guitarra clàssica',
            'description'=> 'Guitarra clàssica',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/guitarra-classica.webp',
        ],
        [
            'name'=> 'Guitarra elèctrica',
            'description'=> 'Guitarra elèctrica',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/guitarra-electrica.webp',
        ],
        [
            'name'=> 'Guitarra electroacústica',
            'description'=> 'Guitarra electroacústica',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/guitarra-electroacustica.webp',
        ],
        [
            'name'=> 'Piano',
            'description'=> 'Teclat de 88 tecles',
            'type'=>'KEYBOARD',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/piano.webp',
        ],
        [
            'name'=> 'Bateria',
            'description'=> 'Bateria elèctrica',
            'type'=>'PERCUSSION',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/bateria.webp',
        ],
        [
            'name'=> 'Cello',
            'description'=> 'Cello 3/4',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/cello.webp',
        ],
        [
            'name'=> 'Saxofon',
            'description'=> 'Saxofon',
            'type'=>'WIND',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/saxofon.webp',
        ],
        [
            'name'=> 'Xilòfon',
            'description'=> 'Xilòfon',
            'type'=>'PERCUSSION',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/xilofon.webp',
        ],
        [
            'name'=> 'Gaita',
            'description'=> 'Gaita',
            'type'=>'WIND',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/gaita.webp',
        ],
        [
            'name'=> 'Banjo',
            'description'=> 'Banjo',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/banjo.webp',
        ],
        [
            'name'=> 'Midi',
            'description'=> 'Midi',
            'type'=>'KEYBOARD',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/midi.webp',
        ],
        [
            'name'=> 'Trompeta',
            'description'=> 'Trompeta',
            'type'=>'WIND',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/trompeta.webp',
        ],
        [
            'name'=> 'Calaix flamenc',
            'description'=> 'Calaix flamenc',
            'type'=>'PERCUSSION',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/calaix-flamenc.webp',
        ],
        [
            'name'=> 'Baix elèctric',
            'description'=> 'Baix elèctric',
            'type'=>'STRING',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/baix-electric.webp',
        ],
        [
            'name'=> 'Flauta travessera',
            'description'=> 'Flauta travessera',
            'type'=>'WIND',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/flauta-travessera.webp',
        ],
        [
            'name'=> 'Trombó',
            'description'=> 'Trombó',
            'type'=>'WIND',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/trombo.webp',
        ],
        [
            'name'=> 'Bongos',
            'description'=> 'Bongos',
            'type'=>'PERCUSSION',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/bongos.webp',
        ],
        [
            'name'=> 'Sintetitzador',
            'description'=> 'Sintetitzador',
            'type'=>'KEYBOARD',
            'status'=>'AVAILABLE',
            'image_path' => 'demo/instruments/sintetitzador.webp',
        ],
    ];

        foreach ($instruments as $data) {
            Instrument::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

    }
}