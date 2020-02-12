<?php


namespace Cpken\Weather;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defef = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(cofnig('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }//register() end

    public function provides()
    {
        return [Weather::class, 'weather'];
    }//provides() end
}