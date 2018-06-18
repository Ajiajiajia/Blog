<?php

namespace App\Http\Controllers;

use App\Services\Catalogs\CatalogService;
use App\Tool\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    //
    private $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function create(Request $request)
    {
        $rules = [
            'title' => 'required',
            'lv' => 'required',
            'parent_id' => 'required',
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rules);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rules);
        $data['created_at'] = Carbon::now();
        $this->catalogService->create($data);
        return response()->json([
            'code' => 1000,
            'message' => '添加成功'
        ]);
    }

    public function edit(Request $request)
    {
        $rules = [
            'id' => 'required',
            'title' => 'required',
            'content' => '',
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rules);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rules);
        $this->catalogService->update($data);
        return response()->json([
            'code' => 1000,
            'message' => '更新成功'
        ]);
    }

    public function show()
    {
        $catalogs = $this->catalogService->getCatalogs();
        $data = [];
        foreach ($catalogs as $catalog) {
            if ($catalog->lv == 1) {
                $data[$catalog->id] = $catalog;
                $data[$catalog->id]->next_lv=[];
            } else if ($catalog->lv == 2) {
                if (isset($data[$catalog->parent_id])){
                    array_push($data[$catalog->parent_id]->next_lv,$catalog);
                }
            }
        }
//        $data= json_decode(json_encode($data),true);
        $newData=[];
        foreach ($data as $item){
            array_push($newData,$item);
        }
        return response()->json([
            'code' => 1000,
            'data' => $newData
        ]);
    }

    public function getCatalogContent($catalog)
    {
        $content = $this->catalogService->getContent($catalog);
        return response()->json([
            'code' => 1000,
            'data' => $content
        ]);
    }

    public function delete($id)
    {
        $this->catalogService->delete($id);
        return response()->json([
            'code' => 1000,
            'message' => '删除成功'
        ]);
    }
}
