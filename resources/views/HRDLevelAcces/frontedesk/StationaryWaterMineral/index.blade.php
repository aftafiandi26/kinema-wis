@extends('layout')

@section('title')
    (hr) Stocked
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')

@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c3' => 'active'
    ])
@stop
@section('body')
<style type="text/css">
tr:nth-child(even) {background-color: #f2f2f2;}
</style>
<?php 
use App\stationary_count;
 ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Stationary Stock (Mineral Water)</h1> 
    </div>
</div>
<div class="row">
<div class="col-lg-12">
    <a href="{{route('addStockStationaryWater')}}" class="btn btn-sm btn-default">Add Stock</a>
    <a href="{{route('GenerateStockedWater')}}" class="btn btn-sm btn-success" target="_blank">PDF</a> 
    <a href="{{route('ExcelStationaryStockWater')}}" class="btn btn-sm btn-info">Excel</a>   
    <br>
    <br>
    <label style="margin-bottom: -10px;">Periode: {{date('F Y')}}</label><br>
    <label>Stock Awal: {{date('F Y', strtotime('-1 month'))}}</label><br>   
</div>
</div>
<div class="row">
<div class="col-lg-2 pull-right">
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for items.." title="Type in a name item" class="form-control">         
</div>
</div>
<div class="row">
<div class="col-lg-12" style="margin-left: 0px; overflow-x: scroll; margin-top: 10px;"> 
    <table id="myTable" class="table table-hover fixed_headers" border="1">
        <thead>
           <tr>
                <th rowspan="2" style="text-align: center;">No</th>               
                <th rowspan="2" style="text-align: center;">Code Item</th>                
                <th rowspan="2" style="text-align: center;">Item</th>
                <th rowspan="2" style="text-align: center;">UOM</th>
                <th rowspan="2" style="text-align: center;">Brand</th>
                <th rowspan="2" style="text-align: center;">Stock</th>
                <th colspan="31" style="text-align: center;">Date Items Out <i>{{date('F Y')}}</i></th>
                <th rowspan="2" style="text-align: center;">Total Items Out</th>
                <th rowspan="2" style="text-align: center;">IN (Purchase)</th>
                <th rowspan="2" style="text-align: center;">Balance Stock</th>
                <th rowspan="2" style="text-align: center;">Date Stocked</th>
                <th rowspan="2" style="text-align: center;">Date Out Stocked</th>
                <th rowspan="2" style="text-align: center;">Date In Stocked</th>
                <th colspan="3" style="text-align: center;">Action</th>
            </tr>
            <tr>              
                <?php for ($i=1; $i <=31 ; $i++) { 
                    echo "<th>$i</th>";
                 } ?>
                  <th style="text-align: center;">In</th>
                  <th style="text-align: center;">Out</th>
                  <th style="text-align: center;">Rename</th>                  
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock as $value): ?>
            <tr>
                <td style="text-align: center;">{{$no++}}</td>
                <td style="text-align: center;">{{$value->kode_barang}}</td>
                <td style="text-align: left;">{{$value->name_item}}</td>
                <td style="text-align: center;">{{$value->satuan}}</td>
                <td style="text-align: center;">{{$value->merk}}</td>
                <td style="text-align: center;">{{$value->stock_barang}}</td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 1)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 2)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 3)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 4)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 5)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 6)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 7)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 8)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 9)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 10)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 11)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 12)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 13)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 14)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 15)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 16)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 17)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 18)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 19)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 20)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 21)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 22)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 23)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 24)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 25)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 26)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 27)->where('kode_barang' ,'=', $value->kode_barang)->first();
              
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 28)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 29)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 30)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">
                <?php 
                    $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 31)->where('kode_barang' ,'=', $value->kode_barang)->first();
                     if ($out_stock != null) {
                        echo $out_stock->total_out_items;
                     }else{
                        echo "0";
                     }?>                                
                </td>
                <td style="text-align: center;">{{$value->total_out_stock}}</td>
                <td style="text-align: center;">{{$value->in_purchase}}</td>
                <td style="text-align: center;">{{$value->balance_stock}}</td>
                <td style="text-align: center;">{{date('M, d-Y', strtotime($value->date_stock))}}</td>
                <td style="text-align: center;"><?php if ($value->date_outed != 0): ?>
                    {{date('M, d-Y', strtotime($value->date_outed))}}
                    <?php else: ?>                        
                    <?php endif ?>
                </td>
                <td style="text-align: center;"><?php if ($value->date_inted != 0): ?>
                    {{date('M, d-Y', strtotime($value->date_inted))}}
                    <?php else: ?>                        
                    <?php endif ?>
                </td>
                 <td><a href="{{route('indexInStockStationaryWater', [$value->id])}}" class="btn btn-sm btn-primary">In (purchase)</a></td>
                 <td><a href="{{route('indexOutStockStationaryWater', [$value->id])}}" class="btn btn-sm btn-warning">Out Item</a></td>
                 <td><a href="{{route('editStockStationaryWater', [$value->id])}}" class="btn btn-sm btn-default">Rename Item</a></td>                
            </tr>
            <?php endforeach ?>
            <tfoot>
                <?php use App\Stationary_transaction; ?>
                <tr>
                    <th colspan="5" style="text-align: right;">Total</th>
                    <th style="text-align: center;">{{$stock_awal}}</th>
                    <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 1)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 2)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 3)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 4)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 5)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 6)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 7)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 8)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 9)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 10)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 11)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 12)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 13)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 14)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 15)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 16)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 17)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 18)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 19)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 20)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 21)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 22)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 23)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 24)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 25)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 26)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 27)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 28)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 29)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 30)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->wherein('kode_barang', [171, 172])->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 31)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                  <th style="text-align: center;">{{$total_items_exited}}</th>
                  <th style="text-align: center;">{{$total_in_purchase}}</th>
                  <th style="text-align: center;">{{$total_balance_stock}}</th>
                  <th colspan="6"></th>
                </tr>
            </tfoot>
        </tbody>
    </table>
</div>
</div>
<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
    @include('assets_script_7')
@stop