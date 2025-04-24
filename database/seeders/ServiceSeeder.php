<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Service::factory()->count(10)->create(); */

        $services = [
            [
                'name' => 'Shampoo Specifico',
                'price' => 1,
            ],
            [
                'name' => 'Conditioner',
                'price' => 3,
            ],
            [
                'name' => 'Shampoo + Mask Vegana',
                'price' => 7,
            ],
            [
                'name' => 'Trattamento Dreams',
                'price' => 13,
                'percentage' => 10
            ],
            [
                'name' => 'Trattamento Malia',
                'price' => 8,
                'percentage' => 10
            ],
            [
                'name' => 'Trattamento Structural',
                'price' => 10,
                'percentage' => 10
            ],
            [
                'name' => 'Trattamento Lifting/Filler',
                'price' => 15,
                'percentage' => 10
            ],
            [
                'name' => 'Ricostruzione',
                'price' => 25,
                'percentage' => 10
            ],
            [
                'name' => 'Trattamento Cutaneo',
                'price' => 15,
                'percentage' => 10
            ],
            [
                'name' => 'Fiala Caduta',
                'price' => 6,
                'percentage' => 10
            ],
            [
                'name' => 'Piega',
                'price' => 8,
            ],
            [
                'name' => 'Supplemento Extensions',
                'price' => 3,
            ],
            [
                'name' => 'Supplemento Ferro/Piastra',
                'price' => 3,
            ],
            [
                'name' => 'Taglio',
                'price' => 10,
            ],
            [
                'name' => 'Taglio Uomo',
                'price' => 15,
            ],
            [
                'name' => 'Riflessante',
                'price' => 20,
                'percentage' => 10
            ],
            [
                'name' => 'Ritocco Colore',
                'price' => 23,
            ],
            [
                'name' => 'Ritocco Colore Senza Ammoniaca',
                'price' => 28,
            ],
            [
                'name' => 'Ritocco Colore 10 Minuti',
                'price' => 28,
            ],
            [
                'name' => 'Ritocco Colore Completo',
                'price' => 10,
            ],
            [
                'name' => 'Sunflower',
                'price' => 25,
            ],
            [
                'name' => 'Ombrè',
                'price' => 85,
            ],
            [
                'name' => 'Babylight',
                'price' => 55,
            ],
            [
                'name' => 'Mechès/Colpi di Sole',
                'price' => 45,
            ],
            [
                'name' => 'Air Touch',
                'price' => 140,
            ],
            [
                'name' => 'Protezione Deco',
                'price' => 20,
            ],
            [
                'name' => 'Decappaggio',
                'price' => 40,
            ],
            [
                'name' => 'Permanente',
                'price' => 45,
            ],
            [
                'name' => 'Cheratina',
                'price' => 80,
            ],
            [
                'name' => 'Trecce',
                'price' => 10,
            ],
            [
                'name' => 'Semiraccolto',
                'price' => 25,
            ],
            [
                'name' => 'Raccolto',
                'price' => 35,
            ],
            [
                'name' => 'Extensions',
                'price' => 3,
                'is_variable_price' => true,
            ],
            [
                'name' => 'Vendita',
                'price' => null,
                'percentage' => 10,
                'is_variable_price' => true,
            ],
            [
                'name' => 'Supplemento Anti-giallo',
                'price' => 6,
            ],
            [
                'name' => 'Prerigmentazione',
                'price' => 20,
            ],
            [
                'name' => 'Pacchetto Sposa',
                'price' => 400,
            ],
            [
                'name' => 'Pacchetto Comunione',
                'price' => 150,
            ],
        ];

        foreach ($services as $service) {
            if (!isset($service['percentage'])) {
                $service['percentage'] = 0;
            }

            Service::create($service);
        }
    }
}
