<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    protected $service;

    /**
    * Instantiate a new controller instance.
    *
    * @return void
    */
    public function __construct(ServiceService $service){
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request){
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
    public function update($id, Request $request){
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

    public function show_available_services(){
        return $this->service->show_available_services();
    }

     public function editservice($id){
        return $this->service->editservice($id);
    }

    public function services(){
        return $this->service->services();
    }

    public function services_home_page_section(){
        return $this->service->services_home_page_section();
    }
    
}
