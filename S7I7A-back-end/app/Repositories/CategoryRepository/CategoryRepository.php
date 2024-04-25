<?php

namespace App\Repositories\CategoryRepository;
use App\Models\Category;
use App\Repositories\RepositoryInterface;

class CategoryRepository implements RepositoryInterface
{

    public function findAll()
    {
        return Category::all();
    }

    public function findTrached(){
        return Category::onlyTrashed()->get();
    }

    public function findById($id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->findById($id);
        $category->delete();
        return $category;
    }
}


