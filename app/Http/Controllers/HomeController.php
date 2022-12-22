<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Fruit;
use App\Models\Child;
use Log;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(){

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

            $fruits = Fruit::with(['children.children'])->orderBy('label')->get();

            return view('welcome')->with([
                'fruits' => $fruits,
                'count' => count($fruits),
                'error' => false,
            ]);

        }catch (\Exception $e) {
            return view('welcome')->with([
                'error' => true,
            ]);
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

    public function editItem(Request $request){
        $validator = $this->validate($request, [
            'label' => 'required|string',
            'slug' => 'required|string'
        ]);

        $item = Fruit::where('slug', $validator['slug'])->first();
        if ($item) {
            $item->label = $validator['label'];
            $item->slug = Str::slug($validator['label']);
            $item->save();
        }

        $item = Child::where('slug', $validator['slug'])->first();
        if ($item) {
            $item->label = $validator['label'];
            $item->slug = Str::slug($validator['label']);
            $item->save();
        }
        return redirect()->intended('/')->withSuccess('Success');
    }

    public function viewItem($slug){

        $item = Fruit::where('slug', $slug)->first();
        if ($item) {

            return view('editItem')->with([
                'item' => $item,
                'error' => false,
            ]);
        }

        $item = Child::where('slug', $slug)->first();
        if ($item) {

            return view('editItem')->with([
                'item' => $item,
                'error' => false,
            ]);
        }
    }


}
