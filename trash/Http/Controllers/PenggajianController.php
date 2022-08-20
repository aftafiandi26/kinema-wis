<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Facades\Datatables;

use App\Cabang;
use App\GolonganGaji;
use App\Jabatan;
use App\Karyawan;
use App\Penggajian;
use App\PenggajianDetail;

class PenggajianController extends Controller {

    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

	public function indexPenggajian()
	{
		$bulan 		= ['' => '-Select-'];
		$tahun 		= ['' => '-Select-'];
		$list_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($list_bulan as $value)
            $bulan[$value] = $value;
        for ($i = 2017; $i <= date('Y'); $i++)
            $tahun[$i] = $i;

		return View::make('penggajian.index')->with(['bulan' => $bulan, 'tahun' => $tahun]);
	}

	public function previewPenggajian(Request $request)
	{
		$bulan = 'Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember';
		$rules = [
            'bulan' => 'required|in:'.$bulan,
            'tahun'	=> 'required|integer|between:2017,'.date('Y')
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('penggajian')
                ->withErrors($validator)
            	->withInput();
        } else {
        	if ($request->input('bulan') === 'Januari') {
        		$bulan1 = '01';
        	} elseif ($request->input('bulan') === 'Februari') {
        		$bulan1 = '02';
        	} elseif ($request->input('bulan') === 'Maret') {
        		$bulan1 = '03';
        	} elseif ($request->input('bulan') === 'April') {
        		$bulan1 = '04';
        	} elseif ($request->input('bulan') === 'Mei') {
        		$bulan1 = '05';
        	} elseif ($request->input('bulan') === 'Juni') {
        		$bulan1 = '06';
        	} elseif ($request->input('bulan') === 'Juli') {
        		$bulan1 = '07';
        	} elseif ($request->input('bulan') === 'Agustus') {
        		$bulan1 = '08';
        	} elseif ($request->input('bulan') === 'September') {
        		$bulan1 = '09';
        	} elseif ($request->input('bulan') === 'Oktober') {
        		$bulan1 = '10';
        	} elseif ($request->input('bulan') === 'November') {
        		$bulan1 = '11';
        	} elseif ($request->input('bulan') === 'Desember') {
        		$bulan1 = '12';
        	}

        	$bulan 	  = $request->input('bulan');
        	$tahun 	  = $request->input('tahun');
        	$data  	  = (object) ['bulan' => $bulan, 'bulan1' => $bulan1, 'tahun' => $tahun];
        	$karyawan = Karyawan::whereRaw('karyawan.created_at <= "'.$tahun.'-'.$bulan1.'-31 23:59:59"')->joinCabang()->joinGolonganGaji()->joinJabatan()->select(['karyawan.nk', 'karyawan.name', 'karyawan.tanggal_lahir', 'karyawan.jenis_kelamin', 'karyawan.created_at', 'jabatan.jabatan', 'cabang.cabang', 'golongan_gaji.gaji_pokok', 'karyawan.tempat_tinggal'])->get();

        	return View::make('penggajian.preview')->with(['data' => $data, 'karyawan' => $karyawan]);
	    }
	}

