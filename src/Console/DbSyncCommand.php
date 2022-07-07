<?php

namespace Dcblogdev\DbSync\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DbSyncCommand extends Command
{
    protected $signature   = 'db:production-sync {--test}';
    protected $description = 'Sync production database with local';

    public function handle(): bool
    {
        $inTest = $this->option('test');

        if (!in_array(config('app.env'), ['local', 'staging'])) {
            $this->error('DB sync will only run on local and staging environments');
            return true;
        }


        $useSsh      = config('dbsync.useSsh');
        $sshUsername = config('dbsync.sshUsername');
        $sshPort     = config('dbsync.sshPort');

        $connect = [
            'sync' => [
                'host' => config('dbsync.host'),
                'username' => config('dbsync.username'),
                'database' => config('dbsync.database'),
                'password' => config('dbsync.password'),

            ],
            'local' => [
                'host' => config('database.connections.mysql.host'),
                'username' => config('database.connections.mysql.username'),
                'database' => config('database.connections.mysql.database'),
                'password' => config('database.connections.mysql.password')

            ]
        ];

        $ignore                = config('dbsync.ignore');
        $ignoreTables          = explode(',', $ignore);
        $importSqlFile         = config('dbsync.importSqlFile');
        $removeFileAfterImport = config('dbsync.removeFileAfterImport');

        if (empty($connect['sync']['host']) || empty($connect['sync']['username']) || empty($connect['sync']['password'])) {
            $this->error("DB credentials not set, have you published the config and set ENV variables?");
            return true;
        }

        $sql = base_path('file.sql');

        if ($inTest === false) {

            $ignoreString = null;
            foreach ($ignoreTables as $name) {
                $ignoreString .= " --ignore-table={$connect['sync']['database']}.$name";
            }

            if ($useSsh === true) {
                exec("ssh $sshUsername@{$connect['sync']['host']} -p$sshPort mysqldump -u {$connect['sync']['username']} -p{$connect['sync']['password']} {$connect['sync']['database']} $ignoreString > file.sql", $output);
            } else {
                exec("mysqldump -h{$connect['sync']['host']} -u {$connect['sync']['username']} -p{$connect['sync']['password']} {$connect['sync']['database']} $ignoreString > file.sql", $output);
            }

            if ($importSqlFile === true) {
                exec("mysql --user={$connect['local']['username']} --password={$connect['local']['password']} --host={$connect['local']['host']} --database {$connect['local']['database']} < $sql");
            }

            if ($removeFileAfterImport === true) {
                unlink('file.sql');
            }
        }

        $this->comment("DB Synced");

        return true;
    }
}
