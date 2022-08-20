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
        <h1 class="page-header">Stationary In Stock</h1> 
    </div>
</div>
<div class="row">
    <div class="col-lg-10">
        <div>
            <form action="{{route('Statoonery/storeInStock', [$stocks->id])}}" method="POST" enctype="multipart/form-data">
               {{ csrf_field() }}
               <div class="form-group">
                <label for="kode">Code Item</label>
                <input type="text" name="kode" value="{{$stocks->kode_barang}}" readonly="true" required="true" class="form-control">
                <input type="hidden" name="id" value="{{$stocks->id}}">
              </div>
              <div class="form-group">
                <label for="nama_item">Item name</label>
                <input type="text" name="nama_item" value="{{$stocks->name_item}}" readonly="true" required="true" class="form-control">
              </div>
              <div class="form-group">
                <label for="merek">Brand</label>
                <input type="text" name="merek" value="{{$stocks->merk}}" readonly="true" required="true" class="form-control">
              </div>
              <div class="form-group">
                <label for="date_stock">Date In Item</label>
                <input type="date" name="date_stock" required="true" class="form-control">
              </div>
              <div class="form-group">
                <label for="jumlah">Amount</label>
                 <input type="number" name="jumlah" min="1" required="true" class="form-control" id="jumlah" />
              </div>  
              <button type="submit" class="btn btn-warning btn-sm">Save In Item</button>
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
