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
        $names = $this->saveFiles($documents);

        $uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            storage_path('app/public/documents/') . $names[0],
            '123.jpeg'
        );

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data'
        ])->post('https://latest.dbrain.io/classify?token=DEMOTOKEN&async=true',
        [
            [
                'image' => $request->file('documents')[0]
            ]
        ]);


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
