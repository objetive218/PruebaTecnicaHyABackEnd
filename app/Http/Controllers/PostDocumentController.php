<?php

namespace App\Http\Controllers;

use App\Models\PostDocument;
use App\Http\Requests\UpdatePostDocumentRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PostDocumentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostDocument::with('user')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);
        $postDocument = $request->user()->postDocuments()->create($fields);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Título');
        $sheet->setCellValue('B1', 'Cuerpo');
        $sheet->setCellValue('A2', $fields['title']);
        $sheet->setCellValue('B2', $fields['body']);

        $fileName = 'document_' . $postDocument->id . '.xlsx';
        $filePath = storage_path('app/public/documents/' . $fileName);

        if (!file_exists(storage_path('app/public/documents'))) {
            mkdir(storage_path('app/public/documents'), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $postDocument->update(['file_path' => 'documents/' . $fileName]);

        return ['post' => $postDocument,  'user' => $postDocument-> user];
        //return response()->json($postDocument);
    }

   public function import(Request $request)
    {
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls', 
        ]);

        
        $file = $request->file('file');

       
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        
        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

          
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

           
            PostDocument::create([
                'title' => $data[0], 
                'body' => $data[1],   
            ]);
        }

        return response()->json([
            'message' => 'Archivo importado correctamente.',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostDocument $postDocument)
    {
        return  $postDocument;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, PostDocument $postDocument)
    {
        Gate::authorize('modify', $postDocument);
        
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);
        $postDocument->update($fields);

         $existingFilePath = storage_path('app/public/' . $postDocument->file_path);
        if (file_exists($existingFilePath)) {
            unlink($existingFilePath); 
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Título');
        $sheet->setCellValue('B1', 'Cuerpo');
        $sheet->setCellValue('A2', $fields['title']);
        $sheet->setCellValue('B2', $fields['body']);

        $fileName = 'document_' . $postDocument->id . '.xlsx';
        $filePath = storage_path('app/public/documents/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $postDocument->update(['file_path' => 'documents/' . $fileName]);

        return  response()->json($postDocument);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostDocument $postDocument)
    {
        Gate::authorize('modify', $postDocument);

        $filepath= storage_path('app/public/'. $postDocument->file_path);
        if(file_exists($filepath)){
            unlink($filepath);
        }

        $postDocument->delete();
        return ['message' => "El archivo fue borrado"];
    }
}
