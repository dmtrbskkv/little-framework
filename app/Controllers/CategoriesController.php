<?php


namespace App\Controllers;


use App\Extensions\Request;
use App\Extensions\View;
use App\Models\LostItemsCategory;
use App\Models\Model;

class CategoriesController
{
    /**
     * Endpoints for methods with "header" function
     */
    const ENDPOINTS = [
        'success_add_category' => '/categories',
        'success_update_category' => '/categories',
        'success_remove_category' => '/categories',
        'error_add_category' => '/categories',
        'no_user_right' => '/'
    ];

    public function showCategories($errors = [])
    {
        $data['categories'] = (new LostItemsCategory())
            ->all();
        $data['errors'] = $errors;
        return new View('categories', $data);
    }

    public function addCategory()
    {
        $request = new Request();
        $label = $request->input('label');

        if(!$label){
            $this->showCategories(['Enter the category name']);
        }

        $category = new LostItemsCategory();
        $category->label = $label;
        if(!$category->save()){
            $this->showCategories(['Error when adding the category']);
        }

        header('Location: '.self::ENDPOINTS['success_add_category']);
    }

    public function updateCategory(){
        $request = new Request();
        $label = trim($request->input('label'));
        $id = trim($request->input('id'));

        if(!$label || !$id){
            $this->showCategories(['Enter the category name']);
        }

        $category = new LostItemsCategory($id);
        $category->label = $label;

        if(!$category->save()){
            $this->showCategories(['Error when updating the category']);
        }

        header('Location: '.self::ENDPOINTS['success_update_category']);
    }

    public function removeCategory(){
        $request = new Request();
        $id = $request->input('id');

        if(!$id || !(new LostItemsCategory())->remove($id)){
            $this->showCategories(['Error when removing the category']);
        }

        header('Location: '.self::ENDPOINTS['success_remove_category']);
    }

}