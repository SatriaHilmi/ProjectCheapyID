@extends('layouts.app')

@section('title', $title)

@section('addon-css')
    <link rel="stylesheet" href="{{ url('assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/modules/izitoast/css/iziToast.min.css') }}">
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="section-body">
                <h2 class="section-title">{{ $title }}</h2>
                <p class="section-lead">Daftar transaksi yang sudah dilakukan.</p>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Kode Transaksi</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->transaction_code }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ number_format($item->total, 0, ',', ',') }}</td>
                                            <td>{{ $item->payment_method }}</td>
                                            <td>
                                                @if ($item->status == 'Belum Bayar')
                                                    <span class="badge badge-danger">{{ $item->status }}</span>
                                                @elseif($item->status == 'Pembayaran Berhasil')
                                                    <span class="badge badge-success">{{ $item->status }}</span>
                                                @elseif($item->status == 'Pesanan Dikirim')
                                                    <span class="badge badge-info">{{ $item->status }}</span>
                                                @elseif($item->status == 'Pesanan Ditolak')
                                                    <span class="badge badge-warning">{{ $item->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary btn-icon icon-left"
                                                    onclick="showTransactionDetails('{{ $item->transaction_code }}')">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>

                                                <button type="button" class="btn btn-warning btn-icon icon-left"
                                                    onclick="showUpdateStatusModal('{{ $item->transaction_code }}', '{{ $item->status }}')">
                                                    <i class="fas fa-edit"></i> Update Status
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada data transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog"
        aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="transaction-details-content">
                        <!-- Transaction details will be loaded here dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Transaction Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="update-status-form" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Belum Bayar">Belum Bayar</option>
                                <option value="Pembayaran Berhasil">Pembayaran Berhasil</option>
                                <option value="Pesanan Dikirim">Pesanan Dikirim</option>
                                <option value="Pesanan Ditolak">Pesanan Ditolak</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('addon-script')
    <script src="{{ url('assets/modules/jquery.min.js') }}"></script> <!-- Ensure jQuery is included -->
    <script src="{{ url('assets/modules/bootstrap/js/bootstrap.bundle.min.js') }}"></script> <!-- Ensure Bootstrap JS is included -->
    <script src="{{ url('assets/modules/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url('assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ url('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('assets/modules/izitoast/js/iziToast.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });

        function showTransactionDetails(transactionCode) {
            $.ajax({
                url: '{{ url('transaction') }}/' + transactionCode + '/details',
                method: 'GET',
                success: function(data) {
                    let details = `
                        <p><strong>Transaction Code:</strong> ${data.transaction_code ?? 'N/A'}</p>
                        <p><strong>User:</strong> ${data.user?.name ?? 'N/A'}</p>
                        <p><strong>Total:</strong> ${number_format(data.total ?? 0, 0, ',', ',')}</p>
                        <p><strong>Payment Method:</strong> ${data.payment_method ?? 'N/A'}</p>
                        <p><strong>Status:</strong> ${data.status ?? 'N/A'}</p>
                        <p><strong>Created At:</strong> ${data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A'}</p>
                        <h5>Detail Transactions:</h5>
                        <ul>`;

                    data.detail_transactions?.forEach(function(detail) {
                        details +=
                            `<li>${detail.product?.name ?? 'N/A'}</li>`;
                    });

                    details += `</ul>`;

                    $('#transaction-details-content').html(details);
                    $('#transactionDetailsModal').modal('show');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        function showUpdateStatusModal(transactionCode, currentStatus) {
            $('#update-status-form').attr('action', '{{ url('transaction') }}/' + transactionCode + '/update-status');
            $('#status').val(currentStatus);
            $('#updateStatusModal').modal('show');
        }

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + (Math.round(n * k) / k).toFixed(prec);
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
    </script>
@endsection
