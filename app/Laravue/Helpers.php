<?php


namespace App\Laravue;

use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class Helpers
{
    public function getArrayDate($dateFrom, $dateTo)
    {
        /// none dateTo and dateFrom return last 7 days
        if (!($dateTo || $dateFrom)) {
            $begin = new Carbon('-7 days');
            $end = new Carbon();
        } else {
            $begin = new Carbon($dateFrom);
            /// Add time to secure the end day, if not it will return 00:00:00 of end day
            $end = new Carbon("{$dateTo}23:59:59");
        }
        $period = Carbon::parse($begin)->toPeriod($end, 1, 'days');
        $arrayDate = [];
        foreach($period  as $date) { $arrayDate[] = $date->format('d-M'); }
        return $arrayDate;
    }

    public function getObjectDate($dateFrom, $dateTo)
    {
        if (!($dateTo || $dateFrom)) {
            $begin = new Carbon('-7 days');
            $end = new Carbon();
        } else {
            $begin = new Carbon($dateFrom);
            /// Add time to secure the end day, if not it will return 00:00:00 of end day
            $end = new Carbon("{$dateTo}23:59:59");
        }
        $period = Carbon::parse($begin)->toPeriod($end, 1, 'days');
        $yesterday = new Carbon('yesterday');
        $arrayDate = [];
        foreach ($period  as $date) {
            if ($date > $yesterday){
                continue;
            }
            $arrayDate[$date->format("Y-m-d")] = 0;
        }
        return $arrayDate;
    }

    private function parseRequest($params)
    {
        $dateTo = Arr::get($params, 'dateTo', Carbon::parse('yesterday')->format('Y-m-d'));
        $dateFrom = Arr::get($params, 'dateFrom', Carbon::parse('-7 days')->format('Y-m-d'));
        $fields = Arr::get($params, 'fields', 'custom_labels');
        $exportType = Arr::get($params, 'exportType', 'analyticPlatformsExport');
        $IDs = Arr::get($params, 'IDs', '');
        /// add default key in verifyRequest()
        return [
            'dateTo' => $dateTo,
            'dateFrom' => $dateFrom,
            'fields' => $fields,
            'exportType' => $exportType,
            'IDs' => $IDs,
        ];
    }

    public function verifyRequest($params)
    {
        $requestKeys = array_keys($params);
        $defaultKeys = ['dateTo', 'dateFrom', 'fields', 'exportType', 'IDs'];
        if (empty($requestKeys)) {
            return $this->parseRequest($params);
        }
        foreach ($requestKeys as $key)
        {
            if (!in_array($key, $defaultKeys))
            {
                throw new Exception('Request parameters are not accepted');
            }
        }
        return $this->parseRequest($params);
    }

    public function parseData($data, $dateFrom, $dateTo)
    {
        $formattedData = [];
        foreach (array_chunk($data, 2) as $rows) {
            $labelID = $this->getLabels(json_decode($rows[0]['body'], true));
            $metrics = $this->getMetrics(json_decode($rows[1]['body'], true));
            if ($metrics == null) {
                continue;
            }
            $metrics['created_at'] = $dateFrom;
            $metrics['updated_at'] = $dateTo;
            $metrics['custom_labels'] = $labelID['label'];
            $metrics['title'] = $labelID['title'];
            $metrics['description'] = $labelID['description'];
            $metrics['post_id'] = $labelID['id'];
            array_push($formattedData, $metrics);
        }
        return $formattedData;
    }

    private function getLabels($data)
    {
        return [
            'label'=> isset($data['custom_labels']) ? $data['custom_labels'][0] : 'None label',
            'title'=> isset($data['title']) ? json_encode($data['title']) : '',
            'description'=> isset($data['description']) ? json_encode($data['description']) : '',
            'id'=> isset($data['id']) ? $data['id'] : 1234567890,
        ];
    }

    private function getMetrics($data)
    {
        $formattedData = [];
        if (!array_key_exists('data', $data)) {
            return null;
        }
        foreach ($data['data'] as $row) {
            if (array_key_exists('period', $row) and $row['period'] != 'lifetime') {
                continue;
            }
            if (is_array($row['values'][0]['value'])) {
                $formattedData[$row['name']] = array_sum($row['values'][0]['value']);
            } else {
                $formattedData[$row['name']] = $row['values'][0]['value'];
            }
        }
        return $formattedData;
    }

    public function formatFloatArray($array)
    {
        $formattedArray = [];
        foreach ($array as $row)
        {
            $row = array_map(function($num){
                return (float) str_replace(",", "", number_format($num,2));
            }, $row);
            $formattedArray = array_merge_recursive($formattedArray, $row);
        }
        return $formattedArray;
    }

    public function getPreviousTime($dateFrom, $dateTo)
    {
        $begin = new CarbonImmutable($dateFrom);
        $end = new CarbonImmutable($dateTo);
        $diff = $end->diffInDays($begin);
        $previousDateTo = $begin->subDay();
        $previousDateFrom = $previousDateTo->subDays($diff);
        return [
            'dateTo' => Carbon::parse($previousDateTo)->format('Y-m-d'),
            'dateFrom' => Carbon::parse($previousDateFrom)->format('Y-m-d'),
        ];
    }
}
