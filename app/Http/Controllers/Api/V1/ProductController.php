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

public function cart_remaining_products(Request $request){
    //NOTE THE 2 CODE LINES BELOW DOES NOT ACTUALLY STORE THE DATA PROPERLY. CUZ I THINK IT HAS TO DO WITH THE ARRAY I AM SENDING BEING CONVERTED TO A STRING
    //Took me 5 hours to solve this. But this linked helped me https://laracasts.com/discuss/channels/laravel/convert-string-array-to-array-of-ints
    $listId = json_decode($request->mealIds);
    //$integerIDs = array_map('intval', $listId);
    //$listId = $request->mealIds["id"];
    
    //WhereIn is for arrays
    $productList = Food::whereIn('id', $listId)->get(); 

    /*$productList = [];
    foreach ($request->mealIds as $e){
        $meal = Food::find($e);
        $productList->array_push($meal);
    }*/


    //THis for loops seems like it does not work
    /*for ($i = 0; i < count($listId); $i++) {
        $meal = Food::find($listId[i]);
        $productList->array_push($meal);
    }   */

    //$list = Food::where('id', $request['id'])->get();
        foreach ($productList as $item){
            $item['description']=strip_tags($item['description']);
            $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
            unset($item['selected_people']);
            unset($item['people']);
        }
        
         $data =  [
            'products' => $productList
        ];
        
 return response()->json($data, 200);
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
