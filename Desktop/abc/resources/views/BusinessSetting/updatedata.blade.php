@extends('admin.body.adminmaster')

@section('admin')
<div class="container-fluid mt-3" style="margin-bottom: 60px;">
	@if(session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

 <p style="color : black; font-weight: bold; font-size: 20px;">Update business settings</p>
    <form action="{{ route('business_update.update') }}" method="post">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->title)) }}</td>
                    <td>
                        <input type="text" name="longtext[{{ $item->id }}]" class="form-control" value="{{ $item->longtext }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection
