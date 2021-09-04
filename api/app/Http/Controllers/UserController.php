<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserAppointments;
use App\Models\UserFavorite;
use App\Models\Professional;
use App\Models\ProfessionalServices;

class UserController extends Controller
{
    private $loggedUser;
    
    public function __construct() {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function read() {
        $array = ['error' => ''];

        $info = $this->loggedUser;
        $info['avatar'] = url('media/avatars/'.$info['avatar']);
        $array['data'] = $info;

        return $array;
    }

    public function toggleFavorite(Request $request) {
        $array = ['error' => ''];

        $id_professional = $request->input('professional');

        $professional = Professional::find($id_professional);


        if($professional) {
            $fav = UserFavorite::select()
                ->where('id_user', $this->loggedUser->id)
                ->where('id_professional', $id_professional)   
            ->first();

            if($fav) {
                //remover
                $fav->delete();
                $array['have'] = false;
            } else {
                //adicionar
                $newFav = new UserFavorite();
                $newFav->id_user = $this->loggedUser->id;
                $newFav->id_professional = $id_professional;
                $newFav->save();
                $array['have'] = true;
            }
        } else {
            $array['error'] = 'Profissional inexistente';
        }

        return $array;
    }

    public function getFavorites() {
        $array = ['error' => '', 'list'=>[]];

        $favs = UserFavorite::select()
            ->where('id_user', $this->loggedUser->id)
        ->get();

        if($favs){
            foreach($favs as $fav){

                $professional = Professional::find($fav['id_professional']);
                $professional['avatar'] = url('media/avatars/'.$professional['avatar']); 
                $array['list'][] = $professional;
            }
        }

        return $array;

    }

    public function getAppointments() {
        $array = ['error' => '', 'list'=>[]];

        $apps = UserAppointments::select()
            ->where('id_user', $this->loggedUser->id)
            ->orderBy('ap_datetime', 'DESC')
        ->get();

        if($apps) {

           foreach($apps as $app) {

            $professional = Professional::find($app['id_professional']);
            $professional['avatar'] = url('media/avatars/'. $professional['avatar']);

            $service = ProfessionalServices::find($app['id_service']);

            $array['list'][] = [
                'id' => $app['id'],
                'datetime' => $app['ap_datetime'],
                'professional' => $professional,
                'service' => $service
            ];

           }
            
        } else {
            $array['error'] = 'Não existem agendamentos';
        }

        return $array;
    }
}
