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
<!-- isi blade -->

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Stationery Stock</h1> 
    </div>
</div>
<div class="row">
    <div class="col-lg-10">
        <div>
            <form action="{{route('Statoonery/storeAddStockStatoonary')}}" method="POST" enctype="multipart/form-data">
               {{ csrf_field() }}
               <div class="form-group">
                <label for="kategori">Category</label>
                <select class="form-control" id="kategori" required="true" name="kategori">
                  <option value="">- choose item -</option>
                  <?php foreach ($kategori as $kk): ?>
                     <option value="{{$kk->unik_kategori}}">{{$kk->kategori_stock}}</option>
                  <?php endforeach ?>
                </select>
              </div>
               <div class="form-group">
                <label for="kode">Code Item</label>
                <input type="text" name="kode" required="true" class="form-control" id="kode">
              </div>
              <div class="form-group">
                <label for="nama_item">Item Name</label>
                <input type="text" name="nama_item" required="true" class="form-control" id="nama_item">
              </div>              
              <div class="form-group">
                <label for="merek">Brand</label>
                <input type="text" name="merek" required="true" class="form-control" id="merek">
              </div>
              <div class="form-group">
                <label for="satuan">UOM</label>
                <input type="text" min="3" id="satuan" name="satuan" required="true" class="form-control">
              </div>
              <div class="form-group">
                <label for="date_stock">Date Add Stock</label>
                <input type="date" name="date_stock" required="true" class="form-control" id="date_stock">
              </div>
              <div class="form-group">
                <label for="jumlah">Stock Item</label>
                 <input type="number" name="jumlah" min="0" required="true" class="form-control" id="jumlah" />
              </div>  
              <button type="submit" class="btn btn-warning btn-sm">Save Item</button>
              <a href="{{route('Statoonery/index')}}" class="btn btn-default btn-sm">Back to Page</a>
            </form>
        </div>
    </div>
</div>
@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
    @include('assets_script_7')
@stop