	public function printPenggajian(Request $request)
	{
		if ($request->has('redirect') && $request->input('redirect') === '1') {
            return Redirect::route('penggajian')->withInput();
        } else {
			$bulan = 'Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember';
			$rules = [
	            'bulan' => 'required|in:'.$bulan,
	            'tahun'	=> 'required|integer|between:2017,'.date('Y')
	        ];
	        $validator = Validator::make($request->all(), $rules);

	        if ($validator->fails()) {
	            return Redirect::route('penggajian')
	                ->withErrors($validator)
	            	->withInput();
	        } else {
	        	if ($request->input('bulan') === 'Januari') {
	        		$bulan1 = '01';
	        	} elseif ($request->input('bulan') === 'Februari') {
	        		$bulan1 = '02';
	        	} elseif ($request->input('bulan') === 'Maret') {
	        		$bulan1 = '03';
	        	} elseif ($request->input('bulan') === 'April') {
	        		$bulan1 = '04';
	        	} elseif ($request->input('bulan') === 'Mei') {
	        		$bulan1 = '05';
	        	} elseif ($request->input('bulan') === 'Juni') {
	        		$bulan1 = '06';
	        	} elseif ($request->input('bulan') === 'Juli') {
	        		$bulan1 = '07';
	        	} elseif ($request->input('bulan') === 'Agustus') {
	        		$bulan1 = '08';
	        	} elseif ($request->input('bulan') === 'September') {
	        		$bulan1 = '09';
	        	} elseif ($request->input('bulan') === 'Oktober') {
	        		$bulan1 = '10';
	        	} elseif ($request->input('bulan') === 'November') {
	        		$bulan1 = '11';
	        	} elseif ($request->input('bulan') === 'Desember') {
	        		$bulan1 = '12';
	        	}

	        	$bulan 	           = $request->input('bulan');
	        	$tahun 	           = $request->input('tahun');
	        	$data  	           = (object) ['bulan' => $bulan, 'bulan1' => $bulan1, 'tahun' => $tahun];
	        	$karyawan          = Karyawan::whereRaw('karyawan.created_at <= "'.$data->tahun.'-'.$data->bulan1.'-31 23:59:59"')->joinCabang()->joinGolonganGaji()->joinJabatan()->select(['karyawan.id', 'karyawan.nk', 'karyawan.name', 'karyawan.tanggal_lahir', 'karyawan.jenis_kelamin', 'karyawan.created_at', 'jabatan.jabatan', 'cabang.cabang', 'golongan_gaji.gaji_pokok', 'karyawan.tempat_tinggal'])->get();
	        	$penggajian        = ['bulan' => $bulan, 'tahun' => $tahun];
	        	$penggajian_detail = [];

	        	if (Penggajian::where($penggajian)->count() === 0) {
		        	$id_penggajian = Penggajian::insertGetId($penggajian);

		        	foreach ($karyawan as $key => $value) {
		        		$tunjangan_rumah = '';
		        		$tunjangan_jabatan = '';

		        		if ($value->tempat_tinggal === "Rumah Pribadi") {
		        			$tunjangan_rumah = 100000;
		        		} else {
		        			$tunjangan_rumah = 0;
		        		}

		        		if ($value->jabatan === "Kepala Mekanik") {
		        			$tunjangan_jabatan = 100000;
		        		} else {
		        			$tunjangan_jabatan = 0;
		        		}

		        		$penggajian_detail[$key] = [
		        			'id_penggajian'     => $id_penggajian,
		        			'id_karyawan'       => $value->id,
		        			'gaji_pokok'        => $value->gaji_pokok,
		        			'tunjangan_rumah'   => $tunjangan_rumah,
		        			'tunjangan_jabatan' => $tunjangan_jabatan
		        		];
		        	}

		        	PenggajianDetail::insert($penggajian_detail);

		        	Excel::create('Laporan Penggajian Karyawan ('.$data->bulan1.'-'.$data->tahun.')', function($excel) use($data, $karyawan) {
		                $excel->sheet($data->bulan1.'-'.$data->tahun, function($sheet) use($data, $karyawan) {
		                    $sheet->loadView('penggajian.print', ['data' => $data, 'karyawan' => $karyawan]);
		                    $sheet->setFreeze('B7');
		                    $sheet->mergeCells('A1:D1');
		                });
		            })->export('xls')->download('xls');
		        } else {
		        	return Redirect::route('penggajian')->with(['getError' => Lang::get('messages.payroll_exists', $penggajian)])->withInput();
		        }
		    }
		}
	}

	public function indexRiwayat()
	{
		return View::make('penggajian.riwayat');
	}

