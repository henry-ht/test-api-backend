<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            // age
            array(
                'name'          => '0-3 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '3-6 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '6-9 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '9-12 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '12-18 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '18-24 months',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            array(
                'name'          => '2 years and up',
                'description'   => '',
                'type'          => 'age',
                'father_id'     => null
            ),
            // end age

            // gender
            array(
                'name'          => 'girl clothes',
                'description'   => '',
                'type'          => 'gender',
                'father_id'     => null
            ),
            array(
                'name'          => 'kid clothes',
                'description'   => '',
                'type'          => 'gender',
                'father_id'     => null
            ),
            array(
                'name'          => 'neutral',
                'description'   => '',
                'type'          => 'gender',
                'father_id'     => null
            ),
            // end gender

            // garment type
            array(
                'name'          => 'one-piece suits',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 't-shirts',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'pants',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'dresses',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'nightwear',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'outerwear',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'shoes',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            array(
                'name'          => 'others',
                'description'   => '',
                'type'          => 'garment_type',
                'father_id'     => null
            ),
            // end garment type

            // state of clothes
            array(
                'name'          => 'new with tags',
                'description'   => '',
                'type'          => 'state_of_clothes',
                'father_id'     => null
            ),
            array(
                'name'          => 'like new',
                'description'   => '',
                'type'          => 'state_of_clothes',
                'father_id'     => null
            ),
            array(
                'name'          => 'lightly used',
                'description'   => '',
                'type'          => 'state_of_clothes',
                'father_id'     => null
            ),
            array(
                'name'          => 'used',
                'description'   => '',
                'type'          => 'state_of_clothes',
                'father_id'     => null
            ),
            // end state of clothes

            // brands
            array(
                'name'          => "carter's",
                'description'   => '',
                'type'          => 'brand',
                'father_id'     => null
            ),
            array(
                'name'          => "oshKosh",
                'description'   => '',
                'type'          => 'brand',
                'father_id'     => null
            ),
            array(
                'name'          => "baby Gap",
                'description'   => '',
                'type'          => 'brand',
                'father_id'     => null
            ),
            array(
                'name'          => "gocco",
                'description'   => '',
                'type'          => 'brand',
                'father_id'     => null
            ),
            // end brands

            // color
            array(
                'name'          => 'pink',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'blue',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'yellow',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'green',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'white',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'beige',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'purple',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'orange',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'grey',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'brown',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            array(
                'name'          => 'others',
                'description'   => '',
                'type'          => 'color',
                'father_id'     => null
            ),
            // end color

        ];

        foreach ($datos as $key => $value) {
            Category::updateOrCreate([
                'name' => $value['name'],
                'type' => $value['type']
            ],[
                'description'   => $value['description'],
                'father_id'     => $value['father_id']
            ]);
        }
    }
}
