<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Responses;

interface Response
{
    public function send(): void;
}
