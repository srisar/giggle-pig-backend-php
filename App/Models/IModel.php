<?php


namespace App\Models;


interface IModel
{
    public static function build($array): self;

    public static function find(int $id);

    public static function findAll($limit = 1000, $offset = 0);

    public function insert();

    public function update();

    public function delete();
}
