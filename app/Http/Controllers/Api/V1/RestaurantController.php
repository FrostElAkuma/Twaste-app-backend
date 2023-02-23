<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Food;

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

    public function upload(Request $request){
        //creating a direcroty and calling it test
        
        //$dir="uploads/";
        //grabing the image from our request
        //$image = $request->file('image');
     
       if ($request->hasFile('image')) {
                //generating a unique id for the image and savinf it as a png
               $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . "png";
               //Checking if we have a public directory in our server. If not we create it
               /*if (!Storage::disk('public')->exists($dir)) {
                   Storage::disk('public')->makeDirectory($dir); //public/test
               }*/
               //Storage::disk('public')->put('images/'.$imageName, file_get_contents($image));
            //Below line took me about 2 hours to get right
            $path = $request->image->storeAs('images', $imageName, 'admin');
       }/*else{
            return response()->json(['message' => trans('/storage/test/'.'def.png')], 200);
       } */
       
       /*$userDetails = [
       
           'image' => $imageName,
        
       ];*/

      // User::where(['id' => 27])->update($userDetails);

       //myServer.come/storage/test/imagename.png
       //The above is for the testing video, but in reality I am storing my images under uploads/images. So myseverver.com/uplouds/images/myImage.png
       //Please note i should have the full image path below. Currently I dont but ned to update in future
       return response()->json(['message' => trans('images/'.$imageName)], 200);
   }

   public function new(Request $request){

    $meal = new Food();
    $meal->name = $request->name;
    $meal->price = $request->price;
    $meal->img = $request->img;//"images/2023-02-23-63f7bb5ec2cd4.png";
    $meal->description = "olalala";
    $meal->stars = 4;
    $meal->people = 5;
    $meal->selected_people = 5;
    $meal->location = "UAE";
    $meal->type_id = 2;
    $meal->created_at = now(); 
    $meal->updated_at = now();

    $meal->save();
    return response()->json('oki',200);
    /*
    $meal = Food::create([
        'name' => "thisWorking?",
        'description' => "olalala",
        'price'=> 33,
        'stars' => 4,
        'people' => 5,
        'selected_people' => 5,
        'img' => "images/9effe833ef9f578bdcd84643995f6acc.png",//$request->img
        'location' => "UAE",
        'created_at' => now(),
        'updated_at' => now(),
        'type_id' => 2,
    ]);*/
   }

}