@extends('admin.layouts.app')
@section('page_title', 'Edit Expense')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.expense.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Expense</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.expense.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Expense Name <span class="text-danger">*</span></label>
                    <input type="text" name="expense_name" class="form-control @error('expense_name') is-invalid @enderror" placeholder="e.g. Travel, Office Supplies" value="{{ old('expense_name', $expense->expense_name) }}">
                    @error('expense_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Optional description" value="{{ old('description', $expense->description) }}">
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.expense.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
