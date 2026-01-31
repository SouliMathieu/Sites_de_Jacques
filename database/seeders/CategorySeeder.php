<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Panneaux Solaires',
                'slug' => 'panneaux-solaires',
                'description' => 'Panneaux solaires photovoltaïques monocristallins et polycristallins pour toutes installations',
                'image' => '/images/products/panneaux-solaires/Panneaux photovoltaïques-vid.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Batteries',
                'slug' => 'batteries',
                'description' => 'Batteries de stockage d\'énergie : Gel, Lithium LiFePO4, AGM pour systèmes solaires',
                'image' => '/images/products/batteries/Batterie lithium felicity Solar.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Onduleurs',
                'slug' => 'onduleurs',
                'description' => 'Onduleurs hybrides et convertisseurs pour systèmes solaires',
                'image' => '/images/products/onduleurs/Onduleur_Hybride1.jpg',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Régulateurs',
                'slug' => 'regulateurs',
                'description' => 'Régulateurs MPPT et contrôleurs de charge pour optimiser vos installations',
                'image' => '/images/products/regulateurs/Régulateur MPPT.jpg',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Kits Solaires',
                'slug' => 'kits',
                'description' => 'Kits solaires complets prêts à installer pour maisons et entreprises',
                'image' => '/images/products/kits/Kit G45.jpg',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Accessoires',
                'slug' => 'electronique',
                'description' => 'Boîtiers, protections et accessoires pour installations photovoltaïques',
                'image' => '/images/products/electronique/Ultimate body.jpg',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
