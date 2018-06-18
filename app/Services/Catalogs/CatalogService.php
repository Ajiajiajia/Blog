<?php

namespace App\Services\Catalogs;

use Illuminate\Support\Facades\DB;

class CatalogService
{

    public function create($catalog)
    {
        DB::table('catalogs')->insert($catalog);
    }

    public function update($catalog){

        DB::table('catalogs')->where('id',$catalog['id'])->update($catalog);
    }
    public function showCatalogTree()
    {
        //TODO 突然感觉没必要这么写
    }

    public function getCatalogs()
    {
        $catalogs = DB::table('catalogs')
            ->where('id','>',0)
            ->select('id', 'title', 'lv', 'parent_id', 'created_at')
            ->orderBy('lv','asc')->orderBy('created_at','asc')
            ->get();
        return $catalogs;
    }

    public function getContent(int $catalog){
        $content=DB::table('catalogs')->where('id','=',$catalog)->first();
        return $content;
    }

    public function delete($catalog){
        DB::table('catalogs')->where('id','=',$catalog)
            ->orWhere('parent_id','=',$catalog)
            ->delete();
    }
}