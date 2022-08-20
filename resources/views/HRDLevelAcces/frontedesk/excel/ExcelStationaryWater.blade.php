<!DOCTYPE html>
<html>
<head>
    <title>Stationary Stock</title>
</head> 
<body>
<div class="cointainer-fluid">
    <div class="row">
        <div class="col-lg-12" style="margin-left: 0px;">
            <table>
                <thead>
                    <tr>
                        <th colspan="5">PT. KINEMA SYTRANS MULTIMEDIA</th>
                    </tr>
                    <tr>
                        <th colspan="5">STATIONERY INVENTORY STOCK</th>
                    </tr>
                    <tr>
                        <th colspan="5">PERIODE {{strtoupper(date('F Y'))}}</th>
                        <?php for ($i=1; $i <=30 ; $i++) { 
                            echo "<th></th>";
                        } ?>
                        <th colspan="6" style="text-align: right;">Update On : {{date('M, d Y')}}</th>
                    </tr>
                </thead>
            </table>         
        </div>
    </div>
    <div class="row">
        <table class="table table-hover" border="1">
    <thead>
        <tr>
             <th rowspan="2" style="text-align: center;">No</th>
                <th rowspan="2" style="text-align: center;">Category</th>
                <th rowspan="2" style="text-align: center;">Code Item</th>                
                <th rowspan="2" style="text-align: center;">Item</th>
                <th rowspan="2" style="text-align: center;">UOM</th>
                <th rowspan="2" style="text-align: center;">Brand</th>
                <th rowspan="2" style="text-align: center;">Stock</th>
                <th colspan="31" style="text-align: center;">Date Items Out {{date('F Y')}}</th>
                <th rowspan="2" style="text-align: center;">Total Items Out</th>
                <th rowspan="2" style="text-align: center;">In (Purchase)</th>
                <th rowspan="2" style="text-align: center;">Balance Stock</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>         
           <?php for ($i=1; $i <=31 ; $i++) { 
                    echo "<th>$i</th>";
                 } ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        use App\stationary_stock;
        use App\Stationary_transaction;
        use App\stationary_count;
        foreach ($kategori as $value): ?>
        <tr>
            <td style="text-align: center;">{{$no++}}</td>
            <td bgcolor="lightgrey">{{$value->kategori_stock}}</td>
             <?php 
                        $kacau = stationary_stock::where('kode_kategory', '=', $value->unik_kategori)->orderBy('kode_barang', 'asc')->get();                      
                        foreach ($kacau as $kacauu): ?>
                        <tr>
                             <td colspan="2"></td>
                            <td style="text-align: center;">{{$kacauu->kode_barang}}</td>
                            <td >{{$kacauu->name_item}}</td>
                            <td style="text-align: center;">{{$kacauu->satuan}}</td> 
                            <td style="text-align: center;">{{$kacauu->merk}}</td> 
                            <td style="text-align: center;">{{$kacauu->stock_barang}}</td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 1)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 2)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 3)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 4)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 5)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 6)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 7)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 8)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 9)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 10)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 11)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 12)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 13)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 14)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 15)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 16)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 17)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 18)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 19)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 20)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 21)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 22)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 23)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 24)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 25)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 26)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 27)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 28)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 29)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 30)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;"><?php 
                                $out_stock = stationary_count::select('*')->whereMONTH('date_out_stock_historical', '=', date('m'))->whereYEAR('date_out_stock_historical', '=', date('Y'))->whereDAY('date_out_stock_historical', '=', 31)->where('kode_barang' ,'=', $kacauu->kode_barang)->first();
                                if ($out_stock != null) {
                                    echo $out_stock->total_out_items;
                                }else{
                                    echo "0";
                                }?>                                
                            </td>
                            <td style="text-align: center;">{{$kacauu->total_out_stock}}</td>
                            <td style="text-align: center;">{{$kacauu->in_purchase}}</td>
                            <td style="text-align: center;">{{$kacauu->balance_stock}}</td>
                             <?php endforeach ?>
        </tr> 
        <?php endforeach ?>
    </tbody>
     <tfoot>
            <tr>
                <th colspan="5"></th>
                <th  style="text-align: center;" >Total</th>
                <th  style="text-align: center;" >{{$stock_awal}}</th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 1)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 2)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 3)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 4)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 5)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 6)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 7)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 8)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 9)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 10)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 11)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 12)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 13)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 14)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 15)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 16)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 17)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 18)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 19)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 20)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 21)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 22)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 23)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 24)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 25)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 26)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 27)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 28)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 29)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 30)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                <th style="text-align: center;"><?php 
                    $total_date = Stationary_transaction::select('out_stock')->where('status_transaction', 2)->whereYEAR('date_out_stock', date('Y'))->whereMONTH('date_out_stock', date('m'))->whereDAY('date_out_stock', 31)->pluck('out_stock');
                    echo $total_date->sum();
                 ?></th>
                 <th style="text-align: center;">{{$total_items_exited}}</th>
                 <th style="text-align: center;">{{$total_in_purchase}}</th>
                 <th style="text-align: center;">{{$total_balance_stock}}</th>                
            </tr>
            </tfoot>
        </table> 
    </div>
     <div class="row">
        <div class="col-lg-12" style="margin-left: 0px;">
            <table>
                <tfoot>
                    <tr>
                        
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="4" style="text-align: center"> Prepare By</td>
                        <td colspan="20" style="text-align: center"> Acknowledged By</td>
                        <td colspan="13" style="text-align: center"> Approved By</td>
                    </tr>

                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>                    

                    <tr>
                        <td colspan="2"></td>
                        <td colspan="4" style="text-align: center; text-decoration: underline;">Wira Audika</td>
                        <td colspan="20" style="text-align: center; text-decoration: underline;">Wahyuni Hasan</td>
                        <td colspan="13" style="text-align: center; text-decoration: underline;">Ghea Lisanova</td>
                    </tr>
                     <tr>
                        <td colspan="2"></td>
                        <td colspan="4" style="text-align: center;">Receptionist Cum Admin</td>
                        <td colspan="20" style="text-align: center;">HR & GA Manager</td>
                        <td colspan="13" style="text-align: center;">GM</td>
                    </tr>
                </tfoot>
            </table>         
        </div>
    </div>
</div>
</body>
</html>