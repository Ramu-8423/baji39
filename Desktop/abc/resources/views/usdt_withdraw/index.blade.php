@extends('admin.body.adminmaster')

@section('admin')
<div class="container-fluid">
    <div class="row">

<div class="col-md-12">
    <div class="white_shd full margin_bottom_30">
       <div class="full graph_head">
          <div class="heading1 margin_0 d-flex">
             <h2>Withdrawl List</h2>
            <!-- <form action="{{route('widthdrawl.all_success')}}" method="post">-->
            <!--     @csrf-->
            <!--<button type="submit" class="btn btn-primary"  style="margin-left:550px;">All Approve</button> -->
            <!--</form>-->
          </div>
       </div>
       <div class="table_section padding_infor_info">
          <div class="table-responsive-sm">
             <table id="example" class="table table-striped" style="width:100%">
                <thead class="thead-dark">
                   <tr>
                     <th>Id</th>
                     <th>UserId</th>
                     <th>Mobile</th>
                     <th>Amount</th>
                     <th>Type</th>
                     <th>Requested Phone</th>
                      <th>Order Id</th>
                     <th>Status</th>
                    
                     <th>Date</th>
                   </tr>
                </thead>
                <tbody>
                  @foreach($widthdrawls as $item)
                   <tr>
                      <td>{{$item->id}}</td>
                      <td>{{$item->user_id}}</td>
                      <td>{{$item->mobile}}</td>   
                       <td>{{$item->amount}}</td>
                       <td>
						@if($item->type == 1)
							<img src="https://root.baji39.com/images/logo/naman.png" alt="logo" width="85" height="35">
						@elseif($item->type == 2)
							<img src="https://root.baji39.com/images/logo/images__2_-removebg-preview.png" alt="logo" width="85" height="35">
						@endif
					</td>
                      <td>{{$item->user_bank_id}}</td>
                      <td>{{$item->order_id}}</td>
                    <td>
                  @if($item->status == 1)
                  <div class="dropdown">
                    <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Pending
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item text-success" href="{{ route('usdt_widthdrawl.success', $item->id) }}">
                          Success
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item text-danger" href="{{ route('widthdrawl.reject', $item->id) }}">
                          Reject
                        </a>
                      </li>
                    </ul>
                  </div>
  
                  @elseif($item->status == 2)
                  <button class="btn btn-success">Success</button>
                
                  @elseif($item->status == 3)
                  <button class="btn btn-danger">Reject</button>
                
                  @endif
                </td>

                      <td>{{$item->created_at}}</td>
                   </tr>
                  @endforeach
                </tbody>
             </table>
          </div>
       </div>
    </div>
 </div>
</div>
</div> 
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

 @endsection

