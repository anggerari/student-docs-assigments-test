<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Display a list of all documents.
     */
    public function index()
    {
        $documents = Document::latest()->get();

        // Add a secure, temporary download URL to each document object.
        $documents->each(function ($document) {
            $document->download_url = $document->s3_url
                ? Storage::disk('s3')->temporaryUrl($document->s3_url, now()->addMinutes(10))
                : '#';
        });

        return view('documents.index', compact('documents'));
    }

    /**
     * Store a new document on S3.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('document');
            $folder = 'student-documents';
            $fileName = uniqid() . '-' . $file->getClientOriginalName();
            $filePath = $folder . '/' . $fileName;

            // Read the file's contents and upload it to S3 as a private file.
            $success = Storage::disk('s3')->put(
                $filePath,
                file_get_contents($file->getRealPath())
            );

            if (!$success) {
                Log::error('S3 upload returned false. Check environmental configuration.');
                return back()->with('error', 'Upload to S3 failed. Please check application logs.');
            }

            // Save the file's path (not a public URL) to the database.
            Document::create([
                'student_id' => $request->student_id,
                'filename' => $file->getClientOriginalName(),
                's3_url' => $filePath,
            ]);

            return back()->with('success', 'File uploaded successfully to S3!');

        } catch (\Aws\Exception\AwsException $e) {
            Log::error('AWS S3 UPLOAD EXCEPTION:', ['message' => $e->getMessage(), 'code' => $e->getAwsErrorCode()]);
            return back()->with('error', 'Could not upload to S3. AWS Error: ' . $e->getAwsErrorCode());
        } catch (\Exception $e) {
            Log::error('GENERIC UPLOAD EXCEPTION:', ['message' => $e->getMessage()]);
            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
