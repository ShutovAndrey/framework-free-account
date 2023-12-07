<?php

declare(strict_types=1);

namespace App\DB;

use App\Models\{GoodsStore, User, Good};
use Composer\Script\Event;
use Illuminate\Database\Capsule\Manager as Capsule;

class MigrationAction
{
    public static function up(Event $event)
    {
        if (empty($event->getArguments())) {
            $test = false;
        } elseif (isset($event->getArguments()[0]) && 'test' == $event->getArguments()[0]) {
            $test = true;
        }

        $eloquent = self::getDB($test);
        $eloquent::schema()->dropAllTables();

        $eloquent::schema()->create('users', static function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('password');
            $table->string('bank_account');
            $table->integer('points')->unsigned()->default(0);
            $table->float('rate')->default(0.8);
            $table->timestamps();
        });

        $eloquent::schema()->create('goods', static function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('gift');
            $table->timestamps();
        });

        $eloquent::schema()->create('goods_store', static function ($table) {
            $table->increments('id');
            $table->integer('good_id')->unsigned();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('quantity');
            $table->boolean('is_gift')->default(true);
            $table->timestamps();
        });

        $eloquent::schema()->create('goods_delivery', static function ($table) {
            $table->increments('id');
            $table->integer('good_id')->unsigned();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('address');
            $table->smallInteger('status');
            $table->integer('quantity')->unsigned()->default(1);
            $table->timestamps();
        });

        $eloquent::schema()->create('users_gifts', static function ($table) {
            $table->increments('id');
            $table->smallInteger('type');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('good_id')->unsigned()->nullable();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('points')->unsigned()->nullable();
            $table->integer('amount')->unsigned()->nullable();
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        $eloquent::schema()->create('transactions', static function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('amount')->unsigned();
            $table->string('uuid', 36);
            $table->timestamps();
        });

        // for flexible admin configuration
        $eloquent::schema()->create('settings', static function ($table) {
            $table->integer('points_max')->unsigned();
            $table->integer('cache_max')->unsigned();
            $table->integer('cache_fund')->unsigned();
            $table->timestamps();
        });

        echo 'Table created successfully!' . PHP_EOL;

        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->name = "user$i";
            $user->email = "user$i@user.com";
            $user->address = "street $i";
            $user->bank_account = "{$i}000123456789000{$i}";
            $user->password = \password_hash("qwerty$i", PASSWORD_BCRYPT);
            $user->save();
        }

        $goods = ['book', 'shoes', 'laptop', 'car', 'flat'];
        foreach ($goods as $i => $gift) {
            $good = new Good();
            $good->name = $gift;
            $good->gift = 'flat' == $gift ? false : true;
            $good->save();

            $store = new GoodsStore();
            $store->good_id = ++$i;
            $store->quantity = $i + 10;
            $store->save();
        }

        $settings = $eloquent::table('settings');
        $settings->insert([
            'points_max' => 10000,
            'cache_max' => 1000,
            'cache_fund' => 5000,
        ]);
    }

    public static function getDB(bool $test = false)
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(\dirname(__DIR__) . '/..');
        $dotenv->load();

        $eloquent = new Capsule();
        $eloquent->addConnection([
            'driver' => 'mysql',
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
            'database' => $test ? \getenv('DB_TEST_NAME') : \getenv('DB_NAME'),
            'username' => \getenv('DB_USER'),
            'password' => \getenv('DB_PASS'),
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',
        ]);
        $eloquent->setAsGlobal();
        $eloquent->bootEloquent();
        $eloquent->setFetchMode(\PDO::FETCH_ASSOC);

        return $eloquent;
    }
}
