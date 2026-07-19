<?php
namespace App\Filament\Widgets;
use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
class AttendanceServiceComparisonChart extends ChartWidget
{
    public ?int $year = null; public ?int $serviceTypeId = null;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Service comparison';
    protected ?string $description = 'Average attendance by service or event.';
    protected ?string $maxHeight = '380px';
    protected function getData(): array
    {
        $rows=Attendance::query()->selectRaw('service_name, ROUND(AVG(total),1) average_total, MAX(total) highest_total, COUNT(*) services_count')->when($this->year,fn(Builder $q)=>$q->whereYear('service_date',$this->year))->when($this->serviceTypeId,fn(Builder $q)=>$q->where('service_type_id',$this->serviceTypeId))->groupBy('service_name')->orderByDesc('average_total')->limit(12)->get();
        return ['datasets'=>[
            ['label'=>'Average','data'=>$rows->pluck('average_total')->all(),'backgroundColor'=>'rgba(37,99,235,.75)'],
            ['label'=>'Highest','data'=>$rows->pluck('highest_total')->all(),'backgroundColor'=>'rgba(16,185,129,.65)'],
        ],'labels'=>$rows->pluck('service_name')->all()];
    }
    protected function getType(): string { return 'bar'; }
    protected function getOptions(): array { return ['indexAxis'=>'y','maintainAspectRatio'=>false,'plugins'=>['legend'=>['position'=>'bottom']],'scales'=>['x'=>['beginAtZero'=>true]]]; }
}
