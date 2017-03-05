<?php

namespace App\Exceptions;


class UserFriendlyException extends \Exception {
    protected $description;
    
    public function setDescription(string $description) {
        $this->description = $description;
    }
    
    public function getDescription() {
        return $this->description ?? false;
    }
}