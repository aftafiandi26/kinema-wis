<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\stationary_stock;
use App\Stationary_transaction;
use App\stationary_count;
use App\stationary_kategori;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Storage;
use Yajra\Datatables\Facades\Datatables;
use Yajra\DataTables\Html\Builder;

class FrontdeskController extends Controller
{
	public function __construct()
    {
        $this->middleware(['auth', 'active', 'hr']);
    }

	public function indexstokstoonery()
    {	
    	$kategori = stationary_kategori::where('id', '!=', 18)->orderBy('unik_kategori', 'asc')->get();
    	$stock = stationary_stock::orderBy('kode_barang', 'asc')->get(); 
    	$out_stock = Stationary_transaction::whereMONTH('date_out_stock', '=', date('m'))->whereYEAR('date_out_stock', '=', date('Y'))->get();
    	$stock_awal = stationary_stock::pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::pluck('balance_stock')->sum();
    	
    	return view('HRDLevelAcces.frontedesk.index', [
    		'kategori' => $kategori,
    		'stock' => $stock, 
    		'out_stock' => $out_stock, 
    		'stock_awal' => $stock_awal,
    		'total_items_exited' => $total_items_exited,
    		'total_in_purchase'		=> $total_in_purchase,
    		'total_balance_stock'	=> $total_balance_stock,
    	]);    
    }


