<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    public function index()
    {
        return view('documents');
    }

    public function uploadDocuments(Request $request)
    {
        $documents = $request->file('documents');
        $types = [];
        foreach ($documents as $document) {
            $types[] = [
                'mime_type' => $document->getMimeType(),
                'client_mime_type' => $document->getClientMimeType()
            ];
        }
//        $names = $this->saveFiles($documents);
//
//        $photo = fopen(storage_path('app/public/documents/'). $names[0], 'r');
//
//        $response = Http::attach(
//            'image', $photo, 'photo.jpg'
//        )->post('https://latest.dbrain.io/classify?token=DEMOTOKEN&async=true');
//
//        $task_id = $response->json('task_id');
//
//        $response2 = Http::get(
//            "https://latest.dbrain.io/result/602656947fbcce9031b83a93?token=DEMOTOKEN"
//        )->json();

        return response()->json($types);
        dd($response2);
    }

    private function saveFiles(array $files): array
    {
        $names = [];
        foreach ($files as $file) {
            $name = time() . "_" . $file->getClientOriginalName();
            $names[] = $name;
            Storage::putFileAs(
                'public/documents',
                $file,
                $name
            );
        }
        return $names;
    }
}
