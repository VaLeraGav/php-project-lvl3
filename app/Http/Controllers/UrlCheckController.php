<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlCheckController extends Controller
{
    public function store(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $url = DB::table('urls')->find($id);

            abort_unless($url, 404);
            $response = Http::get($url->name);

            $document = new Document($response->body());
            $h1 = optional($document->first('h1'))->text();
            $title = optional($document->first('title'))->text();
            $description = optional($document->first('meta[name=description]'))->getAttribute('content');

            $check = [
                'url_id' => $id,
                'status_code' => $response->status(),
                'h1' => $h1,
                'title' => $title,
                'description' => $description,
                'created_at' => Carbon::now('Europe/Moscow'),
            ];

            DB::table('url_checks')->insert($check);

            flash('Страница успешно проверена')->warning();
        } catch (HttpClientException | RequestException $e) {
            flash($e->getMessage())->error();
        }
        return redirect()->route('urls.show', $id);
    }
}