	public function getIndexRiwayat()
	{
		$select = Penggajian::select(['penggajian.id', 'penggajian.bulan', 'penggajian.tahun']);

		return Datatables::of($select)
			->add_column('actions',
				Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'riwayat-penggajian/detail\', [$id]) }}', 'class' => 'folder-open'])
				.Lang::get('messages.btn_warning', ['title' => 'Cetak Laporan', 'url' => '{{ URL::route(\'riwayat-penggajian/print\', [$id]) }}', 'class' => 'print'])
		    )
			->make();
	}

	public function detailRiwayat($id)
	{
		$penggajian        = Penggajian::find($id);
		$penggajian_detail = PenggajianDetail::where('id_penggajian', $penggajian->id)->get();
		$karyawan          = '';

		foreach ($penggajian_detail as $key => $value) {
			$k    = Karyawan::find($value->id_karyawan);
			$numb = $key + 1;

			$karyawan .= "
				<div>
                    <div style='display: inline-table;'>
                        <strong>$numb. </strong>
                    </div>

                    <div style='display: inline-table;'>
		                <strong>No. Karyawan :</strong> $k->nk<br>
		                <strong>Name :</strong> $k->name<br>
		                <strong>Tanggal Lahir :</strong> $k->tanggal_lahir<br>
		                <strong>Jenis Kelamin :</strong> $k->jenis_kelamin<br>
		                <strong>Tanggal Gabung :</strong> ".date_format(date_create($k->created_at), "d-m-Y")."<br>
		                <strong>Jabatan :</strong> ".Jabatan::where('id', GolonganGaji::where('id', $k->golongan_gaji)->value('jabatan'))->value('jabatan')."<br>
		                <strong>Cabang :</strong> ".Cabang::where('id', $k->cabang)->value('cabang')."<br>
		                <strong>Gaji Pokok :</strong> Rp. ". number_format($value->gaji_pokok, 0, ",", ".") ."<br>
		                <strong>Tunjangan Jabatan :</strong> Rp. ". number_format($value->tunjangan_jabatan, 0, ",", ".") ."<br>
		                <strong>Tunjangan Rumah :</strong> Rp. ". number_format($value->tunjangan_rumah, 0, ",", ".") ."<br>
		            </div>
		        </div><br>";
		}

    	$return = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='showModalLabel'>Detail Penggajian</h4>
            </div>
            <div class='modal-body'>
                <div class='well'>
                    <h4><strong><u>Detail Penggajian</u></strong></h4>
                    <strong>Periode :</strong> $penggajian->bulan $penggajian->tahun<br><br>

                    <h4><strong><u>Detail Karyawan</u></strong></h4>
                    ".substr($karyawan, 0, -4)."
                </div>
            </div>
            <div class='modal-footer'>
                <a class='btn btn-primary' href='".URL::route('riwayat-penggajian/print', [$id])."'>Cetak Laporan</a>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Tutup</button>
            </div>
        ";

		return $return;
	}

	public function printRiwayat($id)
	{
    	$penggajian = Penggajian::find($id);

		if ($penggajian->bulan === 'Januari') {
    		$bulan1 = '01';
    	} elseif ($penggajian->bulan === 'Februari') {
    		$bulan1 = '02';
    	} elseif ($penggajian->bulan === 'Maret') {
    		$bulan1 = '03';
    	} elseif ($penggajian->bulan === 'April') {
    		$bulan1 = '04';
    	} elseif ($penggajian->bulan === 'Mei') {
    		$bulan1 = '05';
    	} elseif ($penggajian->bulan === 'Juni') {
    		$bulan1 = '06';
    	} elseif ($penggajian->bulan === 'Juli') {
    		$bulan1 = '07';
    	} elseif ($penggajian->bulan === 'Agustus') {
    		$bulan1 = '08';
    	} elseif ($penggajian->bulan === 'September') {
    		$bulan1 = '09';
    	} elseif ($penggajian->bulan === 'Oktober') {
    		$bulan1 = '10';
    	} elseif ($penggajian->bulan === 'November') {
    		$bulan1 = '11';
    	} elseif ($penggajian->bulan === 'Desember') {
    		$bulan1 = '12';
    	}

		$data  	    = (object) ['bulan' => $penggajian->bulan, 'bulan1' => $bulan1, 'tahun' => $penggajian->tahun];
    	$karyawan   = Karyawan::whereRaw('karyawan.created_at <= "'.$data->tahun.'-'.$data->bulan1.'-31 23:59:59"')->joinCabang()->joinGolonganGaji()->joinJabatan()->select(['karyawan.id', 'karyawan.nk', 'karyawan.name', 'karyawan.tanggal_lahir', 'karyawan.jenis_kelamin', 'karyawan.created_at', 'jabatan.jabatan', 'cabang.cabang', 'golongan_gaji.gaji_pokok', 'karyawan.tempat_tinggal'])->get();

		Excel::create('Laporan Penggajian Karyawan ('.$data->bulan1.'-'.$data->tahun.')', function($excel) use($data, $karyawan) {
            $excel->sheet($data->bulan1.'-'.$data->tahun, function($sheet) use($data, $karyawan) {
                $sheet->loadView('penggajian.print', ['data' => $data, 'karyawan' => $karyawan]);
                $sheet->setFreeze('B7');
                $sheet->mergeCells('A1:D1');
            });
        })->export('xls')->download('xls');
	}
}