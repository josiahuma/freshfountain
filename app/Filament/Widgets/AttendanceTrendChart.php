<?php
namespace App\Filament\Widgets;
use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
class AttendanceTrendChart extends ChartWidget
{
    public ?int $year = null; public ?int $serviceTypeId = null;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Attendance trend';
    protected ?string $description = 'Total attendance for each recorded service.';
    protected ?string $maxHeight = '380px';
    protected function getData(): array
    {
        $rows = Attendance::query()->when($this->year, fn(Builder $q)=>$q->whereYear('service_date',$this->year))->when($this->serviceTypeId, fn(Builder $q)=>$q->where('service_type_id',$this->serviceTypeId))->orderBy('service_date')->get();
        return ['datasets'=>[['label'=>'Total attendance','data'=>$rows->pluck('total')->all(),'borderColor'=>'#2563eb','backgroundColor'=>'rgba(37,99,235,.14)','fill'=>true,'tension'=>.32,'pointRadius'=>3]],'labels'=>$rows->map(fn($r)=>$r->service_date->format('d M'))->all()];
    }
    protected function getType(): string { return 'line'; }
    protected function getOptions(): array { return ['maintainAspectRatio'=>false,'plugins'=>['legend'=>['position'=>'bottom']],'scales'=>['y'=>['beginAtZero'=>true]]]; }
}
