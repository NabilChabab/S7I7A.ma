<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function findAll();
    public function findTrached();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
