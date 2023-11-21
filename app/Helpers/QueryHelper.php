<?php

namespace App\Helpers;
use DB;
use Validator;
/**
 *
 */
class QueryHelper
{

    /**
     * This function will call common functions to generate fetch query.
     *
     * @param object $query Query of model
     * @param $request
     * @param array $filterableColumns List of filterable columns
     * @return object
     */
  public static function fetchQuery(object $query , $request , array $searchableColumns , array $filterableColumns ): object
  {

    $query = self::search($query , $request , $searchableColumns);
    $query = self::filterColumns($query , $request , $filterableColumns);
    $query = self::filterDate($query , $request);
    $query = self::sort($query , $request);
    return $query;

  }

    /**
     * This function will generate query that will filter the columns.
     *
     * @param object $query Query of model
     * @param $request
     * @param array $filterableColumns List of filterable columns
     * @return object
     */
  public static function filterColumns(object $query , $request , array $filterableColumns): object
  {

    $additionalFilters  = $request->additional_filters ? $request->additional_filters : null;
    if (isset($additionalFilters)) {
      foreach ($additionalFilters as $additionalFilter) {
        if (isset($filterableColumns[$additionalFilter['column']])) {
            $column_name =   $filterableColumns[$additionalFilter['column']];
            $column_values = isset($additionalFilter['values']) ? $additionalFilter['values'] : [];
            if (count($column_values) > 0) {
               $query =  $query->where(function ($q) use ($column_name, $column_values) {
                    foreach ($column_values as $value) {
                      $q->orWhere(DB::raw($column_name), $value);
                    }
                });
            }
        }
      }
    }
    return $query;

  }

    /**
    * This function will generate query that will search value.
    *
    * @param object $query                  Query of model
    * @param \Illuminate\Http\Request       $request
    * @param array  $searchableColumns      List of searchable columns
    * @return query
    */
  public static function search($query , $request , $searchableColumns)
  {
    // code...
    if (isset($request->search) && $request->search !== "") {
      $query = $query->where(function ($q) use ($searchableColumns, $request) {
          foreach ($searchableColumns as $columns) {
              if ($columns === "orders.customer_details") {
                  $q->orWhere(DB::raw("LOWER(JSON_EXTRACT(`customer_details` , '$.full_name'))"), 'like', '%' . $request->search . '%')
                      ->orWhere(DB::raw("JSON_EXTRACT(`customer_details` , '$.mobile_number')"), '=', $request->search );
              } else if ($columns === "orders.id") {
                  $orderNumber = ltrim($request->search, '0');
                  $q->orWhere(DB::raw($columns), 'like', '%' . $orderNumber . '%');
              } else {
                  $q->orWhere(DB::raw($columns), 'like', '%' . $request->search . '%');
              }
          }
      });
    }
    return $query;
  }

    /**
     * This function will generate query for sorting data.
     *
     * @param object $query
     * @param $request
     * @return object
     */
  public static function sort(object $query , $request): object
  {

    if(isset($request->sort_by) && $request->sort_by != "" ){
      $validator = Validator::make($request->all(), [
        'sort_order' => 'required|string',
      ]);
      if($validator->fails()){
        abort( ResponseHelper::formatValidatorResponse("error" , $validator->messages()->all() , 400) );
      }
      $query =  $query->orderBy($request->sort_by , $request->sort_order);
    }
    return $query;

  }

  /**
  * This function will generate query for filtering dates.
  *
  * @param object $query                  Query of model
  * @param \Illuminate\Http\Request       $request
  * @return query
  */
  public static function filterDate($query , $request)
  {
    // code...
    if($request->filterDateType != 'NO_FILTER' && isset($request->filterDateType)){
      $validator = Validator::make($request->all(), [
        'filterDateColumn' => 'required|string',
      ]);
      if($validator->fails()){
        abort( ResponseHelper::formatValidatorResponse("error" , $validator->messages()->all() , 400) );
      }
      if($request->filterDateType != 'CUSTOM'){
        $formattedDate = DateFormatterHelper::formatWordToDate($request->filterDateType);
        $fromDate = date('Y-m-d',strtotime($formattedDate['fromDate']));
        $toDate = date('Y-m-d',strtotime($formattedDate['toDate']));
      }else{
        $validator = Validator::make($request->all(), [
          'fromDate' => 'required|date',
          'toDate' => 'required|date',
        ]);
        if($validator->fails()){
          abort( ResponseHelper::formatValidatorResponse("error" , $validator->messages()->all() , 400) );
        }
        $fromDate = $request->fromDate;
        $toDate   = $request->toDate;
      }
      $query = $query->whereBetween(DB::RAW("CAST(".$request->filterDateColumn." as DATE)") , [$fromDate , $toDate]  );
    }
    return $query;

  }

    /**
     * This function will get data from query with pagination or none.
     *
     * @param object $query Query of model
     * @param $request
     * @return mixed
     */
  public static function getData(object $query , $request): mixed
  {
    return $request->paginated ? $query->paginate($request->rows_per_page) : $query->get();
  }
}
