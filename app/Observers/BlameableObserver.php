<?php

namespace App\Observers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BlameableObserver
{
    public function creating(Model $model)
    {
        if (Auth::user()) {
            $model->created_by = Auth::user()->usr_nik;
            $model->updated_by = Auth::user()->usr_nik;
        }
    }

    public function updating(Model $model)
    {
        if (Auth::user()) {
            $model->updated_by = Auth::user()->usr_nik;
        }
    }
}
