<?php

namespace App\Model;

interface AttachmentInterface {
    public function setFilePath(?string $filePath): static;
    public function setFileSize(?int $fileSize): static;
    public function setContentUrl(?string $contentUrl): static;
    public function getFilePath(): ?string;
    public function getFileSize(): ?int;
    public function getContentUrl(): ?string;
}