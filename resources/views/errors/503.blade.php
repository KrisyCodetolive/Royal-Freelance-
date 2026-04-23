@extends('errors::minimal')

@section('title', __('Service Indisponible'))
@section('code', '503')
@section('message', __('Maintenance en cours'))
@section('description', 'Royal LeadPro effectue actuellement une maintenance technique. Veuillez réessayer dans quelques instants.')