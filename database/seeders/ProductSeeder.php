<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $panneauxSolaires = Category::where('slug', 'panneaux-solaires')->first();
        $batteries = Category::where('slug', 'batteries')->first();
        $onduleurs = Category::where('slug', 'onduleurs')->first();
        $electronique = Category::where('slug', 'electronique')->first();
        $kits = Category::where('slug', 'kits')->first();
        $regulateurs = Category::where('slug', 'regulateurs')->first();

        $products = [
            // BATTERIES
            [
                'name' => 'Batterie Gel RTG12-200 Deep Cycle',
                'description' => 'Batterie gel RTG12-200 spécialement conçue pour les décharges profondes. Idéale pour les systèmes solaires autonomes et les applications critiques au Burkina Faso.',
                'specifications' => 'Capacité: 200Ah | Tension: 12V | Type: Gel Deep Cycle | Cycles: 1200+ | Résistance aux températures extrêmes | Maintenance réduite',
                'price' => 175000,
                'promotional_price' => 165000,
                'stock_quantity' => 25,
                'images' => ['/images/products/batteries/batterie gel RTG12-200.jpg'],
                'warranty' => '3 ans',
                'category_id' => $batteries->id,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Batterie Gel RTG12-200 - Zida Solaire Burkina Faso',
                'meta_description' => 'Batterie gel RTG12-200 haute performance pour systèmes solaires au Burkina Faso.',
            ],

            [
                'name' => 'Batterie Lithium Felicity Solar 150Ah',
                'description' => 'Batterie lithium Felicity Solar haute capacité avec technologie LiFePO4. Performance exceptionnelle et durée de vie prolongée pour vos installations solaires.',
                'specifications' => 'Capacité: 150Ah | Tension: 12V | Type: LiFePO4 | Cycles: 5000+ | BMS intelligent | Communication Bluetooth | Poids: 18kg',
                'price' => 450000,
                'promotional_price' => 420000,
                'stock_quantity' => 15,
                'images' => ['/images/products/batteries/Batterie-lithium-felicity-Solar1.jpg'],
                'warranty' => '8 ans',
                'category_id' => $batteries->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Batterie Lithium LiFePO4 15kWh Système',
                'description' => 'Système de batterie lithium LiFePO4 de 15kWh pour installations commerciales et résidentielles de grande capacité. Solution complète avec BMS avancé.',
                'specifications' => 'Capacité: 15kWh | Configuration: 48V | Type: LiFePO4 | Cycles: 8000+ | Système modulaire | Monitoring intelligent | Garantie étendue',
                'price' => 2850000,
                'promotional_price' => 2650000,
                'stock_quantity' => 5,
                'images' => ['/images/products/batteries/Batterie lithium LiFePO4 15kwh.jpg'],
                'warranty' => '15 ans',
                'category_id' => $batteries->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Batterie Felicity Solar Premium',
                'description' => 'Batterie Felicity Solar premium avec technologie avancée pour systèmes solaires professionnels. Fiabilité et performance garanties.',
                'specifications' => 'Capacité: 200Ah | Tension: 12V | Type: Gel Premium | Cycles: 1500+ | Technologie AGM améliorée | Résistance climatique',
                'price' => 195000,
                'promotional_price' => 180000,
                'stock_quantity' => 20,
                'images' => ['/images/products/batteries/Batterie-felicity-Solar.jpg'],
                'warranty' => '4 ans',
                'category_id' => $batteries->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Batterie Lithium LiFePO4 100Ah',
                'description' => 'Batterie lithium LiFePO4 compacte de 100Ah. Légère, performante et avec plus de 6000 cycles de charge. Parfaite pour les systèmes solaires résidentiels.',
                'specifications' => 'Capacité: 100Ah | Tension: 12V | Type: LiFePO4 | Cycles: 6000+ | Poids: 12kg | BMS intégré | Charge rapide',
                'price' => 320000,
                'promotional_price' => 295000,
                'stock_quantity' => 30,
                'images' => ['/images/products/batteries/batterie-lithium-LiFePO4.jpg'],
                'warranty' => '10 ans',
                'category_id' => $batteries->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            // ÉLECTRONIQUE
            [
                'name' => 'Boîtier Ultimate Body Étanche IP65',
                'description' => 'Boîtier de protection Ultimate Body étanche IP65 pour équipements électroniques solaires. Protection optimale contre les intempéries du Burkina Faso.',
                'specifications' => 'Protection: IP65 | Matériau: Aluminium anodisé | Dimensions: 400x300x150mm | Ventilation forcée | Serrure sécurisée | Anti-corrosion',
                'price' => 85000,
                'promotional_price' => 75000,
                'stock_quantity' => 35,
                'images' => ['/images/products/electronique/Ultimate body.jpg'],
                'warranty' => '3 ans',
                'category_id' => $electronique->id,
                'is_active' => true,
            ],

            // KITS
            [
                'name' => 'Kit Solaire Complet G45 - 2kW',
                'description' => 'Kit solaire complet G45 de 2kW incluant panneaux, batterie, onduleur et accessoires. Solution clé en main pour maisons individuelles au Burkina Faso.',
                'specifications' => 'Puissance: 2000W | Panneaux: 4x500W | Batterie: 200Ah Gel | Onduleur: 2000W | Câblage et supports inclus | Installation guidée',
                'price' => 1250000,
                'promotional_price' => 1150000,
                'stock_quantity' => 8,
                'images' => ['/images/products/kits/Kit G45.jpg'],
                'videos' => ['/videos/Kit-G45.mp4'], // Nom exact avec espace
                'warranty' => '2 ans système complet',
                'category_id' => $kits->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            // ONDULEURS
            [
                'name' => 'Onduleur Hybride 5kW MPPT Intégré',
                'description' => 'Onduleur hybride 5kW avec régulateur MPPT intégré et chargeur AC. Solution tout-en-un pour systèmes solaires résidentiels et commerciaux.',
                'specifications' => 'Puissance: 5000W | Entrée: 48V DC | MPPT: 80A | Écran tactile | WiFi/Bluetooth | Parallélisme possible | Efficacité: 95%',
                'price' => 485000,
                'promotional_price' => 450000,
                'stock_quantity' => 12,
                'images' => ['/images/products/onduleurs/Onduleur_Hybride1.jpg'],
                'videos' => ['/videos/Onduleur-hybride-vid.mp4'], // Nom exact sans espace
                'warranty' => '5 ans',
                'category_id' => $onduleurs->id,
                'is_featured' => true,
                'is_active' => true,
            ],

            // PANNEAUX SOLAIRES
            [
                'name' => 'Panneaux Solaires Photovoltaïques Premium',
                'description' => 'Panneaux solaires photovoltaïques haute performance pour installations résidentielles et commerciales. Technologie monocristalline avec rendement optimal.',
                'specifications' => 'Puissance: 300W-500W | Tension: 24V-48V | Efficacité: 22% | Garantie: 25 ans | Certification: IEC 61215 | Résistance grêle',
                'price' => 195000,
                'promotional_price' => 180000,
                'stock_quantity' => 25,
                'images' => ['/images/products/panneaux-solaires/Panneaux photovoltaïques-vid.jpg'],
                'videos' => [
                    '/videos/Panneaux-photovoltaïques-vid.mp4', // Nom exact sans espace
                    '/videos/Panneaux-solaires-photovoltaïques-vid.mp4' // Nom exact sans espace
                ],
                'warranty' => '25 ans',
                'category_id' => $panneauxSolaires->id,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Panneaux Solaires Photovoltaïques - Zida Solaire',
                'meta_description' => 'Panneaux solaires haute performance au meilleur prix au Burkina Faso. Garantie 25 ans.',
            ],

            // RÉGULATEURS
            [
                'name' => 'Régulateur MPPT 60A Intelligent',
                'description' => 'Régulateur de charge MPPT 60A avec écran LCD et communication. Optimise le rendement de vos panneaux solaires avec une efficacité de 98%.',
                'specifications' => 'Courant: 60A | Tension système: 12V/24V/48V auto | Efficacité: 98% | Écran LCD | USB/Bluetooth | Protection IP32 | Algorithme MPPT avancé',
                'price' => 125000,
                'promotional_price' => 115000,
                'stock_quantity' => 40,
                'images' => ['/images/products/regulateurs/Régulateur MPPT.jpg'],
                'videos' => ['/videos/Regulateur-MPPT.mp4'], // Nom exact avec accent et espace
                'warranty' => '5 ans',
                'category_id' => $regulateurs->id,
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
