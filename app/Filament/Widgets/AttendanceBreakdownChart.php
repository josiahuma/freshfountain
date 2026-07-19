<?php
namespace App\Filament\Widgets;
use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
class AttendanceBreakdownChart extends ChartWidget
{
    public ?int $year = null; public ?int $serviceTypeId = null;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Monthly attendance breakdown';
    protected ?string $description = 'Men, women, children, visitors and online attendance by month.';
    protected ?string $maxHeight = '420px';
    protected function getData(): array
    {
        $rows=Attendance::query()->selectRaw('MONTH(service_date) month, SUM(men) men, SUM(women) women, SUM(children) children, SUM(visitors) visitors, SUM(online) online')->when($this->year,fn(Builder $q)=>$q->whereYear('service_date',$this->year))->when($this->serviceTypeId,fn(Builder $q)=>$q->where('service_type_id',$this->serviceTypeId))->groupByRaw('MONTH(service_date)')->orderBy('month')->get();
        $labels=$rows->map(fn($r)=>date('M',mktime(0,0,0,(int)$r->month,1)))->all();
        return ['datasets'=>[
            ['label'=>'Men','data'=>$rows->pluck('men')->all(),'backgroundColor'=>'rgba(37,99,235,.75)'],
            ['label'=>'Women','data'=>$rows->pluck('women')->all(),'backgroundColor'=>'rgba(168,85,247,.75)'],
            ['label'=>'Children','data'=>$rows->pluck('children')->all(),'backgroundColor'=>'rgba(245,158,11,.75)'],
            ['label'=>'Visitors','data'=>$rows->pluck('visitors')->all(),'backgroundColor'=>'rgba(16,185,129,.75)'],
            ['label'=>'Online','data'=>$rows->pluck('online')->all(),'backgroundColor'=>'rgba(6,182,212,.75)'],
        ],'labels'=>$labels];
    }
    protected function getType(): string { return 'bar'; }
    protected function getOptions(): array { return ['maintainAspectRatio'=>false,'plugins'=>['legend'=>['position'=>'bottom']],'scales'=>['x'=>['stacked'=>true],'y'=>['stacked'=>true,'beginAtZero'=>true]]]; }
}
