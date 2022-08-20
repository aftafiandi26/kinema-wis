<font style="text-align: left; vertical-align: middle; font-weight: bold; font-size: 20pt;">LAPORAN PENGGAJIAN KARYAWAN</font>

<br>

<table>
    <tr>
        <td>&nbsp;</td>
        <td style="text-align: right; vertical-align: middle; font-weight: bold;">Bulan :</td>
        <td style="text-align: left; vertical-align: middle; font-weight: bold;">{!! $data->bulan !!}</td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td style="text-align: right; vertical-align: middle; font-weight: bold;">Tahun :</td>
        <td style="text-align: left; vertical-align: middle; font-weight: bold;">{!! $data->tahun !!}</td>
    </tr>
</table>

<br>

<table style="border: 1px medium #000000;">
    <tbody>
        <tr>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 5px; font-weight: bold; background-color: #D9D9D9;">No.</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 15px; font-weight: bold; background-color: #D9D9D9;">No. Karyawan</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 30px; font-weight: bold; background-color: #D9D9D9;">Name</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 15px; font-weight: bold; background-color: #D9D9D9;">Tanggal Lahir</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 15px; font-weight: bold; background-color: #D9D9D9;">Jenis Kelamin</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 15px; font-weight: bold; background-color: #D9D9D9;">Tanggal Gabung</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 20px; font-weight: bold; background-color: #D9D9D9;">Jabatan</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 15px; font-weight: bold; background-color: #D9D9D9;">Cabang</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 20px; font-weight: bold; background-color: #D9D9D9;">Gaji Pokok</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 20px; font-weight: bold; background-color: #D9D9D9;">Tunj. Jabatan</td>
            <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; width: 20px; font-weight: bold; background-color: #D9D9D9;">Tunj. Rumah</td>
        </tr>

        @foreach($karyawan as $key => $value)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $key + 1 !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->nk !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->name !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->tanggal_lahir !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->jenis_kelamin !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! date_format(date_create($value->created_at), "d-m-Y") !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->jabatan !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->cabang !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. {!! number_format($value->gaji_pokok, 0, ",", ".") !!}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. @if ($value->jabatan === "Kepala Mekanik") 100,000 @else 0 @endif</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. @if ($value->tempat_tinggal === "Rumah Pribadi") 100,000 @else 0 @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>