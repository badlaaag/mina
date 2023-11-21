<?php

namespace App\Http\Controllers;

use App\Http\Requests\DummyRequest;
use App\Services\DummyService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{

    protected $service;

    /**
    * Instantiate a new controller instance.
    *
    * @return void
    */
    public function __construct(DummyService $service){
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DummyRequest $request){
       return $this->service->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return $this->service->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, DummyRequest $request){
       return $this->service->update($id , $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id , Request $request){
       return $this->service->destroy($id , $request);
    }

    /**
    * Display a listing of the resource.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function fetch(Request $request){
        return $this->service->fetch($request);
    }
    
}
