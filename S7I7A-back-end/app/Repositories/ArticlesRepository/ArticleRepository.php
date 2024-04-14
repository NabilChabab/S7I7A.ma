<?php

namespace App\Repositories\ArticlesRepository;

use App\Models\Article;
use App\Repositories\RepositoryInterface;

class ArticleRepository implements RepositoryInterface
{

    public function FindAll()
    {
        return Article::all();
    }

    public function FindById($id)
    {
        return Article::findOrFail($id);
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function update($id, array $data)
    {
        $article = $this->FindById($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        $article = $this->FindById($id);
        $article->delete();
        return $article;
    }
    public function findTrached(){
        return Article::onlyTrashed()->get();
    }
}
