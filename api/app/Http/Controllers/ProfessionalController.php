<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\UserAppointments;
use App\Models\UserFavorite;
use App\Models\Professional;
use App\Models\ProfessionalPhotos;
use App\Models\ProfessionalReviews;
use App\Models\ProfessionalServices;
use App\Models\ProfessionalTestimonial;
use App\Models\ProfessionalAvailability;

class ProfessionalController extends Controller
{
    private $loggedUser;
    
    public function __construct() {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }
    /*
    public function createRandom() {
        $array = ['error' => ''];

        for ($q=0; $q < 15; $q++) { 
            $names = ['Tiago','Marina','Rosa','Maísa','Ana','Mariana','Caio','Maria do Carmo','Mateus','Elaine','Zinito','Alisson','Deise','Paulo','Leonardo'];
            $lastnames = ['Abreu','Abreu','Abreu','Cano','Braga','Cano','Vasconcelos','Pereira','Souza','Souza','Cano','Campos','Campos','Chagas','Chagas'];
            $expertise = ['Desenvolvedor de Software','Tatuadora','Escritora','Cabaleleira','Musicista','Pedagoga','Delegado','Estilista','Consultor de Vendas','Consultora de Vendas','Artesão','Consultor de Negócios','Chefe de Cozinha','Psicólogo','Personal Trainer'];

            $services0 = ['serviço001','serviço002','serviço003','serviço004','serviço005'];
            $services1 = ['A','B','C','D','E'];

            $testimonials = [
                'Lorem ipsum suscipit tellus aptent consectetur inceptos, rhoncus quam quisque laoreet hendrerit, lorem erat ut sed nisl. interdum condimentum urna euismod orci',
                'feugiat rhoncus quisque malesuada lacinia aenean, aliquam adipiscing ipsum tincidunt et gravida pulvinar molestie nulla. congue potenti maecenas tortor laoreet',
                'suscipit. vulputate facilisis neque sit quisque ut proin a purus platea fringilla maecenas ad egestas velit, per tincidunt nunc magna vehicula sed congue dolor pulvinar congue id',
                'conubia luctus neque senectus sit rhoncus ullamcorper ipsum donec, fames justo dictum mattis ac aenean lectus porttitor, potenti donec venenatis aliquam aptent feugiat magna',
                'ligula donec ultricies, auctor per id hendrerit leo sit torquent, dictum nullam congue duis quam'
            ];

            $newProfessional = new Professional();
            $newProfessional->name = $names[rand(0, count($names)-1)].' '.$lastnames[rand(0, count($lastnames)-1)];
            $newProfessional->expertise = $expertise[rand(0, count($expertise)-1)];
            $newProfessional->stars = rand(2,4).'.'.rand(0,9);
            $newProfessional->latitude = '-23.5'.rand(0,9).'30907';
            $newProfessional->longitude = '-46.6'.rand(0,9).'82795';
            $newProfessional->save();

            $ns = rand(3,6);

            for ($w=0; $w < 4; $w++) { 
                $newProfessionalPhoto = new ProfessionalPhotos();
                $newProfessionalPhoto->id_professional = $newProfessional->id;
                $newProfessionalPhoto->url = rand(1,5).'.png';
                $newProfessionalPhoto->save();
            }

            for ($w=0; $w < $ns; $w++) { 
                $newProfessionalService = new ProfessionalServices();
                $newProfessionalService->id_professional = $newProfessional->id;
                $newProfessionalService->name = $services0[rand(0, count($services0)-1)].' de '.$services1[rand(0, count($services1)-1)];
                $newProfessionalService->price = rand(1, 99).'.'.rand(0, 100);
                $newProfessionalService->save();
            }

            for ($w=0; $w < 3; $w++) { 
                $newProfessionalTestimonial = new ProfessionalTestimonial();
                $newProfessionalTestimonial->id_professional = $newProfessional->id;
                $newProfessionalTestimonial->name = $names[rand(0, count($names)-1)];
                $newProfessionalTestimonial->rate = rand(2, 4).'.'.rand(0, 9);
                $newProfessionalTestimonial->body = $testimonials[rand(0, count($testimonials)-1)];
                $newProfessionalTestimonial->save();
            }

            for ($e=0; $e < 4; $e++) { 
                $rAdd = rand(7,10);
                $hours = [];
                for ($r=0; $r < 8; $r++) { 
                    $time = $r + $rAdd;
                    if($time < 10) {
                        $time = '0'.$time;
                    }
                    $hours[] = $time.':00';
                }
                $newProfessionalAvail = new ProfessionalAvailability();
                $newProfessionalAvail->id_professional = $newProfessional->id;
                $newProfessionalAvail->weekday = $e;
                $newProfessionalAvail->hours = implode(',', $hours);
                $newProfessionalAvail->save();
            }
        }

        return $array;
    }
    */

