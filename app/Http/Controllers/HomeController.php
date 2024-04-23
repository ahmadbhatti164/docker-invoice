<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Khill\Lavacharts\Laravel\LavachartsFacade;
use Khill\Lavacharts\Lavacharts;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $topProducts = DashboardStatsService::make()->topProducts();
        $invoiceDueDate = DashboardStatsService::make()->invoiceDueDate();
        $totalExpense = DashboardStatsService::make()->totalExpense();
        $this->showGraphs($totalExpense);
        return view('home', compact('topProducts', 'invoiceDueDate', 'totalExpense'));
    }

    private function showGraphs($totalExpense){
        // Weekly graph data
        $weekly = DashboardStatsService::make()->expenseBreakDownWeekly();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Day')
            ->addNumberColumn('Total Cost')
            ->addNumberColumn('VAT')
            ->setDateTimeFormat('Y-m-d');
        foreach ($weekly as $key => $val) {

            $reasons->addRow([$key,  $val['total'], $val['vat']]);
        }
        LavachartsFacade::ColumnChart('WeeklyExpenses', $reasons, [
            'title' => $totalExpense['week_number'] . ' Week',
            'elementId' => 'weekly_expense_div',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'width'=>'1465',
            'height'=>'350',
            'bar'=> [ 'groupWidth'=> '70'],
        ]);

        // Monthly graph data
        $monthly = DashboardStatsService::make()->expenseBreakDownMonthly();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Days')
            ->addNumberColumn('Total Cost')
            ->addNumberColumn('VAT')
            ->setDateTimeFormat('Y-m-d');
        foreach ($monthly as $key => $val) {

            $reasons->addRow([$key, $val['total'], $val['vat']]);
        }
        LavachartsFacade::ColumnChart('MonthlyExpenses', $reasons, [
            'title' => $totalExpense['month_number'] ,
            'elementId' => 'monthly_expense_div',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'width'=>'1465',
            'height'=>'350',
            'bar'=> [ 'groupWidth'=> '70'],
        ]);

        // Yearly graph data

        $yearly = DashboardStatsService::make()->expenseBreakDownYearly();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Day')
            ->addNumberColumn('Total Cost')
            ->addNumberColumn('VAT')
            ->setDateTimeFormat('Y-m-d');
        foreach ($yearly as $key => $val) {

            $reasons->addRow([$key,  $val['total'],$val['vat']]);
        }

        LavachartsFacade::ColumnChart('YEarlyExpenses', $reasons, [
            'title' => $totalExpense['year_number'],
            'elementId' => 'yearly_expense_div',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'width'=>'1465',
            'height'=>'350',
            'bar'=> [ 'groupWidth'=> '70'],


        ]);
    }
}
