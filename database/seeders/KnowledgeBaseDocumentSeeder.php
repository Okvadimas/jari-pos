<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KnowledgeBaseDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "id" => 1,
                "company_id" => 1,
                "filename" => "File Knowledge Base - SOP (Standar Operasional Prosedur).txt",
                "file_path" => "knowledge_base/1/CD004DjD7iC9R7KsEBoZc92hw8yuIlzHzKf9XQ5N.txt",
                "type" => "txt",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 04:48:39",
                "updated_at" => "2026-03-10 04:48:58"
            ],
            [
                "id" => 2,
                "company_id" => 1,
                "filename" => "File Knowledge Base - Recipe.txt",
                "file_path" => "knowledge_base/1/7ULoCdYkuy4rJWsrLNs7s8zHCqH0Dnls3q5Ck3XK.txt",
                "type" => "txt",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 04:50:20",
                "updated_at" => "2026-03-10 04:50:25"
            ],
            [
                "id" => 3,
                "company_id" => 1,
                "filename" => "KNOWLEDGE BASE - RECIPE (2).txt",
                "file_path" => "knowledge_base/1/oRfFJg6VCmbNGqTdNZh3vzWcX4woQiEnmpNcIFaX.txt",
                "type" => "txt",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 07:14:02",
                "updated_at" => "2026-03-10 07:14:12"
            ],
            [
                "id" => 8,
                "company_id" => 1,
                "filename" => "BAB II.pdf",
                "file_path" => "knowledge_base/1/zJ0bN4AAbJ22TRiEN8ZPAUOFTHfKfpDsn1HAuGqo.pdf",
                "type" => "pdf",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 15:09:22",
                "updated_at" => "2026-03-10 15:09:26"
            ],
            [
                "id" => 9,
                "company_id" => 1,
                "filename" => "BAB III.pdf",
                "file_path" => "knowledge_base/1/PXLSEx2zYXDBjBkiW6zP4Ozboh2lod2vGMq9vX6z.pdf",
                "type" => "pdf",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 15:09:31",
                "updated_at" => "2026-03-10 15:09:34"
            ],
            [
                "id" => 10,
                "company_id" => 1,
                "filename" => "BAB IV.pdf",
                "file_path" => "knowledge_base/1/5PyRHBuWLmIML9f97zcBo7iK9uaQjeh0RnM9GlWH.pdf",
                "type" => "pdf",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 15:09:34",
                "updated_at" => "2026-03-10 15:09:40"
            ],
            [
                "id" => 11,
                "company_id" => 1,
                "filename" => "BAB V.pdf",
                "file_path" => "knowledge_base/1/oeVECBoIO3z9h5tJdP0NLyVXiAzaofsTCQ6r55br.pdf",
                "type" => "pdf",
                "status" => "ready",
                "error_message" => null,
                "created_at" => "2026-03-10 15:09:38",
                "updated_at" => "2026-03-10 15:09:43"
            ]
        ];

        \Illuminate\Support\Facades\DB::table('knowledge_base_documents')->insert($data);
    }
}
