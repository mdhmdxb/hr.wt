@extends('core::layouts.app')

@section('title', 'Edit Company')
@section('heading', 'Edit Company')

@section('content')
<form method="POST" action="{{ route('company.update', $company) }}" enctype="multipart/form-data" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company name</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Logo</label>
            @if($company->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo))
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Current: <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo) }}" alt="" class="inline h-6"> (upload new to replace)</p>
            @endif
            <input type="file" name="logo" accept="image/*"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('address', $company->address) }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Update Company</button>
            <a href="{{ route('company.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300">Cancel</a>
        </div>
    </div>
</form>
@endsection
