@extends('adminlte::page')

@section('title', $section->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-folder-open text-primary"></i> {{ $section->name }}
        </h1>
        <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Sections') }}
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Section Details') }}</h3>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3"><i class="fa fa-code text-muted"></i> {{ __('Code') }}</dt>
                <dd class="col-sm-9">{{ $section->code ?? '-' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-link text-muted"></i> {{ __('Slug') }}</dt>
                <dd class="col-sm-9">{{ $section->slug }}</dd>

                <dt class="col-sm-3"><i class="fa fa-sitemap text-muted"></i> {{ __('Parent') }}</dt>
                <dd class="col-sm-9">{{ $section->parent?->name ?? '-' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-sort-numeric-down text-muted"></i> {{ __('Sort Order') }}</dt>
                <dd class="col-sm-9">{{ $section->sort_order }}</dd>

                <dt class="col-sm-3"><i class="fa fa-toggle-on text-muted"></i> {{ __('Status') }}</dt>
                <dd class="col-sm-9">
                    @if($section->is_active)
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> {{ __('Active') }}
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            <i class="fa fa-ban"></i> {{ __('Inactive') }}
                        </span>
                    @endif
                </dd>

                <dt class="col-sm-3"><i class="fa fa-align-left text-muted"></i> {{ __('Description') }}</dt>
                <dd class="col-sm-9">{{ $section->description ?? '-' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-sitemap text-muted"></i> {{ __('Children') }}</dt>
                <dd class="col-sm-9">
                    @forelse($section->children as $child)
                        <a href="{{ route('admin.sections.show', $child) }}" class="badge badge-info mr-1">
                            <i class="fa fa-folder"></i> {{ $child->name }}
                        </a>
                    @empty
                        <span class="text-muted">{{ __('No child sections.') }}</span>
                    @endforelse
                </dd>
            </dl>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-primary custom-btn">
                <i class="fa fa-pen mr-1"></i> {{ __('Edit') }}
            </a>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </div>
@stop
