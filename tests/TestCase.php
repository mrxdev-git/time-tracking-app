<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $config = $app->make(Repository::class);
        $database = $config->get('database.connections.sqlite.database');
        $url = $config->get('database.connections.sqlite.url');

        if ($database !== ':memory:') {
            throw new RuntimeException("Tests require the in-memory SQLite database, got [$database].");
        }

        if (filled($url)) {
            throw new RuntimeException("Tests require the in-memory SQLite database, got url [$url].");
        }

        if (filled($config->get('app.key'))) {
            return $app;
        }

        $key = base64_encode(Encrypter::generateKey($config->get('app.cipher')));
        $config->set('app.key', "base64:$key");

        return $app;
    }
}