    private function searchGeo($address) {
        $key = env('MAPS_KEY', null);

        $address = urlencode($address);

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    public function list(Request $request) {
        $array = ['error' => ''];

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $city = $request->input('city');
        $offset = $request->input('offset');

        if (!$offset) {
            $offset = 0;
        }

        if(!empty($city)) {
            $res = $this->searchGeo($city);

            if(count($res['results']) > 0) {
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }elseif(!empty($lat) && !empty($lng)) {
                $res = $this->searchGeo($lat.','.$lng);

                if(count($res['results']) > 0) {
                    $city = $res['results'][0]['formatted_address'];
                }
            }
        } else {
            $lat = '-23.5630907';
            $lng = '-46.6682795';
            $city = 'São Paulo';
        }

        $professionals = Professional::select(Professional::raw('*, SQRT(
            POW(69.1 * (latitude - '.$lat.'),2) + 
            POW(69.1 * ('.$lng.' - longitude) * COS(latitude / 57.3), 2))  AS distance'))
            ->havingRaw('distance < ?', [25])
            ->orderBy('distance', 'ASC')
            ->offset($offset)
            ->limit(5)
            ->get();

        foreach($professionals as $pkey => $pvalue) {
            $professionals[$pkey]['avatar'] = url('media/avatars/'. $professionals[$pkey]['avatar']);
        }

        $array['data'] = $professionals;
        $array['loc'] = 'São Paulo';

        return $array;
    }

    public function one($id) {
        $array = ['error' => ''];

        $professional = Professional::find($id);

        if($professional) {
            $professional['avatar'] = url('media/avatars/'.$professional['avatar']);
            $professional['favorited'] = false;
            $professional['photos'] = [];
            $professional['services'] = [];
            $professional['testimonials'] = [];
            $professional['available'] = [];

            //retorna favoritos
            $cFavorite = UserFavorite::where('id_user', $this->loggedUser->id)
                ->where('id_professional', $professional->id)
                ->count();
            if($cFavorite > 0) {
                $professional['favorited'] = true;
            } 

            //retornando as fotos
            $professional['photos'] = ProfessionalPhotos::select(['id', 'url'])
                ->where('id_professional', $professional->id)
                ->get();
            foreach($professional['photos'] as $ppkey => $ppvalue) {
                $professional['photos'][$ppkey]['url'] = url('media/uploads/'.$professional['photos'][$ppkey]['url']);
            }

            //retornando os serviços
            $professional['services'] = ProfessionalServices::select(['id', 'name', 'price'])
                ->where('id_professional', $professional->id)
                ->get();

            //retornando os depoimentos
            $professional['testimonials'] = ProfessionalTestimonial::select(['id', 'name', 'rate', 'body'])
                ->where('id_professional', $professional->id)
                ->get();

            //retornando disponibilidade
            $availability = [];

            // - retornando disponibilidade geral
            $avails = ProfessionalAvailability::select(['*'])
            ->where('id_professional', $professional->id)
            ->get();
            $availWeekdays = [];
            foreach($avails as $item) {
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            // - retornar agendamentos dos próximos 20 dias 
            $appointments = [];
            $appQuery = UserAppointments::where('id_professional', $professional->id)
                ->whereBetween('ap_datetime', [
                    date('Y-m-d').' 00:00:00',
                    date('Y-m-d', strtotime('+20 days')).' 23:59:59'
                ])
                ->get();
            foreach($appQuery as $appItem) {
                $appointments[] = $appItem['ap_datetime'];
            }

            // - Gerar disponibilidade real
            for($q=0;$q<20;$q++) {
                $timeItem = strtotime('+'.$q.' days');
                $weekday = date('w', $timeItem);

                if(in_array($weekday, array_keys($availWeekdays))) {
                    $hours = [];

                    $dayItem = date('Y-m-d', $timeItem);

                    foreach($availWeekdays[$weekday] as $hourItem) {
                        $dayFormated = $dayItem.' '.$hourItem.':00';
                        if(!in_array($dayFormated, $appointments)) {
                            $hours[] = $hourItem;
                        } 
                    }

                    if(count($hours) > 0) {
                        $availability[] = [
                            'date' => $dayItem,
                            'hours' => $hours
                        ];
                    }

                }
            }

            $professional['available'] = $availability;
            

            $array['data'] = $professional;
        } else {
            $array['error'] = 'Este Profissional não foi encontrado';
            return $array;
        }


        return $array;
    }
}
