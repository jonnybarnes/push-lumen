<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSubscriptionsLease extends Command
{
    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'subscriptions:checklease';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Check the subscriptions still have valid leases.';

    /**
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        //get all the subscriptions
        $subs = DB::table('subscriptions')->get();

        //get the current time (in the correct TZ)
        date_default_timezone_set('Europe/London');
        $now = time();

        //delete subscriptions with expired leases
        foreach ($subs as $sub) {
            if (($sub->last_checked + env('HUB_LEASE_SECONDS')) < $now) {
                $sub->delete();
            }
        }
    }
}
