<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_documents_with_temporary_urls()
    {
        Document::factory()->count(3)->create([
            's3_url' => 'student-documents/fake-file.pdf'
        ]);

        $expectedTemporaryUrl = 'https://some-temporary-url.com';

        Storage::shouldReceive('disk')->with('s3')->andReturn(
            Mockery::mock(['temporaryUrl' => $expectedTemporaryUrl])
        );

        $response = $this->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.index');
        $response->assertViewHas('documents', function ($documents) use ($expectedTemporaryUrl) {
            return $documents->count() === 3 &&
                $documents->every(fn($doc) => $doc->download_url === $expectedTemporaryUrl);
        });
    }

    public function test_store_uploads_file_to_s3_and_creates_document_record()
    {
        Storage::fake('s3');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $studentId = 'student-123';

        $response = $this->post(route('documents.store'), [
            'student_id' => $studentId,
            'document' => $file,
        ]);

        $document = Document::first();
        Storage::disk('s3')->assertExists($document->s3_url);

        $this->assertDatabaseHas('documents', [
            'student_id' => $studentId,
            'filename' => 'document.pdf',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File uploaded successfully to S3!');
    }
}
