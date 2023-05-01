<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class ProductController extends Controller
{
        
    public function get_popular_products(Request $request){
        //I added the orderBy so the popular products show newest first
        $list = Food::where('type_id', 3)->take(10)->orderBy('created_at', 'DESC')->get();
                //Getting out informatiopn that we dont want
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => 3,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200);
 
    }
        public function get_recommended_products(Request $request){
        
            $list = Food::where('type_id', $request['restaurantid'])->take(10)->orderBy('created_at', 'DESC')->get();
        
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => (int)$request->restaurantid,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200);
    }

    public function update_remaining_products(Request $request){
        
        /*$meal = Food::where('id', $request->id)->get();
    
            $meal['remaining'] = $request->remaining;
            $meal->save();*/

            //This is the correct way to update an item in DB. I got it from stack overflow. Above incorrect code was me trying to do it out of my mind.
            Food::where('id', $request->id)->update(array('remaining' => $request->remaining));
            
}

    public function get_drinks(Request $request){
        $list = Food::where('type_id', 5)->take(10)->get();
        
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => 5,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200);
    }

}
