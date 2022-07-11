<?php

namespace Udhuong\LaravelUploadFile\UrlGenerators;

interface TemporaryUrlGeneratorInterface extends UrlGeneratorInterface
{
    public function getTemporaryUrl(\DateTimeInterface $expiry): string;
}
