<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Fruit;
use App\Models\Child;
use Log;
use Illuminate\Support\Str;

class CheckDataHourlyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $url = 'https://dev.shepherd.appoly.io/fruit.json';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get($url);


            $fruits = $response['menu_items'];
            foreach ($fruits as $fruit) {

                $new_fruit = Fruit::updateOrCreate(
                    ['label' => strtolower($fruit['label']) ],
                    [
                        'label' => strtolower($fruit['label']),
                        'slug' => Str::slug($fruit['label']),
                        'meta' => json_encode($fruit['children']) ?? null,
                    ]
                );

                if (count($fruit['children']) > 0) {
                    $this->handleData($fruit, 0, $new_fruit->id, $new_fruit->id);
                }
            }

        }catch (\Exception $e) {
            Log::error('Error fetching feed data: ' . $e->getMessage());
        }
    }

    private function handleData($fruit, $step, $fruit_id, $parent_id = null){

        if (count($fruit['children']) > 0) {
            $next = $step + 1;
            foreach ($fruit['children'] as $child) {
                $new_child = Child::updateOrCreate(
                    ['label' => strtolower($child['label']) ],
                    [
                    'label' => strtolower($child['label']),
                    'slug' => Str::slug($child['label']),
                    'parent_id' => $parent_id,
                    'index' => $next,
                    'fruit_id' => $fruit_id,
                    'meta' => json_encode($child['children']) ?? null,
                ]);

                if (is_array($child['children'])) {
                    if (count($child['children']) > 0) {
                        $this->handleData($child, $next, $fruit_id, $new_child->id);
                    }
                }
            }
            return;
        }else{
            return;
        }
    }
}
