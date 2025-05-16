<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Admin\AdminResource;
use App\Mappers\Admin\AdminMapper;

class AdminController extends BaseController
{
    private $model;
    private $route = 'admins';
    Public $title="admin";

    public function __construct(Admin $model)
    {
        $this->model = $model;

    }
    public function index(Request $request)
    {
        $access = Auth::guard(name: 'api')->user(); // atau 'admins' sesuai guard kamu
        if (!$access || !$access->hasAccessToMenu($this->route)) {
            return $this->sendError('Akses ditolak', 'Tidak memiliki akses ke menu ini', 403);
        }

        $dataRequest=$request->all() ?? null;
        $page = $request->query('page') ?? 1;
        $raw = (bool) $request->query('raw') ?? false;
        $data = $this->model;

        if($dataRequest){

            if (isset($dataRequest['name'])) {
                $data = $data->where(function ($query) use ($dataRequest) {
                    $query->where('name', 'like', '%' . $dataRequest['name'] . '%');
                });
            }

            if (isset($dataRequest['email'])) {
                $data = $data->where(function ($query) use ($dataRequest) {
                    $query->where('email', 'like', '%' . $dataRequest['email'] . '%');
                });
            }
        }

        $collection = $data->orderBy('id', 'desc')->paginate(10, ['*'], 'page', $page);
        $rawData = AdminResource::collection($collection);

        if ($raw) {
            $datas = $rawData;
        } else {
            $responses = $rawData->response()->getData(true);
            $links = $responses['links'];
            $dataLinks = [];
            unset($responses['links']);
            $meta = $responses['meta'];
            unset($responses['meta']);

            $dataLinks['first_page_url'] = $links['first'];
            $dataLinks['last_page_url'] = $links['last'];
            $dataLinks['prev_page_url'] = $links['prev'];
            $dataLinks['next_page_url'] = $links['next'];

            $datas = array_merge($responses, $meta, $dataLinks);
        }

        return $this->sendResponse($datas, 'Success Load Data');
    }
    public function show($id)
    {
        $access = Auth::guard(name: 'api')->user(); // atau 'admins' sesuai guard kamu
        if (!$access || !$access->hasAccessToMenu($this->route)) {
            return $this->sendError('Akses ditolak', 'Tidak memiliki akses ke menu ini', 403);
        }

        $data = $this->model->find($id);

        if (!$data) {
            return $this->sendError('Data not found', 'Data not found');
        }

        $detailMapper = new AdminMapper($data);
        $result = $detailMapper->data();


        return $this->sendResponse($result, 'Success Load Data');
    }

}
