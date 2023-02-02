<?php

namespace App\Admin\Controllers;

use App\Models\Restaurant;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show; 

//Note these Controllers inside the Admin Folder is just for the Laravel Admin Panel
class RestaurantController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Restaurant';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    //This for showing all the Restaurants
    protected function grid()
    {
        $grid = new Grid(new Restaurant());

        //$grid->column('id', __('Id'));
        $grid->id("Restaurant ID");
        $grid->column('name', __('Name'));
        $grid->column('img', __('Thumbnail Photo'))->image('',60,60);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    //This for clicking and seeing detials about teh Restaurant
    protected function detail($id)
    {
        $show = new Show(Restaurant::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('img', __('Image'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    //This for editing Restaurant ?
    protected function form()
    {
        $form = new Form(new Restaurant());

        $form->text('name', __('Name'));
        $form->image('img', __('Thumbnail'))->uniqueName();
    
        return $form;
    }
}
