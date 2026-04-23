@extends('errors::minimal')

@section('title', __('Session Expirée'))
@section('code', '419')
@section('message', __('Session Expirée'))
@section('description', 'Votre session a expiré pour des raisons de sécurité. Veuillez rafraîchir la page et vous reconnecter.')
