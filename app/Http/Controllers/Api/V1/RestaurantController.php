<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
        
    public function get_info(Request $request){
        //I added the orderBy so the popular products show newest first
        $list = Restaurant::orderBy('created_at', 'DESC')->get();
                //Getting out informatiopn that we dont want
                /*
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }*/
                
                 $data =  [
                    'total_size' => $list->count(),
                    //'type_id' => 2,
                    //'offset' => 0,
                    'restaurants' => $list
                ];
                
         return response()->json($data, 200);
 
    }
}