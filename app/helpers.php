<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('month2roman')) {
  function month2roman($month)
  {
    $romans = [
      'A',
      'B',
      'C',
      'D',
      'E',
      'F',
      'G',
      'H',
      'I',
      'J',
      'K',
      'L'
    ];

    return isset($romans[$month - 1]) ? $romans[$month - 1] : null;
  }
}

if (!function_exists('getLastDocumentNumber')) {
  function getLastDocumentNumber($modelClass, $field, $filters = [], $dateFormat = 'year', $prefixLength = 5, $dateField = 'created_at')
  {
    if (!class_exists($modelClass)) {
      throw new \InvalidArgumentException("Model class {$modelClass} does not exist.");
    }

    $query = $modelClass::query();
    foreach ($filters as $key => $value) {
      $query->where($key, $value);
    }

    $date = now();
    switch ($dateFormat) {
      case 'year':
        $query->whereYear($dateField, $date->format('Y'));
        break;

      case 'month':
        $query->whereYear($dateField, $date->format('Y'))
          ->whereMonth($dateField, $date->format('m'));
        break;
      
      default:
        throw new \InvalidArgumentException("Invalid filter type: {$dateFormat}. Allowed values are 'year', 'month'.");
    }

    $lastDocument = $query->orderBy($field, 'desc')->latest($dateField)->first();
    Log::info("Last Document Number : {$lastDocument}");
    if (!$lastDocument) {
      Log::info('tidak ada dokument, mulai dari 1');
      return 1;
    } else {
      $lastNumber = intval(substr($lastDocument->$field, 9, $prefixLength));
      Log::info("Last Number : {$lastNumber}");
      return $lastNumber + 1;
    }
  }
}

if (!function_exists('newGetLastDocumentNumber')) {
  function newGetLastDocumentNumber($modelClass, $field, $filters = [], $date = null, $dateFormat = 'year', $prefixLength = 4, $startPrefix=0, $dateField='created_at', $orderField = 'created_at')
  {
    if (!class_exists($modelClass)) {
      throw new \InvalidArgumentException("Model class {$modelClass} does not exist.");
    }

    $query = $modelClass::query();
    foreach ($filters as $key => $value) {
      $query->where($key, $value);
    }

		if($date == null){
			$date = now();
		}

    switch ($dateFormat) {
      case 'year':
        $query->whereYear($dateField, $date->format('Y'));
        break;

      case 'month':
        $query->whereYear($dateField, $date->format('Y'))
          ->whereMonth($dateField, $date->format('m'));
        break;
      
      default:
        throw new \InvalidArgumentException("Invalid filter type: {$dateFormat}. Allowed values are 'year', 'month'.");
    }

    $lastDocument = $query->orderByRaw("SUBSTRING({$orderField}, {$startPrefix}, {$prefixLength}) DESC")->first();
    if (!$lastDocument) {
      return 1;
    } else {
      $lastNumber = intval(substr($lastDocument->$field, $startPrefix, $prefixLength));
      return $lastNumber + 1;
    }
  }
}
