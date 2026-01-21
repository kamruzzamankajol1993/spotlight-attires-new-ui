@extends('front.master.master')

@section('title', 'Point History')

@section('body')
<main>
    <section class="section">
        <div class="container py-4">
            <div class="spotlight_user_profile_container">
                <div class="spotlight_user_profile_breadcrumb">
                    <a href="{{ route('home.index') }}">Home</a> > 
                    <a href="{{ route('dashboard.user') }}">Account</a> > 
                    Point History
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4 mb-4">
                        @include('front.include.dashboardSidebar')
                    </div>

                    <div class="col-lg-9 col-md-8">
                        <div class="spotlight_user_profile_main-content">
                            <div class="spotlight_user_profile_main-header mb-4">
                                <h4>My Point History</h4>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Description / Order ID</th>
                                            <th>Type</th>
                                            <th class="text-end">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pointLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at->format('d M, Y h:i A') }}</td>
                                                <td>
                                                    @if($log->order_id)
                                                        Order #{{ optional($log->order)->invoice_no ?? $log->order_id }}
                                                    @else
                                                        {{ $log->meta ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log->type == 'earned')
                                                        <span class="badge bg-success">Earned</span>
                                                    @elseif($log->type == 'redeemed')
                                                        <span class="badge bg-danger">Redeemed</span>
                                                    @elseif($log->type == 'refunded')
                                                        <span class="badge bg-info text-dark">Refunded</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($log->type) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold {{ $log->type == 'earned' || $log->type == 'refunded' ? 'text-success' : 'text-danger' }}">
                                                    {{ $log->type == 'earned' || $log->type == 'refunded' ? '+' : '-' }}{{ $log->points }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    No point history found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $pointLogs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection