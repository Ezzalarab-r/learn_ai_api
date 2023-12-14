<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\CourseType;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class CourseController extends AdminController
{
    //
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Teacher'))->display(function ($token) {
            return User::where('token', '=', $token)->value('name');
        });
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image('', 50, 50);
        $grid->column('description', __('Description'));
        $grid->column('price', __('Price'));
        $grid->column('lessons_count', __('Lessons num'));
        $grid->column('video_length', __('Video length'));
        $grid->column('followers', __('Followers'));
        $grid->column('score', __('Score'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }


    protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('video', __('Video'));
        $show->field('description', __('Description'));
        $show->field('type_id', __('Type id'));
        $show->field('price', __('Price'));
        $show->field('lessons_count', __('Lessons num'));
        $show->field('video_length', __('Video length'));
        $show->field('followers', __('Followers'));
        $show->field('score', __('Score'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }


    protected function form()
    {
        $form = new Form(new Course());

        $form->text('name', __('Name'));
        // get our categories
        // key value pair
        // last one is the key
        $result = CourseType::pluck('title', 'id');
        // select method to select one of the opitons from result variable
        $form->select('type_id', __('Category'))->options($result);

        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->file('video', __('Video'))->uniqueName();
        $form->textarea('description', __('Description'));
        $form->decimal('price', __('Price'));
        $form->number('lessons_count', __('Number of lessons'));
        $form->number('video_length', __('Video Length'));

        $result = User::pluck('name', 'token');
        $form->select('user_token', __('Teacher'))->options($result);

        $form->display('created_at', __('Created at'));
        $form->display('updated_at', __('Updated at'));

        return $form;
    }
}
