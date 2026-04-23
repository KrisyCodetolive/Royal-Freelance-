@extends('errors::minimal')

@section('title', __('Erreur Interne'))
@section('code', '500')
@section('message', __('Erreur Interne du Serveur'))
@section('description', 'Une erreur inattendue avec nos systèmes est survenue. Notre équipe a été alertée et travaille déjà à la résoudre !')