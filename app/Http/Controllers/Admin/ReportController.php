<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\Product;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDailyPurchaseReport()
    {
        $title    = 'report';
        $reports  = DailyReport::all();
        $data     = compact('title','reports');
        return view('admin.reports.daily-index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDailyPurchaseReport()
    {
        $title     =  'report';
        $products  =  Product::all();
        $data      =  compact('products', 'title');
        return view('admin.reports.daly-create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDailyReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDailyPurchaseReport(StoreDailyReportRequest $request)
    {        
        foreach ($request->product_id as $key => $value) {
            $daily_reports                           = new DailyReport();
            $daily_reports->product_id               = $value;
            $daily_reports->product_name             = $request->product_name[$value];
            $daily_reports->product_buy_price        = $request->product_buy_price[$value];
            $daily_reports->product_selling_price    = $request->product_selling_price[$value];
            $daily_reports->save();
        }
        return redirect()->route('admin.daily.purchase.reports.index')->with('success', 'Daily Price Save successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function show(DailyReport $dailyReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function edit(DailyReport $dailyReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDailyReportRequest  $request
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(DailyReport $dailyReport)
    {
        //
    }
}
