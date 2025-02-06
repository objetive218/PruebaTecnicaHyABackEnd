<?php

namespace App\Http\Controllers;

use App\Models\ExcelData;
use App\Models\PostDocument;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends Controller
{
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
}
