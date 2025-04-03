@extends('admin.body.adminmaster')

@section('admin')

<div class="container-fluid mt-5">
  <div class="row">
    <div class="col-md-12">
      <div class="white_shd full margin_bottom_30">
        <div class="full graph_head">
          <div class="d-flex justify-content-between">
            <h2>Bank Details</h2>
          </div>
        </div>
        <div class="table_section padding_infor_info">
          <div class="table-responsive-sm">
            <table id="example" class="table table-striped" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>Id</th>
                  <th>Name</th>
                  <th>Account ID</th>
                  <th>Last Update</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data as $row)
                <tr>
                  <td>{{$row->id}}</td>
                  <td>{{$row->beneficiaryname ?? 'N/A'}}</td>
                  <td>{{$row->bankid ?? 'N/A'}}</td>
                  <td>{{$row->created_at}}</td>
                  <td>
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" onclick="setModalData({{ $row->id }}, '{{ $row->beneficiaryname }}', '{{ $row->bankid }}')">Edit</button>
                  </td>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Bank Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form action="{{ route('update.bankdetails', ':id') }}" method="POST" id="editForm">
  @csrf
  @method('PUT')
  <input type="hidden" name="id" id="editId">
  <div class="form-group">
    <label>Name</label>
    <input type="text" class="form-control" name="beneficiaryname" id="editBeneficiaryName" required>
  </div>
  <div class="form-group">
    <label>Account ID</label>
    <input type="text" class="form-control" name="bankid" id="editBankId" required>
  </div>
  <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

      </div>
    </div>
  </div>
</div>

<script>
function setModalData(id, beneficiaryname, bankid) {
  document.getElementById('editId').value = id;
  document.getElementById('editBeneficiaryName').value = beneficiaryname;
  document.getElementById('editBankId').value = bankid;

  let form = document.getElementById('editForm');
  form.action = `{{ route('update.bankdetails', ':id') }}`.replace(':id', id);
}

</script>

@endsection
