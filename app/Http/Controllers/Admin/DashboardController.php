<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index');
    }

    public function getStats(Request $request)
    {
        $metric = $request->input('metric');
        $period = $request->input('period', 'day'); // day, week, month, year

        $start = now();
        $end = now();
        $groupByFormat = '';
        $dateFormat = '';

        switch ($period) {
            case 'day':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                $groupByFormat = '%Y-%m-%d %H:00:00'; // MySQL format for hour
                $dateFormat = 'H:00'; // Format for label
                break;
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $groupByFormat = '%Y-%m-%d';
                $dateFormat = 'd/m';
                break;
            case 'month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $groupByFormat = '%Y-%m-%d';
                $dateFormat = 'd/m';
                break;
            case 'year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $groupByFormat = '%Y-%m';
                $dateFormat = 'm/Y';
                break;
        }

        $data = $this->queryMetric($metric, $start, $end, $groupByFormat);
        
        $total = $data->sum('aggregate');
        
        // Fill gaps in time series data
        $chartData = $this->fillTimeSeriesData($data, $start, $end, $period, $dateFormat);

        return response()->json([
            'total' => $total,
            'labels' => $chartData['labels'],
            'values' => $chartData['values']
        ]);
    }

    private function queryMetric($metric, $start, $end, $groupByFormat)
    {
        // We use MySQL DATE_FORMAT for grouping by time periods
        
        switch ($metric) {
            case 'new_patients':
                return Patient::select(DB::raw("DATE_FORMAT(created_at, '{$groupByFormat}') as time_group"), DB::raw('count(*) as aggregate'))
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();
            
            case 're_exam_patients':
                return Consultation::select(DB::raw("DATE_FORMAT(consultation.consultation_date, '{$groupByFormat}') as time_group"), DB::raw('count(DISTINCT consultation.patient_id) as aggregate'))
                    ->whereBetween('consultation.consultation_date', [$start, $end])
                    ->whereHas('patient', function ($q) use ($start) {
                        $q->where('created_at', '<', $start); // Bệnh nhân đã đăng ký từ trước
                    })
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'created_consultations':
                return Consultation::select(DB::raw("DATE_FORMAT(consultation_date, '{$groupByFormat}') as time_group"), DB::raw('count(*) as aggregate'))
                    ->whereBetween('consultation_date', [$start, $end])
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'completed_consultations':
                return Consultation::select(DB::raw("DATE_FORMAT(consultation_date, '{$groupByFormat}') as time_group"), DB::raw('count(*) as aggregate'))
                    ->whereBetween('consultation_date', [$start, $end])
                    ->where('status', Consultation::STATUS_COMPLETED)
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'created_prescriptions':
                return Prescription::select(DB::raw("DATE_FORMAT(prescribed_at, '{$groupByFormat}') as time_group"), DB::raw('count(*) as aggregate'))
                    ->whereBetween('prescribed_at', [$start, $end])
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'completed_prescriptions':
                return Prescription::select(DB::raw("DATE_FORMAT(dispensed_at, '{$groupByFormat}') as time_group"), DB::raw('count(*) as aggregate'))
                    ->whereBetween('dispensed_at', [$start, $end])
                    ->where('status', 2) // status = 2 is "đã lấy thuốc"
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'prescribed_medicines':
                return PrescriptionDetail::select(DB::raw("DATE_FORMAT(prescription_detail.created_at, '{$groupByFormat}') as time_group"), DB::raw('sum(prescription_detail.quantity) as aggregate'))
                    ->join('prescription', 'prescription.id', '=', 'prescription_detail.prescription_id')
                    ->whereBetween('prescription.prescribed_at', [$start, $end])
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'dispensed_medicines':
                return PrescriptionDetail::select(DB::raw("DATE_FORMAT(prescription.dispensed_at, '{$groupByFormat}') as time_group"), DB::raw('sum(prescription_detail.quantity) as aggregate'))
                    ->join('prescription', 'prescription.id', '=', 'prescription_detail.prescription_id')
                    ->whereBetween('prescription.dispensed_at', [$start, $end])
                    ->where('prescription.status', 2)
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();

            case 'revenue':
                return Invoice::select(DB::raw("DATE_FORMAT(paid_at, '{$groupByFormat}') as time_group"), DB::raw('sum(total_amount) as aggregate'))
                    ->whereBetween('paid_at', [$start, $end])
                    ->where('status', Invoice::STATUS_PAID)
                    ->groupBy('time_group')
                    ->orderBy('time_group')
                    ->get();
        }
        
        return collect([]);
    }

    private function fillTimeSeriesData($data, $start, $end, $period, $dateFormat)
    {
        $labels = [];
        $values = [];
        
        $dataMap = $data->pluck('aggregate', 'time_group')->toArray();
        
        $current = clone $start;
        
        while ($current <= $end) {
            $key = '';
            $label = '';
            
            switch ($period) {
                case 'day':
                    $key = $current->format('Y-m-d H:00:00');
                    $label = $current->format($dateFormat);
                    $current->addHour();
                    break;
                case 'week':
                case 'month':
                    $key = $current->format('Y-m-d');
                    $label = $current->format($dateFormat);
                    $current->addDay();
                    break;
                case 'year':
                    $key = $current->format('Y-m');
                    $label = $current->format($dateFormat);
                    $current->addMonth();
                    break;
            }
            
            $labels[] = $label;
            $values[] = isset($dataMap[$key]) ? (float) $dataMap[$key] : 0;
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}
