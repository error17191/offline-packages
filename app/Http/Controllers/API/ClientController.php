<?php

namespace App\Http\Controllers\API;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ClientController extends Controller
{
    public function search(Request $request)
    {
        if (!$request->searchText) {
            return [];
        }
        return Client::search($request->searchText)->take(7)->get();
    }

    public function store(ClientRequest $request)
    {
        if (!$request->isJson()) {
        abort(400);
        }

        $data=$request->all();
        $data['age']=Client::getAge($data['birthDate']);
        $client = Client::create($data);
        $client->refresh();
        return response()->json([
            'client' => $client
        ], 201);

    }

    public function isMobileUnique(Request $request)
    {
        $count = Client::isMobileUnique($request->mobile);
        return response()->json([
            'unique' => $count == 0
        ]);
    }

    public function isEmailUnique(Request $request)
    {
        $count = Client::isEmailUnique($request->email);
        return response()->json([
            'unique' => $count == 0
        ]);
    }
}