    public function getstokstoonery()
    {	    	

    	$select = stationary_stock::select(['id', 'kode_barang', 'name_item', 'satuan', 'merk', 'stock_barang', 'total_out_stock', 'in_purchase', 'balance_stock', 'date_stock'])
		->whereMONTH('date_stock', date('m'))
		
		->get();	  	
		return Datatables::of($select)
			->addColumn('date1', '
				@php
				echo "tes";
				@endphp
				', 6)
			->addColumn('date2', 'date2', 7)
			->addColumn('date3', 'date3', 8)
			->addColumn('date4', 'date4', 9)
			->addColumn('date5', 'date5', 10)
			->addColumn('date6', 'date6', 11)
			->addColumn('date7', 'date7', 12)
			->addColumn('date8', 'date8', 13)
			->addColumn('date9', 'date9', 14)
			->addColumn('date10', 'date10', 15)
			->addColumn('date11', 'date11', 16)
			->addColumn('date12', 'date12', 17)
			->addColumn('date13', 'date13', 18)
			->addColumn('date14', 'date14', 19)
			->addColumn('date15', 'date15', 20)
			->addColumn('date16', 'date16', 21)
			->addColumn('date17', 'date17', 22)
			->addColumn('date18', 'date18', 23)
			->addColumn('date19', '
				@php
				echo "tes";
				@endphp
				', 24)
			->addColumn('date20', 'date20', 25)
			->addColumn('date21', 'date21', 26)
			->addColumn('date22', 'date22', 27)
			->addColumn('date23', 'date23', 28)
			->addColumn('date24', 'date24', 29)
			->addColumn('date25', 'date25', 30)
			->addColumn('date26', 'date26', 31)
			->addColumn('date27', 'date27', 32)
			->addColumn('date28', 'date28', 33)
			->addColumn('date29', 'date29', 34)
			->addColumn('date30', 'date30', 35)
			->addColumn('date31', 'date31', 36)
			->addColumn('action1', '<a href="{{route("Statoonery/indexInStock", [$id])}}"class="btn btn-sm btn-primary">IN</a>')	
			->addColumn('action2', '<a href="{{route("Statoonery/indexOutStock", [$id])}}" class="btn btn-sm btn-warning">OUT</a>')					
			->make();   
    }

    public function indexOutStock($id)
    {
    	$stock = stationary_stock::where('id', $id)->first();
    	return view('HRDLevelAcces.frontedesk.outStock', ['stocks' => $stock]);
    }

    public function storeOutStock(Request $request, $id)
    {
    	 $rules = [
            'date_stock' => 'required|date',
            'jumlah'    => 'required|numeric'
        ];

        $data = [           
            'kode_barang' => $request->input('kode'),
            'out_stock'  => $request->input('jumlah'),
            'date_out_stock' => $request->input('date_stock'),
            'status_transaction' => 2,
        ];

        $validator = Validator::make($request->all(), $rules);
    	
	    if ($validator->fails()) {
	        return Redirect::route('Statoonery/indexOutStock', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	Stationary_transaction::insert($data);
	    	stationary_stock::where('kode_barang', $request->input('kode'))->update([
	    		'date_outed' => $request->input('date_stock')
	    	]);
	    	$cek_date_total = stationary_count::select('date_out_stock_historical')->where('date_out_stock_historical', '=', $request->input('date_stock'))->where('kode_barang', '=', $request->input('kode'))->value('date_out_stock_historical');
	    	if ($cek_date_total != null) {
	    		$get_total_out_items = stationary_count::select('total_out_items')->where('kode_barang', '=', $request->input('kode'))->where('date_out_stock_historical', '=', $request->input('date_stock'))->first();
	    		$gettout = $get_total_out_items->total_out_items + $request->input('jumlah');
				   $data2 = [
			        	'kode_barang'	 => $request->input('kode'),
			        	'date_out_stock_historical' => $request->input('date_stock'),
			        	'total_out_items'  => $gettout,
			        ];
	    		stationary_count::where('date_out_stock_historical', '=', $request->input('date_stock'))->update($data2);
	    	}else{	   
	    		 $data3 = [
			        	'kode_barang'	 => $request->input('kode'),
			        	'date_out_stock_historical' => $request->input('date_stock'),
			        	'total_out_items'  => $request->input('jumlah'),			        	
			        ]; 		
	    		stationary_count::insert($data3);
	    	}	    	
	        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	        return Redirect::route('Statoonery/index');
	    }
    }

    public function indexInStock($id)
    {
    	$stock = stationary_stock::where('id', $id)->first();
    	return view('HRDLevelAcces.frontedesk.intStock', ['stocks' => $stock]);
    }

	public function storeInStock(Request $request, $id)
    {	
    	$old_stock = stationary_stock::find($id);
    	$rules = [
            'date_stock' => 'required|date',
            'jumlah'    => 'required|numeric'
        ];

        $data = [           
            'kode_barang' => $request->input('kode'),
            'in_stock'  => $request->input('jumlah'),
            'date_in_stock' => $request->input('date_stock'),
            'status_transaction' => 1,
        ];
       
        $validator = Validator::make($request->all(), $rules);

	    if ($validator->fails()) {
	        return Redirect::route('Statoonery/indexInStock', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	Stationary_transaction::insert($data);
	    	stationary_stock::where('kode_barang', $request->input('kode'))->update([
	    		'date_inted' => $request->input('date_stock'),
	    		'stock_barang' => $request->input('jumlah')+$old_stock->stock_barang
	    	]);
	        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	        return Redirect::route('Statoonery/index');
	    }
    }

    public function addStockStatoonary()
    {
    	$stationary_stock = stationary_kategori::where('id', '!=', 18)->orderBy('kategori_stock', 'asc')->get();

    	return view('HRDLevelAcces.frontedesk.addStock', ['kategori' => $stationary_stock]);
    }

    public function storeAddStockStatoonary(Request $request)
    {
		$rules = [
			'kode'			=> 'required|numeric',
			'nama_item' 	=> 'required|string',
			'merek'			=> 'required',
			'satuan'		=> 'required|string',
            'date_stock'	=> 'required|date',
            'jumlah'   		=> 'required|numeric',
            'kategori'		=> 'required|numeric',
        ];

        $data = [           
            'kode_barang'	 => $request->input('kategori').$request->input('kode'),
            'name_item' 	 => $request->input('nama_item'),
            'satuan'	     => strtolower($request->input('satuan')),
            'merk'			 => $request->input('merek'),
            'stock_barang'	 => $request->input('jumlah'),
            'total_out_stock' => 0,
            'total_in_stock'  => 0,
            'in_purchase'	  => 0,
            'balance_stock'	 => $request->input('jumlah'),
            'date_stock'	 => $request->input('date_stock'),
            'created_at'	=> date('Y-m-d'),
            'kode_kategory' => $request->input('kategori'),
        ];
        
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('Statoonery/addStockStatoonary')
	            ->withErrors($validator)
	            ->withInput();
	    } else {
		    	$coba = stationary_stock::where('kode_barang', 'like', '%'.$request->input('kategori').$request->input('kode').'%')->value('kode_barang');
		    	if ($coba != $request->input('kategori').$request->input('kode')) {
		    		stationary_stock::insert($data);	    
				    Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
				    return Redirect::route('Statoonery/index');
		    	} else {
 					Session::flash('message', Lang::get('messages.act_failed', ['act' => 'Inputed Databes', 'data' => 'Category Stationary']));
		    		Redirect::route('Statoonery/addStockStatoonary');
		    	}
		    }
	}

    public function editStationaryName($id)
    {
    	$stock = stationary_stock::where('id', $id)->first();

    	return view('HRDLevelAcces.frontedesk.EditStock', ['stocks' => $stock]);
    }

    public function saveStationaryName(Request $request, $id)
    {
    	$item = stationary_stock::find($id);



    	$rules = [			
			'nama_item' 	=> 'required',
			'merek'			=> 'required',
			'satuan'		=> 'required',
			'kode'			=> 'required|numeric|min:0',
			'total_stock'		=> 'required|numeric|min:0',
			'total_in_stock'	=> 'required|numeric|min:0',			
			'total_out_stock'	=> 'required|numeric|min:0',
			'in_purchase'		=> 'required|numeric|min:0',
			'balance_stock'		=> 'required|numeric|min:0',
        ];

        $data = [
            'name_item' 	 => $request->input('nama_item'),
            'satuan'    	 => strtolower($request->input('satuan')),
            'merk'			 => $request->input('merek'),  
            'kode_barang'	 => $request->input('kode'), 
            'stock_barang'		=> $request->input('total_stock'),
            'total_in_stock'	=> $request->input('total_in_stock'),
            'total_out_stock'	=> $request->input('total_out_stock'),
            'in_purchase'		=> $request->input('in_purchase'),
            'balance_stock'		=> $request->input('balance_stock'),        
        ];
        
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('editStationaryName', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	stationary_stock::where('id', $id)->update($data);	    
	        Session::flash('success', Lang::get('messages.data_updated', ['data' => 'Data Stock '.$item->name_item.'']));
	        return Redirect::route('Statoonery/index');
	    }
    }

    public function GenerateStocked()
    {     
    	$kategori = stationary_kategori::where('id', '!=', 18)->orderBy('unik_kategori', 'asc')->get();	
	    
		$pdf = App::make('dompdf.wrapper');  
		ini_set("memory_limit", '512M');      
		$pdf->loadview('HRDLevelAcces.frontedesk.generatePDF', ['kategori' => $kategori])
		->setPaper('A4', 'landscape')
		->setOptions(['dpi' => 180, 'defaultFont' => 'sans-serif']);
		return $pdf->stream();		
    }

    public function indexKategoryStationary()
    {
    	return view('HRDLevelAcces.frontedesk.kategory_stock.index');
    }

    public function dataIndexCategory()
    {
    	$modal = stationary_kategori::orderBy('unik_kategori', 'asc')->get();

    	return Datatables::of($modal)
    			->addIndexColumn()
    			->addColumn('action', function(stationary_kategori $stationary){
    				$edit = "<a href=".route('editKategoryStationary', [$stationary->id])." class='btn btn-xs btn-warning' title='Edit ".$stationary->kategori_stock."'><i class='fa fa-pencil'></i></a>";

    				return $edit;
    			})
    			->rawColumns(['action'])
    			->make(true);
    }

    public function addKategoryStationary()
    {    	
    	return view('HRDLevelAcces.frontedesk.kategory_stock.addKategory');
    }   

    public function storeKategoryStationary(Request $request)
    {
    	$rules = [
			'kode'		=> 'required|numeric',
			'category' 	=> 'required',
			
        ];

        $data = [           
            'unik_kategori'	 => $request->input('kode'),
            'kategori_stock' => strtoupper($request->input('category'))          
        ];
     
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('addKategoryStationary')
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	
	    	$coba = stationary_kategori::where('unik_kategori', 'like', '%'.$request->input('kode').'%')->value('unik_kategori'); 
	    	if ($coba !=  $request->input('kode')) {
	    	  		stationary_kategori::insert($data);	
	    	  		 Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	       			 return Redirect::route('indexKategoryStationary');
	    	  	} else {
	    	  		 Session::flash('message', Lang::get('messages.act_failed', ['act' => 'Inputed Databes', 'data' => 'Category Stationary']));
	       			 return Redirect::route('addKategoryStationary');
	    	  	}
	    }
    }

    public function editKategoryStationary($id)
    {
    	$stationary_kategori = stationary_kategori::find($id);
    	return view('HRDLevelAcces.frontedesk.kategory_stock.editKategory', ['stationary_kategori' => $stationary_kategori]);
    }

    public function SaveKategoryStationary(request $request, $id)
    {
    	$rules = [			
			'code' 	=> 'required|unique:stationary_kategory,unik_kategori',	
			'category' 	=> 'required',			
        ];

        $data = [ 
        	'unik_kategori'			 => $request->input('code'),
            'kategori_stock' => strtoupper($request->input('category'))          
        ];
     	
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('editKategoryStationary', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	stationary_kategori::where('id', $id)->update($data);
	    	Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	       	return Redirect::route('indexKategoryStationary');    	  
	    }
    }

    public function GeneratePDFNameKategori($id)
    {   	
		$getKategori = stationary_kategori::find($id);
	    $no = 1;
	    $data = stationary_stock::where('kode_kategory', $getKategori->unik_kategori)->orderBy('kode_barang', 'asc')->get();
	    $stock_awal = stationary_stock::where('kode_kategory', $getKategori->unik_kategori)->whereMONTH('date_stock',date('m', strtotime('-1 month')))->pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::where('kode_kategory', $getKategori->unik_kategori)->pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::where('kode_kategory', $getKategori->unik_kategori)->pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::where('kode_kategory', $getKategori->unik_kategori)->pluck('balance_stock')->sum();

		$pdf = App::make('dompdf.wrapper');   
		$pdf->loadview('HRDLevelAcces.frontedesk.kategory_stock.generatePDFKategori', [
			'getKategori'			=> $getKategori,
			'no' 					=> $no, 
			'data' 					=> $data,
			'stock_awal' 			=> $stock_awal,
			'total_items_exited' 	=> $total_items_exited,
			'total_in_purchase'		=> $total_in_purchase,
			'total_balance_stock'	=> $total_balance_stock
		])
		->setPaper('A4', 'landscape')
		->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
		return $pdf->stream();		
    }

    public function indexStockStationaryWater()
    {
    	$no = 1;
    	$stock = stationary_stock::where('kode_kategory', '=', 17)->orderBy('kode_barang', 'asc')->get();
    	$stock_awal = stationary_stock::where('kode_kategory', '=', 17)->pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::where('kode_kategory', '=', 17)->pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::where('kode_kategory', '=', 17)->pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::where('kode_kategory', '=', 17)->pluck('balance_stock')->sum(); 

    	return view('HRDLevelAcces.frontedesk.StationaryWaterMineral.index', [
    		'no'					=> $no,
    		'stock' 				=> $stock,
    		'stock_awal' 			=> $stock_awal,
    		'total_items_exited' 	=> $total_items_exited,
    		'total_in_purchase'		=> $total_in_purchase,
    		'total_balance_stock'	=> $total_balance_stock
    	]);
    }

    public function addStockStationaryWater()
    {
    	$stationary_stock = stationary_kategori::where('id', 18)->orderBy('kategori_stock', 'asc')->get();
    	return view('HRDLevelAcces.frontedesk.StationaryWaterMineral.addStock', ['kategori' => $stationary_stock]);
    }

    public function storeAddStockStationaryWater(Request $request)
    {
		$rules = [
			'kode_barang'	=> 'required|numeric|unique:stationery_stock',
			'nama_item' 	=> 'required|string',
			'merek'			=> 'required',
			'satuan'		=> 'required|string',
            'date_stock'	=> 'required|date',
            'jumlah'   		=> 'required|numeric',
            'kategori'		=> 'required|numeric',
        ];

        $data = [           
            'kode_barang'	 => $request->input('kategori').$request->input('kode_barang'),
            'name_item' 	 => $request->input('nama_item'),
            'satuan'	     => strtolower($request->input('satuan')),
            'merk'			 => $request->input('merek'),
            'stock_barang'	 => $request->input('jumlah'),
            'total_out_stock' => 0,
            'total_in_stock'  => 0,
            'in_purchase'	  => 0,
            'balance_stock'	 => $request->input('jumlah'),
            'date_stock'	 => $request->input('date_stock'),
            'created_at'	=> date('Y-m-d'),
            'kode_kategory' => $request->input('kategori'),
        ];
       	
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('addStockStationaryWater')
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	$coba = stationary_stock::where('kode_barang', 'like', '%'.$request->input('kategori').$request->input('kode_barang').'%')->value('kode_barang'); 
		    	if ($coba != $request->input('kategori').$request->input('kode_barang')) {
		    		stationary_stock::insert($data);	    
			   		Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
			    	return Redirect::route('indexStockStationaryWater');
		    	} else {
		    	 Session::flash('message',  Lang::get('messages.act_failed', ['act' => 'Inputed Databes', 'data' => 'Category Stationary']));
		    	 return Redirect::route('addStockStationaryWater');
		    	}    	
	    	
		    }
	}

	public function indexOutStockStationaryWater($id)
	{
		$stock = stationary_stock::where('id', $id)->first();
		return view('HRDLevelAcces.frontedesk.StationaryWaterMineral.outStock', ['stocks' => $stock]);
	}

	public function storeOutStockStationaryWater(Request $request, $id)
    {
    	 $rules = [
            'date_stock' => 'required|date',
            'jumlah'    => 'required|numeric'
        ];

        $data = [           
            'kode_barang' => $request->input('kode'),
            'out_stock'  => $request->input('jumlah'),
            'date_out_stock' => $request->input('date_stock'),
            'status_transaction' => 2,
        ];

        $validator = Validator::make($request->all(), $rules);
    	
	    if ($validator->fails()) {
	        return Redirect::route('indexOutStockStationaryWater', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	Stationary_transaction::insert($data);
	    	stationary_stock::where('kode_barang', $request->input('kode'))->update([
	    		'date_outed' => $request->input('date_stock')
	    	]);
	    	$cek_date_total = stationary_count::select('date_out_stock_historical')->where('date_out_stock_historical', '=', $request->input('date_stock'))->where('kode_barang', '=', $request->input('kode'))->value('date_out_stock_historical');
	    	if ($cek_date_total != null) {
	    		$get_total_out_items = stationary_count::select('total_out_items')->where('kode_barang', '=', $request->input('kode'))->where('date_out_stock_historical', '=', $request->input('date_stock'))->first();
	    		$gettout = $get_total_out_items->total_out_items + $request->input('jumlah');
				   $data2 = [
			        	'kode_barang'	 => $request->input('kode'),
			        	'date_out_stock_historical' => $request->input('date_stock'),
			        	'total_out_items'  => $gettout,
			        ];
	    		stationary_count::where('date_out_stock_historical', '=', $request->input('date_stock'))->update($data2);
	    	}else{	   
	    		 $data3 = [
			        	'kode_barang'	 => $request->input('kode'),
			        	'date_out_stock_historical' => $request->input('date_stock'),
			        	'total_out_items'  => $request->input('jumlah'),			        	
			        ]; 		
	    		stationary_count::insert($data3);
	    	}	    	
	        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	        return Redirect::route('indexStockStationaryWater');
	    }
    }

    public function indexInStockStationaryWater($id)
	{
		$stock = stationary_stock::where('id', $id)->first();
		return view('HRDLevelAcces.frontedesk.StationaryWaterMineral.intStock', ['stocks' => $stock]);
	}

	public function storeInStockStationaryWater(Request $request)
    {
		$rules = [
            'date_stock' => 'required|date',
            'jumlah'    => 'required|numeric'
        ];

        $data = [           
            'kode_barang' => $request->input('kode'),
            'in_stock'  => $request->input('jumlah'),
            'date_in_stock' => $request->input('date_stock'),
            'status_transaction' => 1,
        ];
       
        $validator = Validator::make($request->all(), $rules);

	    if ($validator->fails()) {
	        return Redirect::route('indexInStockStationaryWater', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	Stationary_transaction::insert($data);
	    	stationary_stock::where('kode_barang', $request->input('kode'))->update([
	    		'date_inted' => $request->input('date_stock')
	    	]);
	        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	        return Redirect::route('indexStockStationaryWater');
	    }
	}

	public function editStockStationaryWater($id)
    {
    	$stock = stationary_stock::where('id', $id)->first();

    	return view('HRDLevelAcces.frontedesk.StationaryWaterMineral.EditStock', ['stocks' => $stock]);
    }

    public function saveStockStationaryWater(Request $request, $id)
    {
    	$rules = [			
			'nama_item' 	=> 'required',
			'merek'			=> 'required',
			'satuan'		=> 'required',			
        ];

        $data = [
            'name_item' 	 => $request->input('nama_item'),
            'satuan'    	 => strtolower($request->input('satuan')),
            'merk'			 => $request->input('merek'),           
        ];
        
        $validator = Validator::make($request->all(), $rules);
	    if ($validator->fails()) {
	        return Redirect::route('editStockStationaryWater', [$id])
	            ->withErrors($validator)
	            ->withInput();
	    } else {
	    	stationary_stock::where('id', $id)->update($data);	    
	        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Stock']));
	        return Redirect::route('indexStockStationaryWater');
	    }
    }

    public function GenerateStockedWater()
    {   $kategori =  stationary_kategori::where('id', 18)->first();  	
	    $data = stationary_stock::where('kode_kategory', 17)->orderBy('kode_barang', 'asc')->get();
	  	$no = 1;
	  	$stock_awal = stationary_stock::where('kode_kategory', '=', 17)->pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::where('kode_kategory', '=', 17)->pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::where('kode_kategory', '=', 17)->pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::where('kode_kategory', '=', 17)->pluck('balance_stock')->sum(); 

		$pdf = App::make('dompdf.wrapper'); 
	     
		$pdf->loadview('HRDLevelAcces.frontedesk.StationaryWaterMineral.generatePDF', [
			'kategori'				=> $kategori,
			'data' 					=> $data, 
			'no' 					=> $no,
			'stock_awal' 			=> $stock_awal,
			'total_items_exited'	=> $total_items_exited,
			'total_in_purchase'		=> $total_in_purchase,
			'total_balance_stock'	=> $total_balance_stock
		])
		->setPaper('A4', 'landscape')
		->setOptions(['dpi' => 180, 'defaultFont' => 'sans-serif']);
		return $pdf->stream();		
    }

    public function ExcelStationaryStock()
    {
    	$kategori = stationary_kategori::where('id', '!=', 18)->orderBy('id', 'asc')->get();
    	$no = 1;
    	$stock_awal = stationary_stock::pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::pluck('balance_stock')->sum();

    	Excel::create('Stationery', function($excel) use($kategori, $no, $stock_awal, $total_items_exited, $total_in_purchase, $total_balance_stock) {

			    $excel->sheet('New sheet', function($sheet) use($kategori, $no, $stock_awal, $total_items_exited, $total_in_purchase, $total_balance_stock) {

			    $sheet->setOrientation('landscape');
			  	$sheet->setAutoSize(true);
		        $sheet->loadView('HRDLevelAcces.frontedesk.excel.ExcelStationary', ['kategori' => $kategori, 'no' => $no, 'stock_awal' => $stock_awal, 'total_items_exited' => $total_items_exited, 'total_in_purchase' => $total_in_purchase, 'total_balance_stock' => $total_balance_stock]);
		        $sheet->setScale(55);
		   		});
		})->export('xls');

		return back();
    }

    public function ExcelStationaryStockWater()
    {
    	$kategori = stationary_kategori::where('id', '=', 18)->get();
    	$no = 1;
    	$stock_awal = stationary_stock::where('kode_barang', 17)->pluck('stock_barang')->sum();  
    	$total_items_exited =  stationary_stock::where('kode_barang', 17)->pluck('total_out_stock')->sum(); 
    	$total_in_purchase =  stationary_stock::where('kode_barang', 17)->pluck('in_purchase')->sum(); 
    	$total_balance_stock = stationary_stock::where('kode_barang', 17)->pluck('balance_stock')->sum();

    	Excel::create('Stationery Water', function($excel) use($kategori, $no, $stock_awal, $total_items_exited, $total_in_purchase, $total_balance_stock) {

			    $excel->sheet('New sheet', function($sheet) use($kategori, $no, $stock_awal, $total_items_exited, $total_in_purchase, $total_balance_stock) {

			    $sheet->setOrientation('landscape');
			    $sheet->setAutoSize(true);
		        $sheet->loadView('HRDLevelAcces.frontedesk.excel.ExcelStationaryWater', ['kategori' => $kategori, 'no' => $no, 'stock_awal' => $stock_awal, 'total_items_exited' => $total_items_exited, 'total_in_purchase' => $total_in_purchase, 'total_balance_stock' => $total_balance_stock]);
		        $sheet->setScale(55);
		   		});
		})->export('xls');

		return back();
    }

     public function indexForfeited()
    {
        dd('k');
        return view('HRDLevelAcces.forfeited.index');
    }

//end 
}
