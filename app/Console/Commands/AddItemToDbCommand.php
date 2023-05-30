<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Food; // Replace with your item model

//Need to change this to update later
class AddItemToDbCommand extends Command
{   
    //The name and signature of the console command.
    protected $signature = 'add:item';
    //The console command description.
    protected $description = 'Add a certain item to the database';

    // Create a new command instance ? Don't have it here

    //Execute the console command.
    public function handle()
    {
        /*$item = new Item();
        // Set the properties of the item
        $item->property1 = 'Value 1';
        $item->property2 = 'Value 2';
        // ... set other properties

        $item->save();*/

        Food::where('id', 21)->update(array('remaining' => 2));

        $this->info('Item remaining updated to the database successfully.');
    }
}

