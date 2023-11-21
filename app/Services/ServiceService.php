<?php

namespace App\Services;

use App\Helpers\QueryHelper;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Traits\ResponseFormatterTrait;
use App\Traits\HelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Validator;

class ServiceService  
{
    use ResponseFormatterTrait; use HelperTrait;
    private  $filterableColumns = [];
    private  $searchableColumns = [];

    /**
    * Save new data
    *
    * @return \Illuminate\Http\JsonResponse
    */
     public function store(ServiceRequest $request)
    {

            $data = $request->all();

            $new_service_image = $this->uploadImage($request->file('service_image'), "/website/images/services/");
            $service_image_save = $request->getHttpHost() . '/website/images/services/' . $new_service_image;

            $new_service_banner_image = $this->uploadImage($request->file('service_banner_image'), "/website/images/services/");
            $service_banner_image_save = $request->getHttpHost() . '/website/images/services/'. $new_service_banner_image;

            $new_service_main_image = $this->uploadImage($request->file('service_main_image'), "/website/images/services/");
            $service_main_image_save = $request->getHttpHost() . '/website/images/services/'. $new_service_main_image;

            $imageNames = [
                'service_image' => $service_image_save,
                'service_banner_image' => $service_banner_image_save,
                'service_main_image' => $service_main_image_save,

            ];

            $createFields = [];
            $textFields = [
            'service_title',
            'service_card_description',
            'service_description',
            'service_price',
            'service_alt_text',
            ];
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $createFields[$field] = $request->input($field);
                }
            }
            $allUpdates = array_merge($createFields, $imageNames ?? []);
            $new_service = Service::create($allUpdates);
            return redirect()->back()->with('success_message', 'Posted Succesfully!');
    }

    /**
    * Update existing data
    *
    * @return \Illuminate\Http\JsonResponse
    */
    

  public function update($id, Request $request)
{
    $update = Service::find($id);
    if (!$update) {
        return redirect()->back()->with('error_message', 'Service not found.');
    }

    try {
        $imageNames = [];
        
        $imageFields = [
            'service_image' => 'service_image',
            'service_banner_image' => 'service_banner_image',
            'service_main_image' => 'service_main_image',
        ];

        foreach ($imageFields as $fieldName => $requestInput) {
            if ($request->hasFile($requestInput)) {
                // Delete old image
                $oldImagePath = public_path("/website/images/services/" . basename($update->$fieldName));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                
                // Upload new image
                $newImageName = $this->uploadImage($request->file($requestInput), "/website/images/services/");
                $update->$fieldName = $request->getSchemeAndHttpHost() . '/website/images/services/' . $newImageName;
            }
        }
        // Update other service fields
        $textFields = [
            'service_title',
            'service_card_description',
            'service_description',
            'service_price',
            'service_alt_text',
        ];
        foreach ($textFields as $field) {
            if ($request->filled($field)) {
                $update->$field = $request->$field;
            }
        }

        // Update image fields if new images have been uploaded
        foreach ($imageNames as $key => $value) {
            $update->$key = $value;
        }

        $update->save();
        
        return redirect()->back()->with('success_message', 'Updated Successfully');
    } catch (\Exception $e) {
        // Log the exception for debugging purposes
        \Log::error($e->getMessage());
        return redirect()->back()->with('error_message', 'Error during update: ' . $e->getMessage());
    }  
}

    /**
    * Delete existing data
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id, Request $request)
    {
        try {
            $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
            ]);
            if($validator->fails()){
                return $this->failedValidation($validator);
            }
            $delete = Service::find($id);
            if($delete){
                $delete->delete();
            }else{
                return redirect()->back()->with('error_message', 'Service not found');
            }

            return redirect()->back()->with('success_message', 'Deleted Successfully');
        } catch (\Exception $e) {
           return redirect()->back()->with('error_message', 'Error Occurred AYUSIN MO YAN KINGINA MO HAHAHAH');
        }
    }

    /**
    * Fetch specific data
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function show($id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
            ]);
            if($validator->fails()){
                return $this->failedValidation($validator);
            }
            $data = DummyModel::find($id);
            if(!$data){
                return $this->responseError("Dummy does not exists.",[], Response::HTTP_NOT_FOUND );
            }
            return $this->responseSuccess('Success', $data->toArray());
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(),[], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * Fetch data list
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function fetch(Request $request): JsonResponse
    {
        try {
            $query = DummyModel::query();
            $query = QueryHelper::fetchQuery($query , $request , $this->searchableColumns , $this->filterableColumns);
            $data = QueryHelper::getData($query , $request);

            return $this->responseSuccess('Success', $data->toArray()); 
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(),[], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function show_available_services()
    {
        $services = Service::all();
        return view('cms.pages.available-services.available-services', compact('services'));
    }

    public function editservice($id)
    {
       $services = Service::find($id);
        if ($services) {
            return view('cms.pages.available-services.update-services', compact('services'));
        } else {
            return redirect()->route('cms.pages.available-services.available-services')->with('error_message', 'Service not found');
        }
    }

    public function services()
    {
        $services = Service::all();
        return view('front-end.pages.services-page.index', compact('services'));
    }

    public function services_home_page_section()
    {
        $services = Service::paginate(3);
       return view('front-end.pages.home-page.index', compact('services'));
    }
    
}
