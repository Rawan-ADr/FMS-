<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserReportRepository;
use App\Repositories\UserReportRepositoryInterface;
use App\Repositories\FileReportRepository;
use App\Repositories\FileReportRepositoryInterface;
use App\Repositories\GroupRepository;
use App\Repositories\GroupRepositoryInterface;
use App\Repositories\UserGroupRepository;
use App\Repositories\UserGroupRepositoryInterface;
use App\Repositories\FileRepository;
use App\Repositories\FileRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(UserGroupRepositoryInterface::class, UserGroupRepository::class);
        $this->app->bind(FileRepositoryInterface::class,FileRepository::class);
        $this->app->bind(UserReportRepositoryInterface::class,UserReportRepository::class);
        $this->app->bind(FileReportRepositoryInterface::class,FileReportRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
