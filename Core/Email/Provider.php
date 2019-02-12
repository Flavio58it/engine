<?php
/**
 * Minds Email Provider
 */

namespace Minds\Core\Email;

use Minds\Core\Di\Provider as DiProvider;

class Provider extends DiProvider
{
    public function register()
    {
        $this->di->bind('Mailer', function ($di) {
            return new Mailer(new \PHPMailer());
        }, ['useFactory'=>true]);
        $this->di->bind('Email\SpamFilter', function ($di) {
            return new SpamFilter();
        }, ['useFactory'=>true]);

        $this->di->bind('Email\Manager', function ($di) {
            return new Manager();
        }, ['useFactory' => true]);

        $this->di->bind('Email\Repository', function ($di) {
            return new Repository();
        }, ['useFactory' => true]);

        $this->di->bind('Email\Verify\Manager', function ($di) {
            return new Verify\Manager;
        }, ['useFactory' => true]);
    }
}