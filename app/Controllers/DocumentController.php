<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;

class DocumentController extends BaseController
{
    protected $documentModel;

    public function __construct() {
        $this->documentModel = new DocumentModel();
    }

    public function index()
    {
        return view('DocumentForm');
    }

    public function save() {
        if ($this->request->getMethod() == 'post') {
            $documentFiles = $this->request->getFiles('documentsPdfFiles');
            
            // Check if files are uploaded and iterate over them
            if ($documentFiles) {
                // Handle the case where multiple files are uploaded under the same name
                if (is_array($documentFiles)) {
                    foreach ($documentFiles as $file) {
                        if (is_array($file)) {
                            // Handle multiple files with the same input name
                            foreach ($file as $f) {
                                $this->processFile($f);
                            }
                        } else {
                            // Handle single file with the input name
                            $this->processFile($file);
                        }
                    }
                } else {
                    // Handle a single file
                    $this->processFile($documentFiles);
                }
            } else {
                echo "No files were uploaded.";
            }
        }
    }

    private function processFile(UploadedFile $file)
    {
        if ($file->isValid() && !$file->hasMoved()) {
            // Get the file contents
            $documentData = file_get_contents($file->getTempName());

            // Save the file contents to the database
            $this->documentModel->save([
                'document_data' => $documentData,
                'document_name' => $file->getClientName(),
            'member_id' => (int) '4',
            ]);

            // Optionally, you can print the file name
            $fileName = $file->getClientName();
            var_dump($fileName);
        } else {
            echo "Something went wrong with file: " . $file->getClientName();
        }
    }

    public function showPDF($id) {
        $document = $this->documentModel->find($id);
    
        if (!$document) {
            return 'Document not found';
        }
    
        $pdfData = $document['document_data'];
        $fileName = $document['document_name'];
    
        // Set the content type header to PDF
        header('Content-Type: application/pdf');
    
        // Set the content disposition header to inline to display PDF in browser
        header('Content-Disposition: inline; filename="' . $fileName . '"');
    
        // Prevent caching
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
        // Send the PDF data as the response body
        echo $pdfData;
    
        // Terminate script execution to prevent any further output
        exit();
    }
    

}
