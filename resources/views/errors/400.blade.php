@extends('errors::layout')

@section('error-message', $exception->getMessage())

