<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;
use Jenssegers\Date\Date;

use App\Models\Config;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // CONFIGURACIÓN de la empresa
        if (!app()->runningInConsole())
        {
            Config::loadConfig();
        }


        // DEFAULT LOCALE for Date
        Date::setLocale('es');


        /**
         * Boostrap FORMs custom components
         * by DM
         */

        /**
         * HORIZONTAL FORMS
         */

        // FORM - OPEN
        Form::component('fhOpen', 'components.form.horizontal.formOpen', [
            'route',
            'title',
            'model' => null,
            'files' => false,
            'boxClass' => 'primary'
        ]);

        // FORM - CLOSE
        Form::component('fhClose', 'components.form.horizontal.formClose', [
            'submitBtn',
            'cancelBtnRoute' => null
        ]);

        // TEXT
        Form::component('fhText', 'components.form.horizontal.text', [
            'name',
            'title' => null,
            'placeholder' => '',
            'required' => false,
            'width' => '10',
            'value' => null,
            'attributes' => []
        ]);

        // TEXT ÁREA
        Form::component('fhTextarea', 'components.form.horizontal.textarea', [
            'name',
            'title' => null,
            'placeholder' => '',
            'required' => false,
            'width' => '10',
            'value' => null,
            'attributes' => []
        ]);

        // EMAIL
        Form::component('fhEmail', 'components.form.horizontal.email', [
            'name',
            'title' => null,
            'required' => false,
            'value' => null,
            'attributes' => []
        ]);

        // PASSWORD
        Form::component('fhPass', 'components.form.horizontal.password', [
            'name',
            'title' => null,
            'required' => true,
            'attributes' => []
        ]);

        // DATE
        Form::component('fhDate', 'components.form.horizontal.date', [
            'name',
            'title' => null,
            'required' => false,
            'value' => null,
            'attributes' => []
        ]);

        // NUMBER
        Form::component('fhNumber', 'components.form.horizontal.number', [
            'name',
            'title' => null,
            'required' => false,
            'value' => null,
            'attributes' => []
        ]);

        // SELECT
        Form::component('fhSelect', 'components.form.horizontal.select', [
            'name',
            'array' => [],
            'selected' => null,
            'title' => null,
            'placeholder' => '',
            'required' => false,
            'attributes' => [],
            'width' => '4'
        ]);

        // CHECKBOX
        Form::component('fhCheck', 'components.form.horizontal.check', [
            'name',
            'title' => null,
            'required' => false,
            'selected' => false,
            'attributes' => []
        ]);

        // FILE
        Form::component('fhFile', 'components.form.horizontal.file', [
            'name',
            'title' => null,
            'placeholder' => '',
            'required' => false,
            'width' => '4',
            'attributes' => []
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
