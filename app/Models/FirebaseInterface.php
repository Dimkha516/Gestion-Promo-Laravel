<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class FirebaseInterface extends Model
// {
//     use HasFactory;
// }

interface FirebaseInterface{
    public function createInFirebase(array $data);
    public function updateInFirebase(string $id, array $data);
    public function deleteFromFirebase(string $id);
    public function findInFirebase(string $id);
    public function getAllFromFirebase(string $data);
}