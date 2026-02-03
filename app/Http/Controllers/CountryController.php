<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
class CountryController extends Controller
{
   

    // public function save_country()
    // {
    //     $data = '[
    //         {
    //             "state_code": null,
    //             "countryId": "165",
    //             "id": "1477772",
    //             "name": [
                   
    //                 {
    //                     "EN": [
    //                         {
    //                             "name": "Luckau",
    //                             "isVariation": "0"
    //                         }
    //                     ]
    //                 }
    //             ],
    //             "latitude": "51.85246000",
    //             "longitude": "13.70735000",
    //             "code": null
    //         },
    //         {
    //             "state_code": null,
    //             "countryId": "164",
    //             "id": "1521611",
    //             "name": [
                 
    //                 {
    //                     "EN": [
    //                         {
    //                             "name": "Saint-Trivier",
    //                             "isVariation": "0"
    //                         },
    //                         {
    //                             "name": "Saint-Trivier-de-Courtes"
    //                         }
    //                     ]
    //                 },
                 
    //                 {
    //                     "ES": [
    //                         {
    //                             "name": "Saint-Trivier-de-Courtes",
    //                             "isVariation": "0"
    //                         }
    //                     ]
    //                 }
    //             ],
    //             "latitude": "46.45904000",
    //             "longitude": "5.08047000",
    //             "code": null
    //         }
    //     ]';
    
    //     $countries = json_decode($data, true);
  
    //     if (!empty($countries)) {
    //         $batchSize = 100; // Set the desired batch size
    //         $counter = 0; // Initialize the counter variable
    
    //         $batchData = [];
    
    //         foreach ($countries as $countrydata) {
    //             $nameArray = $countrydata['name'];
    //             $name = '';
    
                
    
    //             $isVariation = isset($countrydata['isVariation']) ? $countrydata['isVariation'] : null;
    
    //             $batchData[] = [
    //                 'id' => $countrydata['state_code'],
    //                 'name' => $countrydata['name'][0]['EN'][0]['name'],
    //                 'state_code' => $countrydata['state_code'],
    //                 'countryId' => $countrydata['countryId'],
    //                 'latitude' => $countrydata['latitude'],
    //                 'longitude' => $countrydata['longitude'],
    //                 'code' => $countrydata['code'],
    //                 'isVariation' => $countrydata['name'][0]['EN'][0]['isVariation'],
                    
    //             ];
    
    //          //   Perform the bulk insert when the batch size is reached
    //             if (count($batchData) >= $batchSize) {
    //                 DB::table('TPcountrycity')->insert($batchData);
    //                 $batchData = []; // Reset the batch data
    
    //                 $counter++; // Increment the counter variable
    
    //                 // Break out of the loop after the fourth iteration
    //                 if ($counter >= 4) {
    //                     break;
    //                 }
    //             }
    //         }
       
    
    //         // Data insertion completed
    //         echo 'Data inserted successfully.';
    //     } else {
    //         $this->error('No country data found.');
    //     }
    // }
    
    public function save_country()
    {
        $data = '[
            {
                "state_code": null,
                "countryId": "186",
                "id": "1676732",
                "name": [{
                    "EN": [{
                        "name": "Verhnee Dzhemete",
                        "isVariation": "0"
                    }]
                }, {
                    "RU": [{
                        "name": "\u0412\u0435\u0440\u0445\u043d\u0435\u0435 \u0414\u0436\u0435\u043c\u0435\u0442\u0435",
                        "isVariation": "0"
                    }]
                }],
                "latitude": "44.94955000",
                "longitude": "37.32287000",
                "code": null
            }, {
                "state_code": null,
                "countryId": "153",
                "id": "1051",
                "name": [{
                    
                    "EN": [{
                        "name": "Groedig",
                        "isVariation": "0"
                    }]
                }, {
                    "IT": [{
                        "name": "Gr\u00f6dig",
                        "isVariation": "0"
                    }]
                }, {
                    "FR": [{
                        "name": "Gr\u00f6dig",
                        "isVariation": "0"
                    }]
                }, {
                    "TH": [{
                        "name": "Gr\u00f6dig",
                        "isVariation": "0"
                    }]
                }, {
                    "ES": [{
                        "name": "Gr\u00f6dig",
                        "isVariation": "0"
                    }]
                }],
                "latitude": "47.73333000",
                "longitude": "13.03333000",
                "code": null
            }
        ]';
    
        $countries = json_decode($data, true);
    
        if (isset($countries)) {
            foreach ($countries as $countrydata) {
                // Store the country data in the database
                foreach ($countrydata['name'] as $language) {
                    if (isset($language['EN'])) {
                        $englishTranslation = $language['EN'][0];
                        echo $englishTranslation['name'];
    
                        DB::table('TPcountrycity')->insert([
                            'id' => $countrydata['id'],
                            'name' => $englishTranslation['name'],
                            'state_code' => $countrydata['state_code'],
                            'countryId' => $countrydata['countryId'],
                            'latitude' => $countrydata['latitude'],
                            'longitude' => $countrydata['longitude'],
                            'code' => $countrydata['code'],
                            'isVariation' => $englishTranslation['isVariation'],
                        ]);
                    }
                }
            }
            die();
        } else {
            $this->error('No country data found in the API response.');
        }
    }
    

}