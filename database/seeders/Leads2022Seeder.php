<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Leads2022Seeder extends Seeder
{
    public function run(): void
    {
        DB::table('leads')->truncate();

        $data2022 = [
            'January' => [
                'Paphos'    => ['Zoopla'=>19,'Rightmove'=>28,'APITS'=>38,'SLV'=>16,'HoS'=>5],
                'Limassol'  => ['Zoopla'=>8,'Rightmove'=>5,'APITS'=>1,'SLV'=>9,'HoS'=>3],
                'Larnaca'   => ['Zoopla'=>5,'Rightmove'=>24,'APITS'=>19,'SLV'=>3,'HoS'=>6],
                'Famagusta' => ['Zoopla'=>1,'Rightmove'=>5,'APITS'=>12,'SLV'=>4,'HoS'=>3],
            ],
            'February' => [
                'Paphos'    => ['Zoopla'=>7,'Rightmove'=>25,'APITS'=>14,'SLV'=>15,'HoS'=>4],
                'Limassol'  => ['Zoopla'=>8,'Rightmove'=>4,'APITS'=>6,'SLV'=>5,'HoS'=>4],
                'Larnaca'   => ['Zoopla'=>11,'Rightmove'=>18,'APITS'=>26,'SLV'=>7,'HoS'=>4],
                'Famagusta' => ['Zoopla'=>0,'Rightmove'=>8,'APITS'=>10,'SLV'=>4,'HoS'=>1],
            ],
            'March' => [
                'Paphos'    => ['Zoopla'=>13,'Rightmove'=>31,'APITS'=>21,'SLV'=>15,'HoS'=>0],
                'Limassol'  => ['Zoopla'=>9,'Rightmove'=>13,'APITS'=>7,'SLV'=>6,'HoS'=>0],
                'Larnaca'   => ['Zoopla'=>7,'Rightmove'=>18,'APITS'=>24,'SLV'=>9,'HoS'=>0],
                'Famagusta' => ['Zoopla'=>6,'Rightmove'=>8,'APITS'=>15,'SLV'=>5,'HoS'=>0],
            ],
            'April' => [
                'Paphos'    => ['Zoopla'=>9,'Rightmove'=>21,'APITS'=>7,'SLV'=>11,'HoS'=>1],
                'Limassol'  => ['Zoopla'=>7,'Rightmove'=>6,'APITS'=>7,'SLV'=>7,'HoS'=>2],
                'Larnaca'   => ['Zoopla'=>11,'Rightmove'=>21,'APITS'=>15,'SLV'=>5,'HoS'=>2],
                'Famagusta' => ['Zoopla'=>5,'Rightmove'=>4,'APITS'=>12,'SLV'=>4,'HoS'=>1],
            ],
            'May' => [
                'Paphos'    => ['Zoopla'=>17,'Rightmove'=>48,'APITS'=>21,'SLV'=>13,'HoS'=>4],
                'Limassol'  => ['Zoopla'=>11,'Rightmove'=>12,'APITS'=>6,'SLV'=>10,'HoS'=>6],
                'Larnaca'   => ['Zoopla'=>13,'Rightmove'=>22,'APITS'=>20,'SLV'=>8,'HoS'=>4],
                'Famagusta' => ['Zoopla'=>6,'Rightmove'=>5,'APITS'=>17,'SLV'=>4,'HoS'=>1],
            ],
            'June' => [
                'Paphos'    => ['Zoopla'=>12,'Rightmove'=>30,'APITS'=>20,'SLV'=>18,'HoS'=>5],
                'Limassol'  => ['Zoopla'=>10,'Rightmove'=>5,'APITS'=>3,'SLV'=>12,'HoS'=>0],
                'Larnaca'   => ['Zoopla'=>17,'Rightmove'=>19,'APITS'=>13,'SLV'=>10,'HoS'=>3],
                'Famagusta' => ['Zoopla'=>1,'Rightmove'=>8,'APITS'=>10,'SLV'=>3,'HoS'=>1],
            ],
            'July' => [
                'Paphos'    => ['Zoopla'=>7,'Rightmove'=>30,'APITS'=>18,'SLV'=>14,'HoS'=>3],
                'Limassol'  => ['Zoopla'=>15,'Rightmove'=>13,'APITS'=>10,'SLV'=>6,'HoS'=>2],
                'Larnaca'   => ['Zoopla'=>5,'Rightmove'=>24,'APITS'=>27,'SLV'=>15,'HoS'=>1],
                'Famagusta' => ['Zoopla'=>5,'Rightmove'=>10,'APITS'=>13,'SLV'=>4,'HoS'=>0],
            ],
            'August' => [
                'Paphos'    => ['Zoopla'=>16,'Rightmove'=>43,'APITS'=>24,'SLV'=>14,'HoS'=>1],
                'Limassol'  => ['Zoopla'=>6,'Rightmove'=>5,'APITS'=>4,'SLV'=>4,'HoS'=>1],
                'Larnaca'   => ['Zoopla'=>9,'Rightmove'=>20,'APITS'=>24,'SLV'=>7,'HoS'=>0],
                'Famagusta' => ['Zoopla'=>4,'Rightmove'=>7,'APITS'=>19,'SLV'=>2,'HoS'=>0],
            ],
            'September' => [
                'Paphos'    => ['Zoopla'=>13,'Rightmove'=>31,'APITS'=>30,'SLV'=>14,'HoS'=>10],
                'Limassol'  => ['Zoopla'=>6,'Rightmove'=>7,'APITS'=>4,'SLV'=>8,'HoS'=>2],
                'Larnaca'   => ['Zoopla'=>7,'Rightmove'=>29,'APITS'=>28,'SLV'=>9,'HoS'=>3],
                'Famagusta' => ['Zoopla'=>5,'Rightmove'=>13,'APITS'=>10,'SLV'=>1,'HoS'=>3],
            ],
            'October' => [
                'Paphos'    => ['Zoopla'=>14,'Rightmove'=>18,'APITS'=>25,'SLV'=>19,'HoS'=>9],
                'Limassol'  => ['Zoopla'=>5,'Rightmove'=>9,'APITS'=>2,'SLV'=>8,'HoS'=>8],
                'Larnaca'   => ['Zoopla'=>7,'Rightmove'=>25,'APITS'=>33,'SLV'=>15,'HoS'=>2],
                'Famagusta' => ['Zoopla'=>3,'Rightmove'=>7,'APITS'=>8,'SLV'=>2,'HoS'=>0],
            ],
            'November' => [
                'Paphos'    => ['Zoopla'=>9,'Rightmove'=>23,'APITS'=>14,'SLV'=>11,'HoS'=>9],
                'Limassol'  => ['Zoopla'=>5,'Rightmove'=>14,'APITS'=>7,'SLV'=>6,'HoS'=>7],
                'Larnaca'   => ['Zoopla'=>5,'Rightmove'=>22,'APITS'=>15,'SLV'=>3,'HoS'=>5],
                'Famagusta' => ['Zoopla'=>3,'Rightmove'=>7,'APITS'=>6,'SLV'=>1,'HoS'=>3],
            ],
            'December' => [
                'Paphos'    => ['Zoopla'=>17,'Rightmove'=>6,'APITS'=>7,'SLV'=>3,'HoS'=>5],
                'Limassol'  => ['Zoopla'=>4,'Rightmove'=>2,'APITS'=>4,'SLV'=>8,'HoS'=>1],
                'Larnaca'   => ['Zoopla'=>6,'Rightmove'=>4,'APITS'=>10,'SLV'=>6,'HoS'=>1],
                'Famagusta' => ['Zoopla'=>1,'Rightmove'=>1,'APITS'=>3,'SLV'=>2,'HoS'=>2],
            ],
        ];

        $rows = [];
        foreach ($data2022 as $month => $locations) {
            foreach ($locations as $location => $sources) {
                foreach ($sources as $source => $count) {
                    $rows[] = [
                        'year'      => 2022,
                        'month'     => $month,
                        'location'  => $location,
                        'source'    => $source,
                        'count'     => $count,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ];
                }
            }
        }

        DB::table('leads')->insert($rows);
    }
}
